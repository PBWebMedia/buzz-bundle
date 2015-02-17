<?php

namespace Pbweb\BuzzBundle\Logger;

use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class DebugStack
 *
 * @copyright 2015 PB Web Media B.V.
 */
class DebugStack implements LoggerInterface
{
    /** @var Stopwatch */
    private $stopwatch;

    /** @var array */
    private $stack = array();

    /** @var float|null */
    private $start = null;

    /** @var int */
    private $current = 0;

    /**
     * @param Stopwatch $stopwatch
     */
    public function __construct(Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritDoc}
     */
    public function start(RequestInterface $request)
    {
        $this->start = microtime(true);
        $this->stack[++$this->current] = array(
            'request' => $request,
            'response' => null,
            'exception' => null,
            'executionTime' => 0,
        );

        if ($this->stopwatch) {
            $this->stopwatch->start('buzz', 'buzz');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function stop(MessageInterface $response)
    {
        $this->stack[$this->current]['response'] = $response;
        $this->doStop();
    }

    /**
     * {@inheritDoc}
     */
    public function fail(\Exception $exception)
    {
        $this->stack[$this->current]['exception'] = (string) $exception;
        $this->doStop();
    }

    private function doStop()
    {
        $this->stack[$this->current]['executionTime'] = microtime(true) - $this->start;

        if ($this->stopwatch) {
            $this->stopwatch->stop('buzz', 'buzz');
        }
    }

    /**
     * @return array
     */
    public function getStack()
    {
        return $this->stack;
    }
}
