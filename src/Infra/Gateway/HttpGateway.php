<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Gateway;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use WakeOnWeb\MessageBusPublisher\Domain\Gateway\GatewayInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Gateway\GatewayResponse;

class HttpGateway implements GatewayInterface
{
    private $httpClient;
    private $endpoint;

    public function __construct(HttpClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?: new HttpClient();
    }

    public function configure(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function send($message): GatewayResponse
    {
        $request = $this->buildRequestFromMessage($message);

        try {
            $response = $this->httpClient->send($request);

            return GatewayResponse::createSuccessfulResponse((string) $response->getBody());
        } catch (RequestException $e) {
            return GatewayResponse::createFailureResponse((string) $e->getResponse() ? $e->getResponse()->getBody() : null);
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
