<?php

namespace App\Jobs\Notifications;

use App\Models\RestAdmin;
use App\Models\RestaurantInfo;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendMessageNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sender, $sender_type, $receiver_id, $receiver_type, $message, $conversation;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sender, $sender_type,  $receiver_id, $receiver_type, $message, $conversation)
    {
        $this->sender = $sender;
        $this->sender_type = $sender_type;
        $this->receiver_id = $receiver_id;
        $this->receiver_type = $receiver_type;
        $this->message = $message;
        $this->conversation = $conversation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Sending Message Using Firebase Notification", [
            'conversation_id' => $this->conversation->id,
            'message_id' => $this->message->id,
        ]);

        $payload = [
            'id'=>$this->conversation->id,
            'message_id'=>$this->message->id,
            'type'=>'message',
            'title' => ( $this->sender_type === "REST" ? $this->sender->restaurant->rest_name : $this->sender->fb_profile_name ) . " | Levia",
            'sub_title' => 'New Message',
            'message' => substr($this->message->message, 0, 30),
        ];

        if ($this->receiver_type === "REST"){
            $rest = RestaurantInfo::find($this->receiver_id);
            $rest_admin = null !== $rest ? $rest->admin : null;
            $firebase_notification_token = $rest_admin->firebase_notification_token;
        } else {
            $user = User::find($this->receiver_id);
            $firebase_notification_token = $user->firebase_notification_token;
        }


        if ($this->sender_type === "USER"){
            $payload['thumb'] = $this->sender->getProfilePicture();
        } else {
            $payload['thumb'] = data_get($this, "sender.restaurant.image_url", asset('storage/rest_logo/default.jpg') );
        }

        if ( !is_null( $firebase_notification_token ) ){
            sendNotification( $firebase_notification_token, $payload );
            Log::info("Message Sent Using Firebase - Token: " . $firebase_notification_token);
        }
        
    }
}
