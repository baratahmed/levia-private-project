<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPhoneChangeVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $number, $code;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($number, $code)
    {
        $this->number = $number;
        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Sending Mail for test purpose
        // Mail::raw('Your Phone Change Request Verification Code : ' . $this->code, function ($message) {
        //     $message->from('no-reply@leviabd.com', 'Levia');
        //     $message->to('num_'. $this->number .'@johndoe.com');
        //     $message->subject('Phone Change Request Verification Code');
        // });
    }
}
