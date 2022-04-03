<?php

namespace App\Jobs\Notifications;

use App\Models\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PlaceOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
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
            'type'=>'new_order',
            'title' => 'You received an order.',
            'sub_title' => '',
            'message' => 'You received an order.',
        ];
        
        // Store Notification In Database
        $notification = new UserNotification([
            'rest_id' => $this->order->rest_id,
            'user_type' => 'REST',
            'for_type' => 'order',
            'for_id' => $this->order->id,
            'notification_type_id' => 1,
            'text' => '' ,
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
