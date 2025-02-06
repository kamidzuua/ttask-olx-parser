<?php

declare(strict_types=1);

namespace App\Services\Olx\Dusk;

use DOMDocument;
use DOMElement;
use DOMXPath;

class HTMLParseService
{
    public function getId(string $url): ?string
    {
        return $this->parsePage($url);
    }

    private function parsePage($url): ?string
    {
        $doc = $this->getDoc($url);

        $url = $this->getUrlContainer($doc);

        if ($url) {
            return $this->parseId($url);
        } else {
            return null;
        }
    }

    private function getDoc($url): DOMDocument
    {
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            CURLOPT_SSL_VERIFYPEER => false,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $html = curl_exec($ch);
        curl_close($ch);

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $doc = new DOMDocument();
        $doc->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        return $doc;
    }

    private function getUrlContainer(DOMDocument $doc): ?DOMElement
    {
        $xpath = new DOMXPath($doc);
        $nodeList = $xpath->query('//a[@data-testid="promotion-link"]');

        if ($nodeList->length > 0) {
            return $nodeList->item(0);
        }

        return null;
    }

    private function parseId(DOMElement $url)
    {
        $href = $url->getAttribute('href');

        $parsedUrl = parse_url($href);

        if (!isset($parsedUrl['query'])) {
            return null;
        }

        parse_str($parsedUrl['query'], $queryParams);

        return $queryParams['ad-id'] ?? null;
    }
}
