<?php

namespace Pbweb\BuzzBundle\Debug;

use Buzz\Browser;
use Buzz\Client\ClientInterface;
use Buzz\Message\Factory\FactoryInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Pbweb\BuzzBundle\Logger\LoggerInterface;

/**
 * Class TraceableBrowser
 *
 * @copyright 2015 PB Web Media B.V.
 */
class TraceableBrowser extends Browser
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * @param LoggerInterface  $logger
     * @param ClientInterface  $client
     * @param FactoryInterface $factory
     */
    public function __construct(LoggerInterface $logger, ClientInterface $client = null, FactoryInterface $factory = null)
    {
        parent::__construct($client, $factory);
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
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
}
