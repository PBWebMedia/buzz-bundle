<?php

namespace Pbweb\BuzzBundle\Debug;

use Buzz\Client\ClientInterface;
use Buzz\Message\Request;
use Buzz\Message\Response;
use Mockery\Mock;
use Pbweb\BuzzBundle\Logger\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @copyright 2015 PB Web Media B.V.
 */
class TraceableBrowserTest extends TestCase
{
    /** @var TraceableBrowser */
    private $browser;
    /** @var Mock|LoggerInterface */
    private $logger;
    /** @var Mock|ClientInterface */
    private $client;
    /** @var Mock|Request */
    private $request;
    /** @var Mock|Response */
    private $response;
    /** @var Mock|RequestInterface */
    private $psr7Request;
    /** @var Mock|ResponseInterface */
    private $psr7Response;

    protected function setUp()
    {
        $this->logger = \Mockery::mock(LoggerInterface::class);
        $this->client = \Mockery::mock(ClientInterface::class);

        $this->browser = new TraceableBrowser($this->logger, $this->client);

        $this->request = \Mockery::mock(Request::class);
        $this->response = \Mockery::mock(Response::class);
        $this->psr7Request = \Mockery::mock(RequestInterface::class);
        $this->psr7Response = \Mockery::mock(ResponseInterface::class);
    }

    public function testSend()
    {
        $this->logger->shouldReceive('start')->with($this->request)->once()->ordered();
        $this->logger->shouldReceive('stop')->with($this->response)->once()->ordered();
        $this->client->shouldReceive('send')->with($this->request, $this->response)->once();

        $result = $this->browser->send($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testSendException()
    {
        $exception = new \RuntimeException('Fail!');

        $this->logger->shouldReceive('start')->with($this->request)->once()->ordered();
        $this->logger->shouldReceive('fail')->with($exception)->once()->ordered();
        $this->client->shouldReceive('send')->with($this->request, $this->response)->once()->andThrow($exception);

        $this->expectException(get_class($exception));
        $this->expectExceptionMessage($exception->getMessage());

        $this->browser->send($this->request, $this->response);
    }

    public function testSendRequest()
    {
        $this->logger->shouldReceive('startRequest')->with($this->psr7Request)->once()->ordered();
        $this->logger->shouldReceive('stopRequest')->with($this->psr7Response)->once()->ordered();
        $this->client->shouldReceive('sendRequest')->with($this->psr7Request)->once()->andReturn($this->psr7Response);

        $result = $this->browser->sendRequest($this->psr7Request);

        $this->assertSame($this->psr7Response, $result);
    }

    public function testSendRequestException()
    {
        $exception = new \RuntimeException('Fail!');

        $this->logger->shouldReceive('startRequest')->with($this->psr7Request)->once()->ordered();
        $this->logger->shouldReceive('fail')->with($exception)->once()->ordered();
        $this->client->shouldReceive('sendRequest')->with($this->psr7Request)->once()->andThrow($exception);

        $this->expectException(get_class($exception));
        $this->expectExceptionMessage($exception->getMessage());

        $this->browser->sendRequest($this->psr7Request);
    }
}
