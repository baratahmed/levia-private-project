<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MentionController extends Controller
{
    public function searchKeyword(Request $request)
    {
        $this->validate($request,[
            'query' => 'required|min:2'
        ]);

        $user = auth('api')->user();

        // In case we choose to sort by names
        // $following = $user->getFollowings();
        // $followers = $user->getFollowers();
        $users = User::where('fb_profile_name', "LIKE", "%{$request->input('query')}%")
            ->selectRaw('id, fb_profile_name as name')
            ->paginate(10);
        // First, search from followers and followings and keep the results on top

        return response([
            'success' => true,
            'data' => $users
        ], 200);
    }
}
