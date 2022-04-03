<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    public static function addPerspective($messages, $user, $user_type){
        foreach($messages as $message){
            if ( $message->from_id == $user->id && $message->from_user_type === $user_type){ // From id is the current user
                // To id is the opponent 
                $message->perspective = "OUTGOING";
            } else { // From id is the opponent, To id is the current user
                $message->perspective = "INCOMING";
            }
        }
    }

    public function getPerspective($user, $user_type){
        if ( $this->from_id == $user->id && $this->from_user_type === $user_type){ // From id is the current user
            // To id is the opponent 
            return "OUTGOING";
        } else { // From id is the opponent, To id is the current user
            return "INCOMING";
        }
    }

    public function scopeToUser($query, $user_id, $user_type){
        return $query->where('to_id', $user_id)->where('to_user_type', $user_type);
    }
    
    public function scopeOnlyUnseen($query){
        return $query->where('seen_status', 'UNSEEN');
    }
}
