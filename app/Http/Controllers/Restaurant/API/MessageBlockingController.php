<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MessageBlock;

class MessageBlockingController extends Controller
{
    public function blockedUsers(){
        $user = auth('api_restaurant')->user();
        $rest = $user->restaurant;

        $block = MessageBlock::
            where('blocked_by', $rest->id)
            ->where('blocked_by_type', 'REST')
            ->with([
                'blocked_user_instance:id,fb_profile_name,fb_profile_pic_url',
                'blocked_restaurant_instance:id,rest_name,rest_image_url',
                ])
            ->paginate();

        // Patch: Remove 'REST' or "rest" instances from the collection if that's not the blocked_user_type
        $block->map(function($row){
            if ($row['blocked_user_type'] === 'REST'){
                unset($row['blocked_user_instance']);
            } else {
                unset($row['blocked_restaurant_instance']);
            }

            return $row;
        });

        return response([
            'success' => true,
            'data' => [
                'message_blocked_users' => $block   
            ],
        ]);
    }

    public function blockUser(Request $request)
    {
        $this->validate($request, [
            'blocked_user_type' => 'required|in:USER,REST',
            'blocked_user_id' => 'required|integer'
        ]);

        $user = auth('api_restaurant')->user();
        $rest = $user->restaurant;

        $blocked_user_type = $request->input('blocked_user_type', 'REST');
        $blocked_user_id = $request->input('blocked_user_id');

        // DB::enableQueryLog();
        $block = MessageBlock::
            where('blocked_by', $rest->id)
            ->where('blocked_by_type', 'REST')
            ->where('blocked_user', $blocked_user_id)
            ->where('blocked_user_type', $blocked_user_type)
            ->first();
        // return DB::getQueryLog();
        // return $block;

        $reverse_block = MessageBlock::
            where('blocked_by', $blocked_user_id)
            ->where('blocked_by_type', $blocked_user_type)
            ->where('blocked_user', $rest->id)
            ->where('blocked_user_type', 'REST')
            ->first();

        // return [$block, $reverse_block];

        if ($block || $reverse_block){
            return response([
                'success' => false,
                'message' => "You can't perform this action."
            ], 422);
        }

        $block = new MessageBlock([
            'blocked_by' => $rest->id,
            'blocked_by_type' => 'REST',
            'blocked_user' => $blocked_user_id,
            'blocked_user_type' => $blocked_user_type,
        ]);

        $block->save();

        return response([
            'success' => true,
            'message' => "Block successful. This person can't follow or send send you messages anymore."
        ], 200);
    }

    public function unblockUser(Request $request)
    {
        $this->validate($request, [
            'blocked_user_type' => 'required|in:USER,REST',
            'blocked_user_id' => 'required|integer'
        ]);

        $user = auth('api_restaurant')->user();
        $rest = $user->restaurant;

        $blocked_user_type = $request->input('blocked_user_type', 'REST');
        $blocked_user_id = $request->input('blocked_user_id');

        $block = MessageBlock::
            where('blocked_by', $rest->id)
            ->where('blocked_by_type', 'REST')
            ->where('blocked_user', $blocked_user_id)
            ->where('blocked_user_type', $blocked_user_type)
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