<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Conversation extends Model
{
    protected $guarded = [];

    public static function injectOpponents($conversations, $user, $user_type){
        $user_ids = [];
        $rest_ids = [];

        foreach($conversations as $conv){
            if ( $conv->user_id_1 == $user->id && $conv->user_id_1_type === $user_type){ // User 1 is the current user
                // User 2 is the opponent
                if ($conv->user_id_2_type === "REST"){ 
                    $rest_ids[] = $conv->user_id_2;
                } else { 
                    $user_ids[] = $conv->user_id_2; 
                }
            } else { // User 1 is the opponent, User 2 is the current user
                if ($conv->user_id_1_type === "REST"){ 
                    $rest_ids[] = $conv->user_id_1;
                } else { 
                    $user_ids[] = $conv->user_id_1; 
                }
            }
        }

        // Query Everything At Once
        $users = User::selectRaw("id,fb_profile_name,fb_profile_pic_url")->whereIn('id', $user_ids)->get();
        $rests = RestaurantInfo::selectRaw("id,rest_name,rest_image_url")->whereIn('id', $rest_ids)->get();

        // dd($users, $rests);
        
        // Now inject the appropriate opponents
        foreach($conversations as $conv){
            if ( $conv->user_id_1 == $user->id && $conv->user_id_1_type === $user_type){ // User 1 is the current user
                // User 2 is the opponent
                if ($conv->user_id_2_type === "REST"){ 
                    $conv->opponent = $rests->where('id',$conv->user_id_2)->first();
                } else { 
                    $conv->opponent = $users->where('id',$conv->user_id_2)->first();
                }
            } else { // User 1 is the opponent, User 2 is the current user
                if ($conv->user_id_1_type === "REST"){ 
                    $conv->opponent = $rests->where('id',$conv->user_id_1)->first();
                } else { 
                    $conv->opponent = $users->where('id',$conv->user_id_1)->first();
                }
            }

            // dd($conv->opponent);
        }
    }

    public static function injectMessageMeta($conversations, $user, $user_type){
        foreach($conversations as $conversation){
            // TODO: Scale it, remove n+1 queries
            $last_message = Message::where('conversation_id', $conversation->id)->orderBy('id', 'desc')->first();
            
            if ($last_message){
                $conversation['meta'] = [
                    'last_message' => $last_message->message !== null ? $last_message->message : "Sent an attachment",
                    'perspective' => $last_message->getPerspective($user, $user_type),
                    'seen_status' => $last_message->seen_status,
                ];
            }

        }
    }

    public static function injectOpponentsAndMessageMeta($conversations, $user, $user_type){
        static::injectMessageMeta($conversations, $user, $user_type);
        static::injectOpponents($conversations, $user, $user_type);
    }
    
    /**
     * Get the opponent model
     *
     * @param  Conversation $conv Conversation Model
     * @param  User|RestaurantInfo $user Current user model
     * @param  string $user_type Current user type
     * @return User|RestaurantInfo
     */
    public static function getOpponentModel($conv, $user, $user_type){
        if ( $conv->user_id_1 == $user->id && $conv->user_id_1_type === $user_type){ // User 1 is the current user
            // User 2 is the opponent
            if ($conv->user_id_2_type === "REST"){ 
                return RestaurantInfo::findOrFail($conv->user_id_2);
            } else { 
                return User::findOrFail($conv->user_id_2);
            }
        } else { // User 1 is the opponent, User 2 is the current user
            if ($conv->user_id_1_type === "REST"){ 
                return RestaurantInfo::findOrFail($conv->user_id_1);
            } else { 
                return User::findOrFail($conv->user_id_1);
            }
        }
    }

    public static function hasUser($conversation, $user, $user_type){
        return ($conversation->user_id_1 == $user->id && $conversation->user_id_1_type === $user_type) 
          || ($conversation->user_id_2 == $user->id && $conversation->user_id_2_type === $user_type);
    }

    // Get the conversation if exists, otherwise create a conversation
    public static function byOrderRestUser($order, $rest, $user, $create_if_not_exist = true){
        // dd($user);
        // DB::enableQueryLog();
        $conversation = Conversation::where([ // Restaurant Initiated Conversation
            'user_id_1' => $rest->id,
            'user_id_1_type' => 'REST',
            'user_id_2' => $user->id,
            'user_id_2_type' => 'USER',
            'order_id' => $order->id,
            'type' => 'ORDER_RELATED'
        ])->orWhere(function($query) use($rest, $user, $order){
            $query->where([ // User Initiated Conversation
                'user_id_2' => $rest->id,
                'user_id_2_type' => 'REST',
                'user_id_1' => $user->id,
                'user_id_1_type' => 'USER',
                'order_id' => $order->id,
                'type' => 'ORDER_RELATED'
            ]);
        })->first();
        // dd(DB::getQueryLog());

        if ( ! $conversation && $create_if_not_exist ){
            $conversation = new Conversation([
                'user_id_1' => $rest->id,
                'user_id_1_type' => 'REST',
                'user_id_2' => $user->id,
                'user_id_2_type' => 'USER',
                'order_id' => $order->id,
                'type' => 'ORDER_RELATED'
            ]);
            $conversation->save();
        }

        return $conversation;
    }

    // Get the conversation if exists, otherwise create a conversation
    public static function byRestAndUser($rest, $user, $create_if_not_exist = true){
        // dd($user);
        // DB::enableQueryLog();
        $conversation = Conversation::where([ // Restaurant Initiated Conversation
            'user_id_1' => $rest->id,
            'user_id_1_type' => 'REST',
            'user_id_2' => $user->id,
            'user_id_2_type' => 'USER',
            'type' => 'USER_TO_USER'
        ])->orWhere(function($query) use($rest, $user){
            $query->where([ // User Initiated Conversation
                'user_id_2' => $rest->id,
                'user_id_2_type' => 'REST',
                'user_id_1' => $user->id,
                'user_id_1_type' => 'USER',
                'type' => 'USER_TO_USER'
            ]);
        })->first();
        // dd(DB::getQueryLog());

        if ( ! $conversation && $create_if_not_exist ){
            $conversation = new Conversation([
                'user_id_1' => $rest->id,
                'user_id_1_type' => 'REST',
                'user_id_2' => $user->id,
                'user_id_2_type' => 'USER',
                'type' => 'USER_TO_USER'
            ]);
            $conversation->save();
        }

        return $conversation;
    }

    // Get the conversation if exists, otherwise create a conversation
    public static function byUserAndUser($opponent, $user, $create_if_not_exist = true){
        // dd($user);
        // DB::enableQueryLog();
        $conversation = Conversation::where([ // Restaurant Initiated Conversation
            'user_id_1' => $opponent->id,
            'user_id_1_type' => 'USER',
            'user_id_2' => $user->id,
            'user_id_2_type' => 'USER',
            'type' => 'USER_TO_USER'
        ])->orWhere(function($query) use($opponent, $user){
            $query->where([ // User Initiated Conversation
                'user_id_2' => $opponent->id,
                'user_id_2_type' => 'USER',
                'user_id_1' => $user->id,
                'user_id_1_type' => 'USER',
                'type' => 'USER_TO_USER'
            ]);
        })->first();
        // dd(DB::getQueryLog());

        if ( ! $conversation && $create_if_not_exist ){
            $conversation = new Conversation([
                'user_id_1' => $opponent->id,
                'user_id_1_type' => 'USER',
                'user_id_2' => $user->id,
                'user_id_2_type' => 'USER',
                'type' => 'USER_TO_USER'
            ]);
            $conversation->save();
        }

        return $conversation;
    }
    
    // Get the conversation if exists, otherwise create a conversation
    public static function byOrderUserUser($order, $rest, $user, $create_if_not_exist = true){
        // dd($user);
        // DB::enableQueryLog();
        $conversation = Conversation::where([ // Current User Initiated
            'user_id_1' => $rest->id,
            'user_id_1_type' => 'USER',
            'user_id_2' => $user->id,
            'user_id_2_type' => 'USER',
            'order_id' => $order->id,
            'type' => 'ORDER_RELATED'
        ])->orWhere(function($query) use($rest, $user, $order){
            $query->where([ // Opponent User Initiated Conversation
                'user_id_2' => $rest->id,
                'user_id_2_type' => 'USER',
                'user_id_1' => $user->id,
                'user_id_1_type' => 'USER',
                'order_id' => $order->id,
                'type' => 'ORDER_RELATED'
            ]);
        })->first();
        // dd(DB::getQueryLog());

        if ( ! $conversation && $create_if_not_exist ){
            $conversation = new Conversation([
                'user_id_1' => $rest->id,
                'user_id_1_type' => 'USER',
                'user_id_2' => $user->id,
                'user_id_2_type' => 'USER',
                'order_id' => $order->id,
                'type' => 'ORDER_RELATED'
            ]);
            $conversation->save();
        }

        return $conversation;
    }
    
    
    

    // Get the conversations only, don't create anything
    public static function byRestaurant($rest){
        // dd($user);
        // DB::enableQueryLog();
        $conversations = Conversation::where([ // Restaurant Initiated Conversation
            'user_id_1' => $rest->id,
            'user_id_1_type' => 'REST'
        ])->orWhere(function($query) use($rest){
            $query->where([ // User Initiated Conversation
                'user_id_2' => $rest->id,
                'user_id_2_type' => 'REST'
            ]);
        })->orderBy('id','desc')->paginate(20);

        return $conversations;
    }
    
    // Get the conversations only, don't create anything
    public static function byUser($user){
        // dd($user);
        // DB::enableQueryLog();
        $conversations = Conversation::where([ // Restaurant Initiated Conversation
            'user_id_1' => $user->id,
            'user_id_1_type' => 'USER'
        ])->orWhere(function($query) use($user){
            $query->where([ // User Initiated Conversation
                'user_id_2' => $user->id,
                'user_id_2_type' => 'USER'
            ]);
        })->orderBy('id','desc')->paginate(20);

        return $conversations;
    }

}
