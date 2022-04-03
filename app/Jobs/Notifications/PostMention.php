<?php

namespace App\Jobs\Notifications;

use App\Models\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PostMention implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mentioned_users, $post;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mentioned_users, $post)
    {
        $this->mentioned_users = $mentioned_users;
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // dd($this->mentioned_users);
        foreach ($this->mentioned_users as $mentioned_user){
            $notification =  new UserNotification([
                'for_type' => 'mention',
                'for_id' => $this->post->id,
                'user_id' => $mentioned_user->id,
                'notification_type_id' => 1,
            ]);

            $payload = [
                'id'=>$this->post->id,
                'type'=>'mention',
                'title' => $this->post->user->fb_profile_name . ' has mentioned you in a post.',
                'sub_title' => $this->post->user->fb_profile_name . ' has mentioned you in a post.',
                'message' => $this->post->user->fb_profile_name . ' has mentioned you in a post.',
                'thumb' => $this->post->user->profile_picture
            ];

            $notification->payload = json_encode($payload);
            $notification->save();

            if (!is_null($mentioned_user->firebase_notification_token)){
                sendNotification($mentioned_user->firebase_notification_token, $payload);
            }
        }
    }
}
