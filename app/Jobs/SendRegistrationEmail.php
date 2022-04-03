<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendRegistrationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $link, $user, $otp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($link, $user, $otp)
    {
        $this->link = $link;
        $this->otp = $otp;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $link = $this->link;
        $user = $this->user;
        $otp = $this->otp;

        Mail::send('theme_landing.pages.registrationemail', compact('link', 'otp'), function ($message) use($user) {
            $message->from('no-reply@leviabd.com', 'Levia');
            $message->sender('no-reply@leviabd.com', 'Levia');
            $message->to($user->email, $user->name);
            $message->subject('Welcome to Levia');
        });
    }
}
