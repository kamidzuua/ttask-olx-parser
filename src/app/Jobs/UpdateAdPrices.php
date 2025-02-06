<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Ad;
use App\Services\Olx\AdParser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateAdPrices implements ShouldQueue
{
    use Queueable;

    public const QUEUE_NAME = 'parsers';

    private Ad $ad;

    private AdParser $parser;

    /**
     * Create a new job instance.
     */
    public function __construct(Ad $ad)
    {
        $this->ad = $ad;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->parser = new AdParser();

        if (!$this->ad->olx_id) {
            $this->ad->olx_id = $this->parser->parseId($this->ad->url);
        }

        $apiResponse = $this->parser->getPrice($this->ad->olx_id);
        $price = $apiResponse['price'];
        $currency = $apiResponse['currency'];

        if ($price != $this->ad->last_price || $currency != $this->ad->currency) {
            $this->ad->last_price = $price;
            $this->ad->currency = $currency;

            SendUpdateEmail::dispatch($this->ad, $this->ad->emails)->onQueue('emails');
        }

        $this->ad->save();
    }
}
