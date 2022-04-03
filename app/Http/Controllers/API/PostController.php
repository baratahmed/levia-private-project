<?php

namespace App\Http\Controllers\API;

use App\Jobs\Notifications\PostMention;
use App\Jobs\Notifications\PostShareNotificationToAuthor;
use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostLike;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
          'user_id' => 'sometimes|exists:user_info,id',
        ]);


        $posts = Post::
            with('user:id,fb_profile_name,fb_profile_pic_url')->
            with('restaurant:id,rest_name,rest_image_url')->
            with('meta:id,rest_id,food_id,type,action,object,rating_value,review_text,media')->
            leftJoin('news_feeds', 'posts.post_meta_id', '=', 'news_feeds.id')->
            selectRaw('posts.*, news_feeds.rest_id as news_feed_rest_id')->
            orderBy('updated_at', 'desc');

        $post_ids = $posts->pluck('id');

        // Posts from only People I Follow
        if ($request->has('from') && strtoupper($request->input('from')) === 'FOLLOWING' && auth('api')->check()){
            $user = auth('api')->user();
            $following = UserFollow::where('posts.user_id', $user->id)->select('follow_id')->get();
            $posts = $posts->whereIn('posts.user_id', $following->pluck('follow_id'))->orWhere('posts.user_id', $user->id);
        } else { // Post from specific people
            if ($request->has('user_id')){
                $posts = $posts->where('posts.user_id', $request->user_id);
            }

            if ($request->has('rest_id')){
                $posts = $posts->where(function($query) use($request) {
                    $query->where('posts.rest_id', $request->rest_id)
                        ->orWhere('news_feeds.rest_id', $request->rest_id);
                });
            }
            else if ($request->has('restaurant_id')){
                $posts = $posts->where(function($query) use($request) {
                    $query->where('posts.rest_id', $request->restaurant_id)
                        ->orWhere('news_feeds.rest_id', $request->restaurant_id);
                });
            }
        }

        $posts = $posts->paginate(10);

        $is_liked = PostLike::whereIn('post_id', $post_ids)->where('user_id', auth()->id())->get();

        $posts->getCollection()->transform(function ($value) use($is_liked) {
            $value['is_liked'] = !!($is_liked->where('post_id', $value['id'])->first()) ? true : false;
            $value['created_at_string'] = Carbon::parse($value['created_at'])->diffForHumans();
            return $value;
        });

        return response([
            'success' => 'true',
            'data'    => $posts
        ], 200);
    }





    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $post = Post::
                with('user:id,fb_profile_name,fb_profile_pic_url')->
                with('meta:id,rest_id,food_id,type,action,object,rating_value,review_text,media')->
                orderBy('id', 'desc')->
                where('id', $post->id)->
                first();

        $post['is_liked'] = !!(PostLike::where('post_id', $post->id)->where('user_id', auth()->id())->first()) ? true : false;
        $post['created_at_string'] = $post->created_at->diffForHumans();

        return response([
            'success' => 'true',
            'data'    => $post
        ], 200);
    }


    /**
     * Create a post
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'image' => 'sometimes',
            'image.*' => 'image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        // dd($request->all());

        if (!$request->has('text') && !$request->has('image')){
            throw ValidationException::withMessages([
                'text' => 'Post can not be empty. Please write something or upload an image.'
            ]);
        }

        $user = auth('api')->user();

        // Extract Mentions
        $mentions = null;
        preg_match_all("/@\[([0-9]+):[A-Za-z0-9 .@$%^*!~\-=_+:;'\"]+]/",$request->text, $mentions);
        // return $mentions;
        // if there are mentions, log them and send notifications
        if (is_array($mentions) && is_array($mentions[0]) && is_array($mentions[1])){
            // return $mentions[1];
            $mentioned_ids = array_unique($mentions[1]);

            // delete user's own id if mentioned
            if (($key = array_search($user->id, $mentioned_ids)) !== false) {
                unset($mentioned_ids[$key]);
            }

            $mentioned_users = User::whereIn('id', $mentioned_ids)->get();
            // return $mentioned_users;
        }

        // die();

        // dd('Done');
        $post = new Post();
        $post->user_id = $user->id;
        $post->post = $request->has('text') ? $request->text : null;
        $post->media = null;
        $post->mentions = is_array($mentions) && is_array($mentions[1]) ? json_encode($mentions[1]) : json_encode([]);
        $post->shared_post_id = null;

        if ($request->has('image')){
            $files[] = $request->file('image');
            $names = [];

            //Process multiple images
            foreach($files as $file){
                $name = str_random(10) . '-' .Carbon::now()->toDayDateTimeString() . '-LEVIA' . '.' . $file->getClientOriginalExtension();
                $name = preg_replace('/[,:\ \t]/i', '-', $name);

                // Store the image
                // $request->file->move(public_path('uploads'), $name);
                Storage::disk('local')->put('public/post_media_photos/'.$name, file_get_contents($file));
                $names[] = $name;
            }

            $post->media = json_encode(['image' => $names]);
        }

        $post->save();

        if (isset($mentioned_users)){
            PostMention::dispatch($mentioned_users, $post);
        }

        return response([
            'success' => 'true',
            'data'    => $post
        ], 200);
    }


    /**
     * Update a post
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Post $post, Request $request)
    {
        $this->validate($request, [
            'image' => 'sometimes',
            'image.*' => 'image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        // dd($request->all());

        if (!$request->has('text') && !$request->has('image')){
            throw ValidationException::withMessages([
                'text' => 'Post can not be empty. Please write something or upload an image.'
            ]);
        }

        $user = auth('api')->user();

        if ($user->id !== $post->user_id){
            throw ValidationException::withMessages([
                'post' => 'Access forbidden. This post does not belong to this user.'
            ]);
        }


        // dd('Done');
        $post->post = $request->has('text') ? $request->text : null;

        if ($request->has('updated_images')){
            $images[] = $request->input('updated_images');
            $names = [];
            $current_file = 0;

            foreach($images as $image){
                if ("new" === strtolower($image)){ // If it's a new image, upload it and add to Array
                    if (isset($request->file('image')[$current_file])){
                        $file = $request->file('image')[$current_file];
                        // dd($file);
                        $name = str_random(10) . '-' .Carbon::now()->toDayDateTimeString() . '-LEVIA' . '.' . $file->getClientOriginalExtension();
                        $name = preg_replace('/[,:\ \t]/i', '-', $name);

                        Storage::disk('local')->put('public/post_media_photos/'.$name, file_get_contents($file));
                        $names[] = $name;
                    } else {
                        throw ValidationException::withMessages([
                            'updated_images' => '"new" is passed in updated_images, but image file ' . $current_file . ' does not exits on request.'
                        ]);
                    }
                    $current_file++;
                } else { // An old image is submitted, add it to the $names
                    $parts = explode('/', $image);
                    $lastPart = $parts[count($parts)-1];

                    // Check if the image actually is from this post
                    if (!image_exists_on_post($post, $lastPart)){
                        throw ValidationException::withMessages([
                            'updated_images' => $lastPart . ' is not a valid image for this post.'
                        ]);
                    }

                    $names[] = $lastPart;
                }
            }

            $post->media = json_encode(['image' => $names]);
        }

        // if ($request->has('image')){
        //     $files = $request->file('image');
        //     $names = [];
        //     // Process multiple images
        //     foreach($files as $file){
        //         $name = str_random(10) . '-' .Carbon::now()->toDayDateTimeString() . '-LEVIA' . '.' . $file->getClientOriginalExtension();
        //         $name = preg_replace('/[,:\ \t]/i', '-', $name);

        //         // Store the image
        //         Storage::disk('local')->put('public/post_media_photos/'.$name, file_get_contents($file));
        //         $names[] = $name;
        //     }

        //     $post->media = json_encode(['image' => $names]);
        // }

        $post->save();

        return response([
            'success' => 'true',
            'data'    => $post
        ], 200);
    }


    /**
     * Delete a post
     *
     * @return \Illuminate\Http\Response
     */

    public function destroy(Post $post)
    {
        $user = auth('api')->user();

        if ($user->id != $post->user_id){
            throw ValidationException::withMessages([
                'post' => 'Access forbidden. This post does not belong to this user.'
            ]);
        }

        $post->delete();

        return response([
            'success' => 'true',
            'message'    => "Post has been deleted"
        ], 200);

    }


    /**
     * Share a post
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function share(Request $request)
    {
        $this->validate($request, [
          'post_id' => 'required|exists:posts,id',
        ]);

        $user = auth('api')->user();

        $share_id = $request->post_id;

        // If the post itself is a shared post, share the original post insteed.
        $share = Post::findOrFail($share_id);
        if($share->shared_post_id !== null){
            $share_id = $share->shared_post_id;
            $share = Post::findOrFail($share_id);
        }

        $post = new Post();
        $post->user_id = $user->id;
        $post->post = $request->has('text') ? $request->text : null;
        $post->media = null;
        $post->shared_post_id = $share_id;

        $share->shares_count = $share->shares_count + 1;


        DB::transaction(function () use($post, $share) {
            $post->save();
            $share->save();
        });

        PostShareNotificationToAuthor::dispatch($share, $post);


        return response([
            'success' => 'true',
            'data'    => $post
        ], 200);
    }
}
