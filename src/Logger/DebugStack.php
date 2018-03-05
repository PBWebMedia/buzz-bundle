<?php

namespace Pbweb\BuzzBundle\Logger;

use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @copyright 2015 PB Web Media B.V.
 */
class DebugStack implements LoggerInterface
{
    /** @var ?Stopwatch */
    private $stopwatch;

    /** @var array */
    private $stack = [];

    /** @var float|null */
    private $start = null;

    /** @var int */
    private $current = 0;

    public function __construct(?Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }

    public function start(RequestInterface $request)
    {
        $this->start = microtime(true);
        $this->stack[++$this->current] = [
            'request' => $request,
            'response' => null,
            'exception' => null,
            'executionTime' => 0,
        ];

        if ($this->stopwatch) {
            $this->stopwatch->start('buzz', 'buzz');
        }
    }

    public function stop(MessageInterface $response)
    {
        $this->stack[$this->current]['response'] = $response;
        $this->doStop();
    }

    public function fail(\Exception $exception)
    {
        $this->stack[$this->current]['exception'] = (string) $exception;
        $this->doStop();
    }

    public function clear()
    {
        $this->stack = [];
        $this->start = null;
        $this->current = 0;
        $this->stopwatch->reset();
    }

    private function doStop()
    {
        $this->stack[$this->current]['executionTime'] = microtime(true) - $this->start;

        if ($this->stopwatch) {
            $this->stopwatch->stop('buzz', 'buzz');
        }
    }

    public function getStack(): array
    {
        return $this->stack;
    }
}
