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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getCollection()->each(function (Ad $ad) {
           dispatch((new UpdateAdPrices($ad)))->onQueue(UpdateAdPrices::QUEUE_NAME);
        });
    }

    private function getCollection(): Collection
    {
        return Ad::with('emails')
            ->get();
    }
}
