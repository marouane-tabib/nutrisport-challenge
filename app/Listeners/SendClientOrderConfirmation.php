<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\ClientOrderMail;
use Illuminate\Support\Facades\Mail;

class SendClientOrderConfirmation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $delay = 5;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        if (!$event->order->user->email) {
            return;
        }

        Mail::to($event->order->user->email)
            ->send(new ClientOrderMail($event->order));
    }
}
