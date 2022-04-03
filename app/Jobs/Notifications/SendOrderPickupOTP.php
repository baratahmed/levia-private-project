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

class SendOrderPickupOTP implements ShouldQueue
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
            'type'=>'otp.pickup',
            'title' => 'Your order pickup otp.',
            'sub_title' => '',
            'message' => 'Your order (#'.$this->order->order_number.') pickup otp is: ' . $this->verification->verification_digits,
        ];
        
        // Store Notification In Database for Restaurant
        $notification = new UserNotification([
            'rest_id' => $this->order->rest_id,
            'user_type' => 'REST',
            'for_type' => 'otp.pickup',
            'for_id' => $this->order->id,
            'notification_type_id' => 1,
            'text' => 'Your order pickup otp is: ' . $this->verification->verification_digits ,
            'payload' => json_encode($payload),
        ]);

        $notification->save();

        // Broadcast Notification to Firebase Cloud Message
        $rest = $this->order->restaurant;
        $radmin = null !== $rest ? $rest->admin : null;

        if ( !is_null($radmin) && !is_null($radmin->firebase_notification_token) ){
            sendNotification($radmin->firebase_notification_token, $payload);
        }
    }
}
