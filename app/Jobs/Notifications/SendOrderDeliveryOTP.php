<?php

namespace App\Jobs\Notifications;

use App\Models\Order;
use App\Models\OrderVerificationCode;
use App\Models\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendOrderDeliveryOTP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * Order Model
     *
     * @var Order
     */
    private $order;

    /**
     * Order Verification Model
     *
     * @var OrderVerificationCode
     */
    private $verification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, OrderVerificationCode $verification)
    {
        $this->order = $order;
        $this->verification = $verification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = [
            'id'=>$this->order->id,
            'type'=>'otp.delivery',
            'title' => 'Your order delivery otp.',
            'sub_title' => '',
            'message' => 'Your order (#'.$this->order->order_number.') delivery otp is: ' . $this->verification->verification_digits,
        ];
        
        // Store Notification In Database for Restaurant
        $notification = new UserNotification([
            'user_id' => $this->order->user_id,
            'user_type' => 'USER',
            'for_type' => 'otp.delivery',
            'for_id' => $this->order->id,
            'notification_type_id' => 1,
            'text' => 'Your order delivery otp is: ' . $this->verification->verification_digits ,
            'payload' => json_encode($payload),
        ]);

        $notification->save();

        // Broadcast Notification to Firebase Cloud Message
        $customer = $this->order->customer;

        if ( !is_null($customer) && !is_null($customer->firebase_notification_token) ){
            sendNotification($customer->firebase_notification_token, $payload);
        }
    }
}
