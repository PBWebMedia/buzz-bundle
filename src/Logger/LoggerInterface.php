<?php

namespace Pbweb\BuzzBundle\Logger;

use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;

/**
 * @copyright 2015 PB Web Media B.V.
 */
interface LoggerInterface
{
    public function start(RequestInterface $request);
    public function stop(MessageInterface $response);
    public function fail(\Exception $exception);
    public function clear();
}
