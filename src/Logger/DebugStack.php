<?php

namespace Pbweb\BuzzBundle\Logger;

use Buzz\Converter\RequestConverter;
use Buzz\Converter\ResponseConverter;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Psr\Http\Message\RequestInterface as Psr7RequestInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @copyright 2015 PB Web Media B.V.
 */
class DebugStack implements LoggerInterface
{
    /** @var Stopwatch|null */
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
        $this->startRequest(RequestConverter::psr7($request));
    }

    public function startRequest(Psr7RequestInterface $request)
    {
        $this->start = microtime(true);
        $this->stack[++$this->current] = [
            'request' => $request,
            'request_body' => (string) $request->getBody(),
            'response' => null,
            'response_body' => null,
            'exception' => null,
            'executionTime' => 0,
        ];

        if ($this->stopwatch) {
            $this->stopwatch->start('buzz', 'buzz');
        }
    }

    public function stop(MessageInterface $response)
    {
        $this->stopRequest(ResponseConverter::psr7($response));
    }

    public function stopRequest(Psr7ResponseInterface $response)
    {
        $this->stack[$this->current]['response'] = $response;
        $this->stack[$this->current]['response_body'] = (string) $response->getBody();
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
            $this->stopwatch->stop('buzz');
        }
    }

    public function getStack(): array
    {
        return $this->stack;
    }
}
