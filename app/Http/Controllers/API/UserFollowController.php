<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserFollowController extends Controller
{
    public function postFollowAction(Request $request, User $profile){
        $this->validate($request, [
            'action' => 'required|in:FOLLOW,UNFOLLOW'
        ]);

        $user = auth('api')->user();

        // dd($user, $profile);

        $follow = UserFollow::where('user_id', $user->id)
            ->where('follow_id', $profile->id)
            ->first();

        if ($follow && $request->input('action') === "FOLLOW"){
            return response([
                'success' => false,
                'message' => "You already follow this user."
            ], 422);
        } else if ($follow && $request->input('action') === "UNFOLLOW"){
            $follow->delete();
            return response([
                'success' => true,
                'message' => "You have successfully unfollowed this user."
            ], 200);
        } else if (!$follow && $request->input('action') === "FOLLOW"){
            $follow = new UserFollow([
                'user_id' => $user->id,
                'follow_id' => $profile->id
            ]);
            $follow->save();
            return response([
                'success' => true,
                'message' => "You have successfully followed this user."
            ], 200);
        } else if (!$follow && $request->input('action') === "UNFOLLOW"){
            return response([
                'success' => true,
                'message' => "You didn't follow this user yet."
            ], 200);
        }

        return response([
            'success' => false,
            'message' => "Invalid Request"
        ], 422);
    }

    public function getMyFollowings($user = null)
    {
        $self_request = request()->is('api/my/*');
        if ($user === null && $self_request) {
            $user = auth('api')->user();
        } 
        else {
            $user = User::findOrFail($user);
        }

        $follows = UserFollow::where('user_id', $user->id)
            ->selectRaw('user_id,follow_id,user_id as follower_user_id,follow_id as following_user_id')
            ->with('following_user:id,fb_profile_name,fb_profile_pic_url,user_bio')
            ->get();

        $follows->map(function($follow){
            $follow['follow_status'] = my_follow_status(auth('api')->id(), $follow->following_user_id);
            $follow['number_of_reviews'] = number_of_ratings($follow->following_user_id);
            return $follow;
        });

        return response([
            'success' => true,
            'data' => [
                'following' => $follows
            ]
        ]);
    }

    public function getMyFollowers($user = null)
    {
        $self_request = request()->is('api/my/*');
        if ($user === null && $self_request) {
            $user = auth('api')->user();
        } 
        else {
            $user = User::findOrFail($user);
        }

        $follows = UserFollow::where('follow_id', $user->id)
            ->selectRaw('user_id, follow_id,user_id as follower_user_id,follow_id as following_user_id')
            ->with('follower_user:id,fb_profile_name,fb_profile_pic_url,user_bio')
            ->get();

        $follows->map(function($follow){
            $follow['follow_status'] = my_follow_status(auth('api')->id(), $follow->follower_user_id);
            $follow['number_of_reviews'] = number_of_ratings($follow->follower_user_id);
            return $follow;
        });

        return response([
            'success' => true,
            'data' => [
                'follower' => $follows
            ]
        ]);
    }

}
