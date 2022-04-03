<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\Notifications\UpdateUnlikePostNotification;
use App\Jobs\Notifications\SendPostLikeNotificationToAuthor;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostLikeController extends Controller
{
        /**
     * Display list of persons who liked this post
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
        $likes = $post->likes()->with('user:id,fb_profile_name,fb_profile_pic_url')->get();

        $likes->map(function($like){
            $like['follow_status'] = my_follow_status( auth('api')->id() , $like->user_id);
            $like['number_of_reviews'] = number_of_ratings($like->user_id);
            return $like;
        });
        
        return response([
          'success' => 'true',
          'data'    => $likes,
        ], 200);
    }


    /**
     * Like or Unlike a Post
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Post $post, Request $request)
    {
        $this->validate($request, [
          'action' => 'required',
        ]);

        $user = auth('api')->user();
        // $user = $post->likes()->orderBy('updated_at', 'desc')->first()->user;

        $action = $request->action;

        if ($action != "LIKE" && $action != "UNLIKE"){
            throw ValidationException::withMessages([
                'action' => 'Invalid action. Action should be LIKE or UNLIKE'
            ]);
        }

        $like = $post->likes()->where('user_id', $user->id)->first();

        if (!$like){
            if ($action == "LIKE"){
                $like = new PostLike([
                    'user_id' => $user->id
                ]);

                
                DB::transaction(function () use($post, $like) {
                    $post->likes()->save($like);
                    $post->likes_count++;
                    $post->save();
                });

                SendPostLikeNotificationToAuthor::dispatch($post, $like);

                return response([
                    'success' => 'true',
                    'message' => 'Like Successful.',
                ], 200);
            } else {
                throw ValidationException::withMessages([
                    'post_id' => 'Invalid operation. You haven\'t liked the post yet'
                ]);
            }
        } else {
            if ($action == "UNLIKE"){
                DB::transaction(function () use($post, $like) {
                    $like->delete();
                    $post->likes_count--;
                    $post->save();
                });

                UpdateUnlikePostNotification::dispatch($post);

                return response([
                    'success' => 'true',
                    'message' => 'Unlike Successful.',
                ], 200);
            } else {
                throw ValidationException::withMessages([
                    'post_id' => 'You have already liked this post'
                ]);
            }
        }

    }
}
