<?php

namespace Pbweb\BuzzBundle\DataCollector;

use Pbweb\BuzzBundle\Logger\DebugStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Buzz\Message\Response as BuzzResponse;

/**
 * Class BuzzDataCollectorTest
 *
 * @copyright 2015 PB Web Media B.V.
 */
class BuzzDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var BuzzDataCollector */
    private $collector;

    /** @var \PHPUnit_Framework_MockObject_MockObject|DebugStack */
    private $logger;

    protected function setUp()
    {
        $this->logger = $this->createMockDebugStack();
        $this->collector = new BuzzDataCollector($this->logger);
    }

    public function test()
    {
        $stack = [
            ['request' => null, 'response' => $this->createMockBuzzResponse(true), 'exception' => null, 'executionTime' => 0.0001],
            ['request' => null, 'response' => $this->createMockBuzzResponse(true), 'exception' => null, 'executionTime' => 0.0200],
            ['request' => null, 'response' => $this->createMockBuzzResponse(true), 'exception' => null, 'executionTime' => 1.0030],
        ];

        $this->logger->expects($this->once())
            ->method('getStack')
            ->willReturn($stack);

        $this->collector->collect($this->createMockRequest(), $this->createMockResponse());

        $this->assertSame(3, $this->collector->getRequestCount());
        $this->assertSame(1.0231, $this->collector->getTime());
        $this->assertSame($stack, $this->collector->getRequestList());
        $this->assertFalse($this->collector->hasError());
        $this->assertSame('buzz', $this->collector->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DebugStack
     */
    private function createMockDebugStack()
    {
        return $this->getMockBuilder(DebugStack::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param bool $successful
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|BuzzResponse
     */
    private function createMockBuzzResponse($successful)
    {
        $response = $this->getMockBuilder(BuzzResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response->expects($this->once())
            ->method('isSuccessful')
            ->willReturn($successful);

        return $response;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Request
     */
    private function createMockRequest()
    {
        return $this->createMock(Request::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Response
     */
    private function createMockResponse()
    {
        return $this->createMock(Response::class);
    }
}
