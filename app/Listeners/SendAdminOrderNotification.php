<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\AdminOrderMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Agent;


class SendAdminOrderNotification implements ShouldQueue
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
        $admin = Agent::find(1);

        if (!$admin || !$admin->email) {
            return;
        }

        Mail::to($admin->email)
            ->send(new AdminOrderMail($event->order));
    }
}
