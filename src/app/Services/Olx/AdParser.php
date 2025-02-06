<?php

declare(strict_types=1);

namespace App\Services\Olx;

use App\Services\Olx\Api\ApiService;
use App\Services\Olx\Dusk\HTMLParseService;

class AdParser
{
    private ApiService $api;

    private HTMLParseService $dusk;

    public function __construct()
    {
        $this->api = new ApiService();
        $this->dusk = new HTMLParseService();
    }

    public function parseId(string $url): string
    {
        return $this->dusk->getId($url);
    }

    public function getPrice(string $id): array
    {
        return $this->api->getPrice($id);
    }
}
