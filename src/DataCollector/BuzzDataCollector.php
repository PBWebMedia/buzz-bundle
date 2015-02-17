<?php

namespace Pbweb\BuzzBundle\DataCollector;

use Pbweb\BuzzBundle\Logger\DebugStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Buzz\Message\Response as BuzzResponse;

/**
 * Class BuzzDataCollector
 *
 * @copyright 2015 PB Web Media B.V.
 */
class BuzzDataCollector extends DataCollector
{
    /** @var DebugStack */
    private $logger;

    /**
     * @param DebugStack $logger
     */
    public function __construct(DebugStack $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $requestList = $this->logger->getStack();

        $executionTime = array_sum(array_column($requestList, 'executionTime'));

        $hasError = array_reduce($requestList, function ($carry, array $request) {
            /** @var BuzzResponse $response */
            $response = $request['response'];

            return $carry || ! $response || ! $response->isSuccessful();
        }, false);

        $this->data = array(
            'requestList' => $requestList,
            'requestCount' => count($requestList),
            'executionTime' => $executionTime,
            'hasError' => $hasError,
        );
    }
    /**
     * @return int
     */
    public function getRequestCount()
    {
        return $this->data['requestCount'];
    }

    /**
     * @return float
     */
    public function getTime()
    {
        return $this->data['executionTime'];
    }

    /**
     * @return array
     */
    public function getRequestList()
    {
        return $this->data['requestList'];
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->data['hasError'];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'buzz';
    }
}
