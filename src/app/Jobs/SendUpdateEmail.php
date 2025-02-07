<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\PriceChanged;
use App\Models\Ad;
use App\Models\Email;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class SendUpdateEmail implements ShouldQueue
{
    public const QUEUE_NAME = 'emails';

    use Queueable;

    private Ad $ad;

    private Collection $emails;

    /**
     * Create a new job instance.
     */
    public function __construct(Ad $ad, Collection $emails)
    {
        $this->ad = $ad;
        $this->emails = $emails;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->emails->each(function (Email $email) {
            Mail::to($email->email)
                ->send(new PriceChanged($this->ad));
        });
    }
}
