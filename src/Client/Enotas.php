<?php

namespace Vluzrmos\Enotas\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class Enotas
{
    protected $baseUri = 'https://app.enotas.com.br/api/';
    protected $apiKey = null;

    protected static ?Enotas $instance = null;

    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    public static function getInstance()
    {
        return static::$instance;
    }

    public static function setInstance(Enotas $enotas)
    {
        static::$instance = $enotas;
    }

    public function useAsGlobalInstance()
    {
        static::setInstance($this);

        return $this;
    }

    public function setBaseUri($baseUri)
    {
        $this->baseUri = $this->unslash($baseUri).'/';

        return $this;
    }

    protected function unslash($str)
    {
        return trim($str, " \t\n\r\0\x0B/\\");
    }

    public function httpClient()
    {
        return new Client([
            'base_uri' => $this->baseUri,
            'headers' => $this->getHttpClientHeaders(),
        ]);
    }

    public function getHttpClientHeaders()
    {
        return [
            'Authorization' => "Basic {$this->apiKey}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
    }

    public function request($method, $endpoint, array $options = [])
    {
        $endpoint = $this->unslash($endpoint);

        /** @var Response */
        $response = $this->httpClient()->request($method, $endpoint, $options);

        if (!$this->responseIsSucessful($response)) {
            return [];
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param Response|null $response
     */
    protected function responseIsSucessful($response)
    {
        if (!$response) {
            return false;
        }

        $code = $response->getStatusCode();

        return $code >= 200 && $code < 300;
    }
}
