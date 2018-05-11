<?php

namespace Pbweb\BuzzBundle\DataCollector;

use Mockery\Mock;
use Pbweb\BuzzBundle\Logger\DebugStack;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @copyright 2015 PB Web Media B.V.
 */
class BuzzDataCollectorTest extends TestCase
{
    /** @var BuzzDataCollector */
    private $collector;
    /** @var Mock|DebugStack */
    private $logger;
    /** @var Mock|Request */
    private $request;
    /** @var Mock|Response */
    private $response;

    protected function setUp()
    {
        $this->logger = \Mockery::mock(DebugStack::class);
        $this->collector = new BuzzDataCollector($this->logger);

        $this->request = \Mockery::mock(Request::class);
        $this->response = \Mockery::mock(Response::class);
    }

    public function test()
    {
        $stack = [
            ['request' => null, 'response' => $this->createMockPsr7Response(200), 'exception' => null, 'executionTime' => 0.0001],
            ['request' => null, 'response' => $this->createMockPsr7Response(200), 'exception' => null, 'executionTime' => 0.0200],
            ['request' => null, 'response' => $this->createMockPsr7Response(200), 'exception' => null, 'executionTime' => 1.0030],
        ];

        $this->logger->shouldReceive('getStack')
            ->andReturn($stack);

        $this->collector->collect($this->request, $this->response);

        $this->assertSame(3, $this->collector->getRequestCount());
        $this->assertSame(1.0231, $this->collector->getTime());
        $this->assertSame($stack, $this->collector->getRequestList());
        $this->assertFalse($this->collector->hasError());
        $this->assertSame('buzz', $this->collector->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResponseInterface
     */
    private function createMockPsr7Response(int $statusCode)
    {
        /** @var Mock|ResponseInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')->andReturn($statusCode)->byDefault();

        return $response;
    }
}
