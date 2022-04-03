<?php

namespace App\Jobs\Notifications;

use App\Models\PostComment;
use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendPostCommentNotificationToAuthor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $post, $comment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($post, $comment)
    {
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
        $author = $this->post->user;

        if ($this->post->user_id === $this->comment->user_id){
            return;
        }

        // If already a notification exists for this post and about comments, update this
        $notification = UserNotification::where('for_type', 'comment')
                        ->where('for_id', $this->post->id)
                        ->where('user_id', $this->post->user_id)
                        ->where('is_seen', false)
                        ->where('notification_type_id', 1)
                        ->orderBy('updated_at', 'desc')
                        ->first();

        if ($notification){
            $old_updated_at = $notification->updated_at;
        }

        // If the notification is new, store as new
        else {
            $notification =  new UserNotification([
                'for_type' => 'comment',
                'for_id' => $this->post->id,
                'user_id' => $this->post->user_id,
                'notification_type_id' => 1,
            ]);

            $old_updated_at = Carbon::now()->subMinutes(60);
        }

        $latest_names = PostComment::with('user:fb_profile_name,id')->select(['id','user_id'])->where('post_id',$this->post->id)->orderBy('created_at','desc')->groupBy('user_id')->take(3)->get();
        // Log::info('Latest names:' . $latest_names);
        $total_count = PostComment::where('post_id',$this->post->id)->distinct('user_id')->count('user_id');
        // Log::info('Total count:' . $total_count);
        $additional_names = $total_count - $latest_names->count();
        // Log::info('Additional count:' . $additional_names);
        $names_array = $latest_names->map(function($name){return $name->user->fb_profile_name;})->all();
        $names_string = namesArrayToString($names_array, $additional_names);

        $payload = [
            'id'=>$this->post->id,
            'type'=>'comment',
            'title' => 'New Comment',
            'sub_title' => '',
            'message' => $names_string . ' has commented on your post.',
        ];

        $notification->payload = json_encode($payload);
        $notification->save();

        // Send Push notification only when there is a one hour gap 
        // betwen last push notification for the same type of event.
        $diff = $old_updated_at->diffInMinutes($notification->updated_at);
        
        // Log::info("old_updated_at : $old_updated_at, notif->updated_at : {$notification->updated_at} \nDIFF: $diff");
        
        if (!is_null($author->firebase_notification_token)){
            sendNotification($author->firebase_notification_token, $payload);
        }

    }
}
