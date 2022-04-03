<?php

namespace App\Jobs\Notifications;

use App\Models\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PostCommentMention implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $mentioned_users, $post, $comment;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mentioned_users, $post, $comment)
    {
        $this->mentioned_users = $mentioned_users;
        $this->post = $post;
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->mentioned_users as $mentioned_user){
            $notification =  new UserNotification([
                'for_type' => 'comment_mention',
                'for_id' => $this->comment->id,
                'user_id' => $mentioned_user->id,
                'notification_type_id' => 1,
            ]);

            $payload = [
                'id'=>$this->comment->id,
                'post_id'=>$this->post->id,
                'type'=>'comment_mention',
                'title' => $this->comment->user->fb_profile_name . ' has mentioned you in a comment.',
                'sub_title' => $this->comment->user->fb_profile_name . ' has mentioned you in a comment.',
                'message' => $this->comment->user->fb_profile_name . ' has mentioned you in a comment.',
                'thumb' => $this->comment->user->profile_picture
            ];

            $notification->payload = json_encode($payload);
            $notification->save();

            if (!is_null($mentioned_user->firebase_notification_token)){
                sendNotification($mentioned_user->firebase_notification_token, $payload);
            }
        }
    }
}
