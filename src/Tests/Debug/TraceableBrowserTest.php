<?php

namespace Pbweb\BuzzBundle\Tests\Debug;

use Buzz\Client\ClientInterface;
use Buzz\Message\Request;
use Buzz\Message\Response;
use Pbweb\BuzzBundle\Debug\TraceableBrowser;
use Pbweb\BuzzBundle\Logger\LoggerInterface;

/**
 * Class TraceableBrowserTest
 *
 * @copyright 2015 PB Web Media B.V.
 */
class TraceableBrowserTest extends \PHPUnit_Framework_TestCase
{
    /** @var TraceableBrowser */
    private $browser;

    /** @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface */
    private $logger;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ClientInterface */
    private $client;

    protected function setUp()
    {
        $this->logger = $this->createMockLogger();
        $this->client = $this->createMockClient();
        $this->browser = new TraceableBrowser($this->logger, $this->client);
    }

    public function test()
    {
        $request = $this->createMockRequest();
        $response = $this->createMockResponse();

        $this->logger->expects($this->at(0))
            ->method('start')
            ->with($request);

        $this->logger->expects($this->at(1))
            ->method('stop')
            ->with($response);

        $this->client->expects($this->once())
            ->method('send')
            ->with($request, $response);

        $result = $this->browser->send($request, $response);

        $this->assertSame($response, $result);
    }

    public function testException()
    {
        $request = $this->createMockRequest();
        $response = $this->createMockResponse();
        $exception = new \RuntimeException('Fail!');

        $this->logger->expects($this->at(0))
            ->method('start')
            ->with($request);

        $this->logger->expects($this->at(1))
            ->method('fail')
            ->with($exception);

        $this->client->expects($this->once())
            ->method('send')
            ->with($request, $response)
            ->willThrowException($exception);

        $this->setExpectedException(get_class($exception), $exception->getMessage());

        $this->browser->send($request, $response);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private function createMockLogger()
    {
        return $this->getMock(LoggerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ClientInterface
     */
    private function createMockClient()
    {
        return $this->getMock(ClientInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Request
     */
    private function createMockRequest()
    {
        return $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Response
     */
    private function createMockResponse()
    {
        return $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
