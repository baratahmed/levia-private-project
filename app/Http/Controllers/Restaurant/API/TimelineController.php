<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Restaurant\PostsController;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\RestaurantInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TimelineController extends Controller
{
    public function getTimeline(){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $posts = Post::
            with('user:id,fb_profile_name,fb_profile_pic_url')->
            with('restaurant:id,rest_name,rest_image_url')->
            with('meta:id,rest_id,food_id,type,action,object,rating_value,review_text,media')->
            leftJoin('news_feeds', 'posts.post_meta_id', '=', 'news_feeds.id')->
            selectRaw('posts.*, news_feeds.rest_id as news_feed_rest_id')->
            orderBy('updated_at', 'desc');

        $post_ids = $posts->pluck('id');

        $posts = $posts->where(function($query) use($rest) {
            $query->where('posts.rest_id', $rest->id)
                ->orWhere('news_feeds.rest_id', $rest->id);
        });

        $posts = $posts->paginate(10);


        $posts->getCollection()->transform(function ($value) {
            $value['created_at_string'] = Carbon::parse($value['created_at'])->diffForHumans();
            return $value;
        });
  
        return response([
            'success' => 'true',
            'data'    => $posts
        ], 200);
    }

    public function getComments(Request $request){
        $this->validate($request, [
            'post_id' => 'required|exists:posts,id',
        ]);
        
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $post = Post::find($request->post_id);

        if ($post->rest_id == $rest->id || ($post->meta !== null && $post->meta->rest_id == $rest->id)){
            $comments = PostComment::with('user:id,fb_profile_name,fb_profile_pic_url')
                ->where('post_id', $post->id)->paginate(10);

            foreach($comments as $comment){
                $comment->created_at_string = $comment->created_at->diffForHumans();
            }

            return response([
                'success' => true,
                'data' => [
                    'post' => $post,
                    'comments' => $comments
                ],
            ]);
            
        }

        return response([
            'success' => false,
            'message' => 'You are not authorized to view the comments',
        ], 422);
        
    }
    
    public function getLikes(Request $request){
        $this->validate($request, [
            'post_id' => 'required|exists:posts,id',
        ]);
        
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $post = Post::find($request->post_id);

        if ($post->rest_id == $rest->id || ($post->meta !== null && $post->meta->rest_id == $rest->id)){
            $likes = $post->likes()->with('user:id,fb_profile_name,fb_profile_pic_url')->paginate(10);

            $likes->map(function($like){
                $like['number_of_reviews'] = number_of_ratings($like->user_id);
                return $like;
            });

            return response([
                'success' => true,
                'data' => [
                    'post' => $post,
                    'likes' => $likes
                ],
            ]);
            
        }

        return response([
            'success' => false,
            'message' => 'You are not authorized to view the comments',
        ], 422);
        
    }

    public function postCreatePost(Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        // Patch the request for arrays
        Log::info("Update rest schedule request (Before Patch): ", $request->all());
        $request->merge(extractStringToArray($request));
        Log::info("Update rest schedule request (After Patch): ", $request->all());

        $post_controller = new PostsController;
        $response = $post_controller->store($request, true);

        return $response;
    }
}
