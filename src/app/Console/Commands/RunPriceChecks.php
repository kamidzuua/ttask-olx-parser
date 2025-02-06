<?php

namespace App\Console\Commands;

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
        dd($this->getCollection());
    }

    private function getCollection(): Collection
    {
        return Ad::all();
    }
}
