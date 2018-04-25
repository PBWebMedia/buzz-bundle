<?php

namespace Pbweb\BuzzBundle\Logger;

use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Psr\Http\Message\RequestInterface as Psr7RequestInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

/**
 * @copyright 2015 PB Web Media B.V.
 */
interface LoggerInterface
{
    public function start(RequestInterface $request);
    public function startRequest(Psr7RequestInterface $request);
    public function stop(MessageInterface $response);
    public function stopRequest(Psr7ResponseInterface $response);
    public function fail(\Exception $exception);
    public function clear();
}
