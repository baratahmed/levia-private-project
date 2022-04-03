<?php

namespace App\Http\Controllers\API;

use App\Jobs\Notifications\PostCommentMention;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\Notifications\SendPostCommentNotificationToAuthor;
use App\Jobs\Notifications\UpdateCommentDeletedNotification;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PostCommentController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
        $comments = PostComment::with('user:id,fb_profile_name,fb_profile_pic_url')->where('post_id', $post->id)->orderBy('id', 'desc')->paginate(10);

        foreach($comments as $comment){
          $comment->created_at_string = $comment->created_at->diffForHumans();
        }

        return response([
          'success' => 'true',
          'data'    => $comments,
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Post $post, Request $request)
    {
        $this->validate($request, [
          'comment' => 'required|min:2',
        ]);

        // $user = User::inRandomOrder()->first();
        $user = auth('api')->user();

        $comment = new PostComment([
            'user_id' => $user->id,
            'comment' => $request->comment
        ]);

        // Extract Mentions
        $mentions = null;
        preg_match_all("/@\[([0-9]+):[A-Za-z0-9 .@$%^*!~\-=_+:;'\"]+]/",$request->comment, $mentions);
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

        $comment->mentions = is_array($mentions) && is_array($mentions[1]) ? json_encode($mentions[1]) : json_encode([]);


        DB::transaction(function () use($post, $comment) {
            $post->comments()->save($comment);
            $post->comments_count++;
            $post->save();
        });

        SendPostCommentNotificationToAuthor::dispatch($post,$comment);
        if (isset($mentioned_users)){
            PostCommentMention::dispatch($mentioned_users, $post, $comment);
        }

        return response([
          'success' => 'true',
          'data'    => $comment,
        ], 200);

    }

    public function update(Post $post, PostComment $comment, Request $request){
        $user = auth('api')->user();

        // Does the comment belong to the authenticated user?
        if ( (int) $comment->user_id !== (int) $user->id){
            return response([
                'success' => 'false',
                'message'    => 'You don\'t have permission to perform this action',
            ], 422);
        }

        // Does the comment belong to the post given?
        if ( (int) $comment->post_id !==  (int) $post->id){
            return response([
                'success' => 'false',
                'message'    => 'Invalid post and comment combination',
            ], 422);
        }

        // Alright, mark the comment as deleted
        // Extract Mentions
        $mentions = null;
        preg_match_all("/@\[([0-9]+):[A-Za-z0-9 .@$%^*!~\-=_+:;'\"]+]/",$request->comment, $mentions);
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

        $comment->mentions = is_array($mentions) && is_array($mentions[1]) ? json_encode($mentions[1]) : json_encode([]);
        $comment->comment = $request->comment;
        $comment->save();

        return response([
            'success' => 'true',
            'message'    => 'Comment has been updated',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post, PostComment $comment)
    {
        $user = auth('api')->user();

        // return $comment;

        // Does the comment belong to the authenticated user?
        if ($comment->user_id !== $user->id){
            return response([
              'success' => 'false',
              'message'    => 'You don\'t have permission to perform this action',
            ], 422);
        }

        // Does the comment belong to the post given?
        if ($comment->post_id !== $post->id){
            return response([
              'success' => 'false',
              'message'    => 'Invalid post and comment combination',
            ], 422);
        }

        // Alright, mark the comment as deleted
        DB::transaction(function () use($post, $comment) {
            $comment->delete();
            $post->comments_count--;
            $post->save();
        });

        UpdateCommentDeletedNotification::dispatch($post);

        return response([
            'success' => 'true',
            'message'    => 'Comment has been deleted',
        ], 200);
    }
}
