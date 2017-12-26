<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Gateway;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayInterface;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayResponse;

class HttpGateway implements GatewayInterface
{
    private $endpoint;
    private $httpClient;

    public function __construct(string $endpoint, HttpClient $httpClient = null)
    {
        $this->endpoint = $endpoint;
        $this->httpClient = $httpClient ?: new HttpClient();
    }

    public function send($message): GatewayResponse
    {
        $request = $this->buildRequestFromMessage($message);

        try {
            $response = $this->httpClient->send($request);

            return GatewayResponse::createSuccessfulResponse((string) $response->getBody());
        } catch (RequestException $e) {
            return GatewayResponse::createFailureResponse((string) $e->getResponse()->getBody());
        }
    }

    /**
     * Add your specific request informations here.
     */
    protected function buildRequestFromMessage($message): Request
    {
        return new Request('POST', $this->endpoint);
    }
}
