<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\FirstTimeAdSetup;
use App\Jobs\UpdateAdPrices;
use App\Models\Ad;
use App\Services\Olx\AdParser;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GetMissingIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'olx:get-missing-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs FirstTimeAdSetup job';

    private AdParser $parser;

    private Collection $ads;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->parser = new AdParser();

        $this->ads = $this->getCollection();

        $updates = $this->onlyUniqueUrls()->map(function (string $url) {
            $adId = $this->parser->parseId($url);
            $adPrice = $this->parser->getPrice($adId);

            return [
                'id'          =>  $adId,
                'last_price'  =>  $adPrice,
                'url'     =>  $url
            ];
        });

        $this->ads->each(function (Ad $ad) use ($updates) {
            dispatch((new FirstTimeAdSetup($ad, $updates)))->onQueue(FirstTimeAdSetup::QUEUE_NAME);
        });
    }

    private function getCollection(): Collection
    {
        return Ad::whereNull('olx_id')
            ->get();
    }

    private function onlyUniqueUrls(): Collection
    {
        return $this->ads
            ->unique(fn (Ad $ad) => $ad->url)
            ->map(fn (Ad $ad) => $ad->url);
    }
}
