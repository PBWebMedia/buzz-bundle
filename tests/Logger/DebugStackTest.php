<?php

namespace Pbweb\BuzzBundle\Logger;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @copyright 2015 PB Web Media B.V.
 */
class DebugStackTest extends MockeryTestCase
{
    /** @var DebugStack */
    private $stack;
    /** @var Mock|Stopwatch */
    private $stopwatch;
    /** @var Mock|RequestInterface */
    private $requestA;
    /** @var Mock|RequestInterface */
    private $requestB;
    /** @var Mock|ResponseInterface */
    private $response;

    protected function setUp(): void
    {
        $this->stopwatch = \Mockery::mock(Stopwatch::class);
        $this->stopwatch->shouldIgnoreMissing();
        $this->stack = new DebugStack($this->stopwatch);

        $this->requestA = \Mockery::mock(RequestInterface::class);
        $this->requestA->shouldReceive('getBody')->andReturn('body request a')->byDefault();
        $this->requestB = \Mockery::mock(RequestInterface::class);
        $this->requestB->shouldReceive('getBody')->andReturn('body request b')->byDefault();
        $this->response = \Mockery::mock(ResponseInterface::class);
        $this->response->shouldReceive('getBody')->andReturn('body response')->byDefault();
    }

    public function test()
    {
        $this->stack->startRequest($this->requestA);
        $this->stack->stopRequest($this->response);

        $exception = new \Exception('Failed');

        $this->stack->startRequest($this->requestB);
        $this->stack->fail($exception);

        $result = $this->stack->getStack();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $this->assertSame($this->requestA, $result[1]['request']);
        $this->assertSame('body request a', $result[1]['request_body']);
        $this->assertSame($this->response, $result[1]['response']);
        $this->assertSame('body response', $result[1]['response_body']);
        $this->assertNull($result[1]['exception']);
        $this->assertIsFloat($result[1]['executionTime']);

        $this->assertSame($this->requestB, $result[2]['request']);
        $this->assertSame('body request b', $result[2]['request_body']);
        $this->assertNull($result[2]['response']);
        $this->assertSame((string) $exception, $result[2]['exception']);
        $this->assertIsFloat($result[2]['executionTime']);
    }
}
