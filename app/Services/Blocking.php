<?php
namespace App\Services;

use App\Models\MessageBlock;
use App\Models\RestUserBlock;
use App\Models\UserBlock;

class Blocking {
    public static function is_message_blocked($user_type, $user_id, $blocked_user_type, $blocked_user_id){
        // dump($user_type, $user_id, $blocked_user_type, $blocked_user_id);
        $block = MessageBlock::
            where('blocked_by', $user_id)
            ->where('blocked_by_type', $user_type)
            ->where('blocked_user', $blocked_user_id)
            ->where('blocked_user_type', $blocked_user_type)
            ->first();

        // $reverse_block = MessageBlock::
        //     where('blocked_by', $blocked_user_id)
        //     ->where('blocked_by_type', $blocked_user_type)
        //     ->where('blocked_user', $user_id)
        //     ->where('blocked_user_type', $user_type)
        //     ->first();

        // dd($block, $reverse_block);

        // if ($block || $reverse_block){
        if ($block){
            return true;
        }

        return false;
    }


    public static function is_user_blocked($myself, $opponent){
        $is_blocked = false;
    
        if ("REST" === get_user_type($myself) && "USER" === get_user_type($opponent)){
            $is_blocked = RestUserBlock::where('radmin_id', $myself->radmin_id)->where('user_id', $opponent->id)->exists();
        } else if ("USER" === get_user_type($myself) && "USER" === get_user_type($opponent)){
            $is_blocked = UserBlock::where('user_id', $myself->id)->where('blocked_user_id', $opponent->id)->exists();
        }
    
        return $is_blocked;
    }
}