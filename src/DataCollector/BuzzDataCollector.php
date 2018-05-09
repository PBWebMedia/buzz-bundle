<?php

namespace Pbweb\BuzzBundle\DataCollector;

use Pbweb\BuzzBundle\Logger\DebugStack;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * @copyright 2015 PB Web Media B.V.
 */
class BuzzDataCollector extends DataCollector
{
    /** @var DebugStack */
    private $logger;

    public function __construct(DebugStack $logger)
    {
        $this->logger = $logger;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $requestList = $this->logger->getStack();

        $executionTime = array_sum(array_column($requestList, 'executionTime'));

        $hasError = array_reduce($requestList, function ($carry, array $request) {
            /** @var ResponseInterface $response */
            $response = $request['response'];

            return $carry || ! $response || ! ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300);
        }, false);

        $this->data = [
            'requestList' => $requestList,
            'requestCount' => count($requestList),
            'executionTime' => $executionTime,
            'hasError' => $hasError,
        ];
    }

    public function reset()
    {
        $this->data = [];
        $this->logger->clear();
    }

    public function getRequestCount(): int
    {
        return $this->data['requestCount'];
    }

    public function getTime(): float
    {
        return $this->data['executionTime'];
    }

    public function getRequestList(): array
    {
        return $this->data['requestList'];
    }

    public function hasError(): bool
    {
        return $this->data['hasError'];
    }

    public function getName()
    {
        return 'buzz';
    }
}
