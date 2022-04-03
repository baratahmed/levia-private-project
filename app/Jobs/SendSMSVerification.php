<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSMSVerification implements ShouldQueue
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
        // Mail::raw('Your Verification Code : ' . $this->code, function ($message) {
        //     $message->from('no-reply@leviabd.com', 'Levia');
        //     $message->to('num_'. $this->number .'@johndoe.com');
        //     $message->subject('Subject');
        // });

        // Send Actual SMS using SMS API || Alpha.net.bd
        // $username = env('ALPHA_SMS_GATEWAY_USERNAME');
        // $hash = env('ALPHA_SMS_GATEWAY_HASH');

        // if (null === $username || null === $hash){
        //     return;
        // }

        // $numbers = $this->number;
        // $message = "Your+Levia+verification+code+is:+" . $this->code;
    
        // $params = array('u'=>$username, 'h'=>$hash, 'op'=>'pv', 'to'=>$numbers, 'msg'=>$message);
    
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "http://alphasms.biz/index.php?app=ws");
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // $response = curl_exec($ch);
        
        // Log::info($response);
        
        // curl_close ($ch);
    }
}
