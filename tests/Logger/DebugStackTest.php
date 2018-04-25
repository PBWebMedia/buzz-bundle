<?php

namespace Pbweb\BuzzBundle\Logger;

use Buzz\Message\Request;
use Buzz\Message\Response;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @copyright 2015 PB Web Media B.V.
 */
class DebugStackTest extends TestCase
{
    /** @var DebugStack */
    private $stack;
    /** @var Mock|Stopwatch */
    private $stopwatch;
    /** @var Mock|Request */
    private $requestA;
    /** @var Mock|Request */
    private $requestB;
    /** @var Mock|Response */
    private $response;

    protected function setUp()
    {
        $this->stopwatch = \Mockery::mock(Stopwatch::class);
        $this->stopwatch->shouldIgnoreMissing();
        $this->stack = new DebugStack($this->stopwatch);

        $this->requestA = \Mockery::mock(Request::class);
        $this->requestB = \Mockery::mock(Request::class);
        $this->response = \Mockery::mock(Response::class);
    }

    public function test()
    {
        $this->stack->start($this->requestA);
        $this->stack->stop($this->response);

        $exception = new \Exception('Failed');

        $this->stack->start($this->requestB);
        $this->stack->fail($exception);

        $result = $this->stack->getStack();

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        $this->assertSame($this->requestA, $result[1]['request']);
        $this->assertSame($this->response, $result[1]['response']);
        $this->assertNull($result[1]['exception']);
        $this->assertInternalType('float', $result[1]['executionTime']);

        $this->assertSame($this->requestB, $result[2]['request']);
        $this->assertNull($result[2]['response']);
        $this->assertSame((string) $exception, $result[2]['exception']);
        $this->assertInternalType('float', $result[2]['executionTime']);
    }
}
