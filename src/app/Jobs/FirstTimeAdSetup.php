<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Ad;
use App\Services\Olx\AdParser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class FirstTimeAdSetup implements ShouldQueue
{
    use Queueable;

    public const QUEUE_NAME = 'setups';

    private Collection $ads;

    private AdParser $parser;

    /**
     * Create a new job instance.
     */
    public function __construct(Collection $ads)
    {
        $this->ads = $ads;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->parser = new AdParser();

        $parsedMap = $this->onlyUniqueUrls()->map(function (string $url) {
            $adId = $this->parser->parseId($url);
            $adPrice = $this->parser->getPrice($adId);

            return [
              'id'          =>  $adId,
              'last_price'  =>  $adPrice,
              'url'     =>  $url
            ];
        });

        $this->ads->each(function (Ad $ad) use ($parsedMap) {
            $found = $parsedMap->firstWhere(fn (array $piece) => $piece['url'] === $ad->url);

            $ad->update([
                'olx_id'        =>  $found['id'],
                'last_price'    =>  $found['last_price']
            ]);

            (new SendSubscribeEmail($ad))->onQueue(SendSubscribeEmail::QUEUE_NAME);
        });
    }

    private function onlyUniqueUrls(): Collection
    {
        return $this->ads
            ->unique(fn (Ad $ad) => $ad->url)
            ->map(fn (Ad $ad) => $ad->url);
    }
}
