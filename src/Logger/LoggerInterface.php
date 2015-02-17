<?php

namespace Pbweb\BuzzBundle\Logger;

use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;

/**
 * Interface LoggerInterface
 *
 * @copyright 2015 PB Web Media B.V.
 */
interface LoggerInterface
{
    /**
     * @param RequestInterface $request
     */
    public function start(RequestInterface $request);

    /**
     * @param MessageInterface $response
     */
    public function stop(MessageInterface $response);

    /**
     * @param \Exception $exception
     */
    public function fail(\Exception $exception);
}
