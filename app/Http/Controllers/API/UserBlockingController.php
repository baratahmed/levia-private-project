<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserBlockingController extends Controller
{
    public function blockedUsers(){
        $user = auth('api')->user();

        $block = UserBlock::where('user_id', $user->id)
            ->with('blocked_user:id,fb_profile_name,fb_profile_pic_url')
            ->paginate();

        $block->map(function($b){
            $b['block_status'] = true;
            $b['number_of_reviews'] = number_of_ratings($b->blocked_user_id);
            return $b;
        });

        return response([
            'success' => true,
            'data' => [
                'blocked_users' => $block   
            ],
        ]);
    }

    public function blockUser(User $profile)
    {
        $user = auth('api')->user();

        DB::enableQueryLog();
        $block = UserBlock::where('user_id', $user->id)
            ->where('blocked_user_id', $profile->id)
            ->first();
        // return DB::getQueryLog();
        // return $block;

        $reverse_block = UserBlock::where('user_id', $profile->id)
            ->where('blocked_user_id', $user->id)
            ->first();

        // return [$block, $reverse_block];

        if ($block || $reverse_block){
            return response([
                'success' => false,
                'message' => "You can't perform this action."
            ], 422);
        }

        $block = new UserBlock([
            'user_id' => $user->id,
            'blocked_user_id' => $profile->id
        ]);

        $block->save();

        return response([
            'success' => true,
            'message' => "Block successful. This person can't follow or send send you messages anymore."
        ], 200);
    }

    public function unblockUser(User $profile)
    {
        $user = auth('api')->user();

        $block = UserBlock::where('user_id', $user->id)
            ->where('blocked_user_id', $profile->id)
            ->first();

        if (!$block){
            return response([
                'success' => false,
                'message' => "You can't perform this action."
            ], 422);
        }


        $block->delete();

        return response([
            'success' => true,
            'message' => "Unblock successful."
        ], 200);
    }
}
