<?php

declare(strict_types=1);

namespace App\Services\Olx\Api;

use GuzzleHttp\Client;

class ApiService
{
    private const DELIVERY_ROUTE = 'https://ua.production.delivery.olx.tools/payment/ad/%s/buyer/?lang=uk-UA';

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getPrice(string $id): int
    {
        $route = sprintf(self::DELIVERY_ROUTE, $id);

        $response = $this->makeRequest('get', $route);

        return intval($response['product']['price']);
    }

    private function makeRequest(string $method, string $route, array $headers = []): array
    {
        $response = $this->client->request($method, $route, $headers);

        return json_decode($response->getBody()->getContents(), true);
    }
}
