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

    private Ad $ad;

    private Collection $updates;


    /**
     * Create a new job instance.
     */
    public function __construct(Ad $ad, Collection $updates)
    {
        $this->ad = $ad;
        $this->updates = $updates;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $found = $this->updates->firstWhere(fn (array $piece) => $piece['url'] === $this->ad->url);

        $this->ad->update([
            'olx_id'        =>  $found['id'],
            'last_price'    =>  $found['last_price']
        ]);

        (new SendSubscribeEmail($this->ad))->onQueue(SendSubscribeEmail::QUEUE_NAME);

    }
}
