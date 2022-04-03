<?php

namespace App\Jobs\Notifications;

use App\Models\Post;
use App\Models\PostLike;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class PostShareNotificationToAuthor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $post_which_is_shared, $share_post;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($post_which_is_shared, $share_post)
    {
        $this->post_which_is_shared = $post_which_is_shared;
        $this->share_post = $share_post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $author = $this->post_which_is_shared->user;

        if ($this->post_which_is_shared->user_id === $this->share_post->user_id){
            return;
        }

        // If already a notification exists for this post and about comments, update this
        $notification = UserNotification::where('for_type', 'share')
            ->where('for_id', $this->post_which_is_shared->id)
            ->where('user_id', $this->post_which_is_shared->user_id)
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
                'for_type' => 'share',
                'for_id' => $this->post_which_is_shared->id,
                'user_id' => $this->post_which_is_shared->user_id,
                'notification_type_id' => 1,
            ]);

            $old_updated_at = Carbon::now()->subMinutes(60);
        }

        // DB::enableQueryLog();
        $latest_names = Post::with('user:fb_profile_name,id')
            ->select(['id','user_id'])
            ->where('shared_post_id',$this->post_which_is_shared->id)
            ->orderBy('created_at','desc')
            ->groupBy('user_id')
            ->take(3)
            ->get();
        // dd($latest_names);
        // Log::info('Latest names:' . $latest_names);
        $total_count = Post::where('shared_post_id',$this->post_which_is_shared->id)
            ->distinct('user_id')
            ->count('user_id');
        // Log::info('Total count:' . $total_count);
        $additional_names = $total_count - $latest_names->count();
        // Log::info('Additional count:' . $additional_names);
        $names_array = $latest_names->map(function($name){return $name->user->fb_profile_name;})->all();
        // dd($names_array);
        $names_string = namesArrayToString($names_array, $additional_names);

        $payload = [
            'id'=>$this->share_post->id,
            'type'=>'share',
            'title' => 'Your post has been shared',
            'sub_title' => '',
            'message' => $names_string . ' has shared your post.',
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
