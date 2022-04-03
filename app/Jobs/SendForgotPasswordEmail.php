<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendForgotPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $link, $user, $email;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($link, $user, $email)
    {
        $this->link = $link;
        $this->user = $user;
        $this->email = $email;
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
        $email = $this->email;

        Mail::send('theme_landing.pages.resetemail', compact('link'), function ($message) use($email, $user) {
            $message->from('no-reply@leviabd.com', 'Levia');
            $message->sender('no-reply@leviabd.com', 'Levia');
            $message->to($email, $user->name);
            $message->subject('Password Recovery for Levia');
        });
    }
}
