<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Ad;
use App\Services\Olx\AdParser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class UpdateAdPrices implements ShouldQueue
{
    use Queueable;

    public const QUEUE_NAME = 'parsers';

    private Collection $ads;

    private AdParser $parser;

    /**
     * Create a new job instance.
     */
    public function __construct(Collection $ads)
    {
        $this->ads = $ads;
        $this->parser = new AdParser();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->ads->each(function (Ad $ad) {
           $price = $this->parser->getPrice($ad->id);
           if ($price != $ad->last_price) {
               $ad->update([
                   'last_price' => $price
               ]);

               SendUpdateEmail::dispatch($ad)->onQueue('emails');
           }
        });
    }
}
