<?php

namespace Pbweb\BuzzBundle\Debug;

use Buzz\Browser;
use Buzz\Client\ClientInterface;
use Buzz\Message\Factory\FactoryInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Pbweb\BuzzBundle\Logger\LoggerInterface;
use Psr\Http\Message\RequestInterface as Psr7RequestInterface;

/**
 * @copyright 2015 PB Web Media B.V.
 */
class TraceableBrowser extends Browser
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger, ClientInterface $client = null, FactoryInterface $factory = null)
    {
        parent::__construct($client, $factory);
        $this->logger = $logger;
    }

    public function send(RequestInterface $request, MessageInterface $response = null)
    {
        $this->logger->start($request);
        try {
            $response = parent::send($request, $response);
        } catch (\RuntimeException $exception) {
            $this->logger->fail($exception);

            throw $exception;
        }
        $this->logger->stop($response);

        return $response;
    }

    public function sendRequest(Psr7RequestInterface $request)
    {
        $this->logger->startRequest($request);
        try {
            $response = parent::sendRequest($request);
        } catch (\RuntimeException $exception) {
            $this->logger->fail($exception);

            throw $exception;
        }
        $this->logger->stopRequest($response);

        return $response;
    }
}
