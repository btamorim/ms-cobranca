<?php

namespace App\Jobs;

use Throwable;
use App\Traits\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Mail\notificationChargeTicket;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class notificationChargeCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Log;

    private $attributes;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::to($this->attributes['email'])->send(new notificationChargeTicket($this->attributes));
        } catch (Throwable $th) {
            $this->storeLogData(['message' => $th->getMessage()], "error_send_charge_notification");
        }
    }
}
