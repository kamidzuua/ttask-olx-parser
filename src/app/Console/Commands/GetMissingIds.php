<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\FirstTimeAdSetup;
use App\Models\Ad;
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

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $collection = $this->getCollection();
        $job = new FirstTimeAdSetup($collection);
        dispatch($job)->onQueue(FirstTimeAdSetup::QUEUE_NAME);
        Log::error('huesos');
    }

    private function getCollection(): Collection
    {
        return Ad::whereNull('olx_id')
            ->get();
    }
}
