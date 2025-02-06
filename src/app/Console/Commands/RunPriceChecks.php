<?php

namespace App\Console\Commands;

use App\Jobs\UpdateAdPrices;
use App\Models\Ad;
use App\Services\Olx\AdParser;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class RunPriceChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'olx:run-price-checks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private AdParser $parser;

    private Collection $ads;

    /**
     * Execute the console command.
     */
    public function handle()
    {

    }

    private function getCollection(): Collection
    {
        return Ad::all();
    }

    private function onlyUniqueUrls(): Collection
    {
        return $this->ads
            ->unique(fn (Ad $ad) => $ad->url)
            ->map(fn (Ad $ad) => $ad->url);
    }
}
