<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestUserBlock;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserBlockingController extends Controller
{
    public function blockedUsers(){
        $user = auth('api_restaurant')->user();
        $rest = $user->restaurant;

        $block = RestUserBlock::where('radmin_id', $user->id)
            ->with('user:id,fb_profile_name,fb_profile_pic_url')
            ->paginate();

        $block->map(function($b){
            $b['block_status'] = true;
            $b['number_of_reviews'] = number_of_ratings($b->user_id);
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
        $user = auth('api_restaurant')->user();
        $rest = $user->restaurant;

        DB::enableQueryLog();
        $block = RestUserBlock::where('radmin_id', $user->id)
            ->where('user_id', $profile->id)
            ->first();
        // return DB::getQueryLog();
        // return $block;

        // return [$block, $reverse_block];

        if ($block){
            return response([
                'success' => false,
                'message' => "You can't perform this action."
            ], 422);
        }

        $block = new RestUserBlock([
            'radmin_id' => $user->id,
            'user_id' => $profile->id
        ]);

        $block->save();

        return response([
            'success' => true,
            'message' => "Block successful. This person can't order or send you messages anymore."
        ], 200);
    }

    public function unblockUser(User $profile)
    {
        $user = auth('api_restaurant')->user();

        $block = RestUserBlock::where('radmin_id', $user->id)
            ->where('user_id', $profile->id)
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
