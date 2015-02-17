<?php

namespace Pbweb\BuzzBundle\Tests\Logger;

use Buzz\Message\Request;
use Buzz\Message\Response;
use Pbweb\BuzzBundle\Logger\DebugStack;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class DebugStackTest
 *
 * @copyright 2015 PB Web Media B.V.
 */
class DebugStackTest extends \PHPUnit_Framework_TestCase
{
    /** @var DebugStack */
    private $stack;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Stopwatch */
    private $stopwatch;

    protected function setUp()
    {
        $this->stopwatch = $this->createMockStopwatch();
        $this->stack = new DebugStack($this->stopwatch);
    }

    public function test()
    {
        $requestA = $this->createMockRequest();
        $responseA = $this->createMockResponse();

        $this->stack->start($requestA);
        $this->stack->stop($responseA);

        $requestB = $this->createMockRequest();
        $exceptionB = new \Exception('Failed');

        $this->stack->start($requestB);
        $this->stack->fail($exceptionB);

        $result = $this->stack->getStack();

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->assertSame($requestA, $result[1]['request']);
        $this->assertSame($responseA, $result[1]['response']);
        $this->assertNull($result[1]['exception']);
        $this->assertInternalType('float', $result[1]['executionTime']);

        $this->assertSame($requestB, $result[2]['request']);
        $this->assertNull($result[2]['response']);
        $this->assertSame((string) $exceptionB, $result[2]['exception']);
        $this->assertInternalType('float', $result[2]['executionTime']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Stopwatch
     */
    private function createMockStopwatch()
    {
        return $this->getMock(Stopwatch::class);
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
