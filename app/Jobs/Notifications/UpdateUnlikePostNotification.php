<?php

namespace App\Jobs\Notifications;

use App\Models\PostLike;
use App\Models\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateUnlikePostNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $post;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $author = $this->post->user;
        // If already a notification exists for this post and about likes, update this
        $notification = UserNotification::where('for_type', 'like')
                        ->where('for_id', $this->post->id)
                        ->where('user_id', $this->post->user_id)
                        // ->where('is_seen', false)
                        ->where('notification_type_id', 1)
                        ->orderBy('updated_at', 'desc')
                        ->first();

        if ($notification){
            $old_updated_at = $notification->updated_at;
        }

        // If notification is not found, stop execution
        else {
            return;
        }

        $latest_names = PostLike::with('user:fb_profile_name,id')->select(['id','user_id'])->where('post_id',$this->post->id)->orderBy('created_at','desc')->groupBy('user_id')->take(3)->get();
        // Log::info('Latest names:' . $latest_names);
        $total_count = PostLike::where('post_id',$this->post->id)->distinct('user_id')->count('user_id');
        // Log::info('Total count:' . $total_count);
        $additional_names = $total_count - $latest_names->count();
        // Log::info('Additional count:' . $additional_names);
        $names_array = $latest_names->map(function($name){return $name->user->fb_profile_name;})->all();
        $names_string = namesArrayToString($names_array, $additional_names);

        $payload = [
            'id'=>$this->post->id,
            'type'=>'like',
            'title' => 'New likes on your post',
            'sub_title' => '',
            'message' => $names_string . ' has liked your post.',
        ];

        $notification->payload = json_encode($payload);
        $notification->save();
    }
}
