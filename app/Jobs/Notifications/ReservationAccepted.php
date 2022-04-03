<?php

namespace App\Jobs\Notifications;

use App\Models\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReservationAccepted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $reserve, $rest_name, $datetime;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($reserve, $rest_name, $datetime)
    {
        $this->reserve = $reserve;
        $this->rest_name = $rest_name;
        $this->datetime = $datetime;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = [
            'id'=>$this->reserve->id,
            'type'=>'reservation',
            'title' => 'Reservation Accepted',
            'sub_title' => '',
            'message' => 'Your reservation has been accepted',
        ];
        
        // Store Notification In Database
        $notification = new UserNotification([
            'user_id' => $this->reserve->user_id,
            'for_type' => 'reservation',
            'for_id' => $this->reserve->id,
            'notification_type_id' => 1,
            'text' => '' ,
            'payload' => json_encode($payload),
        ]);

        $notification->save();

        // Broadcast Notification to Firebase Cloud Message
        $user = $this->reserve->user;
        if (!is_null($user->firebase_notification_token)){
            sendNotification($user->firebase_notification_token, $payload);
        }
    }
}
