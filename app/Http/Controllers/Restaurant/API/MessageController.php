<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\Notifications\SendMessageNotification;
use App\Models\Conversation;
use App\Models\FoodRatingReview;
use App\Models\Message;
use App\Models\MessageBlock;
use App\Models\Order;
use App\Models\RestaurantInfo;
use App\Models\RestaurantRatingReview;
use App\Models\User;
use App\Models\UserFollow;
use App\Services\Blocking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function getUserShortProfile(Request $request){
        $this->validate($request, [
            'user_id' => 'required|exists:user_info,id',
        ]);

        $rest_user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $rest_user->id)->first();
        $user = User::find($request->input('user_id'));

        $additional = [];
        if ($user->isNormalUser){
            $additional['follwers'] = UserFollow::where('follow_id', $user->id)->count();
            $additional['reviews'] = RestaurantRatingReview::where('user_id', $user->id)->count() + FoodRatingReview::where('user_id', $user->id)->count();
        }

        $message_block = MessageBlock::where([
            'blocked_by' => $user->id,
            'blocked_by_type' => 'USER',
            'blocked_user' => $rest->id,
            'blocked_user_type' => 'REST',
        ])->first();

        return response([
            'success' => true,
            'data' => [
                'user' => $user,
                'additional' => $additional,
                'is_blocked' => $message_block !== null
            ],
        ]);
    }

    public function sendMessage(Request $request){
        $this->validate($request, [
            'order_id' => 'sometimes|exists:orders,id',
            'conversation_id' => 'sometimes|exists:conversations,id',
            'image' => 'sometimes|file|mimes:jpeg,jpg,png'
        ]);

        // $order = Order::findOrFail($request->input('order_id'));
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();
        

        if ( ! $request->has('message') && ! $request->has('image') ){
            return response([
                'success' => false,
                'message' => 'Please provide a text or image message.',
            ]);
        }

        // Order ID means, the restaurant is initiating a conversation
        if ($request->has('order_id')){
            $this->validate($request, [
                'to' => 'required',
            ]);
            
            $order = Order::find($request->input('order_id'));
            $customer = User::findOrFail($order->user_id);
            $dr = User::findOrFail($order->dr_id);

            // authorize
            if ( (int) $order->rest_id !== (int) $rest->id){
                return response([
                    'success' => false,
                    'message' => 'This order does not belong to you',
                ]);
            }

            // If he wants to send to the DR, but DR is null
            if ( strtoupper($request->input('to')) === 'DR' && $order->dr_id === null){
                return response([
                    'success' => false,
                    'message' => 'DR has not been assigned to this order yet.',
                ]);
            }

            // Find or Create the Conversation
            if ($request->has('to') && strtoupper($request->input('to')) === "DR"){ // Conversation between this restaurant and the DR
                // return ("TO DR");
                $conversation = Conversation::byOrderRestUser($order, $rest, $dr);
            } else { // Conversation between this restaurant and the customer
                // return ("TO USER");
                $conversation = Conversation::byOrderRestUser($order, $rest, $customer);
            }
        }
        else if ($request->has('conversation_id')){
            $conversation = Conversation::findOrFail($request->input('conversation_id'));

            // check if this conv belongs to the user
            if ( ! Conversation::hasUser($conversation, $rest, "REST") ){
                return response([
                    'success' => false,
                    'data' => 'This conversation does not belong to you.',
                ]);
            }
        }
        else {
            return response([
                'success' => false,
                'message' => 'Please provide the associated order_id with the message.',
            ]);
        }

        // return $conversation;
        // Check if current user is blocked by the opponent
        $message_block = MessageBlock::where([
            'blocked_by' => $user->id,
            'blocked_by_type' => 'USER',
            'blocked_user' => $rest->id,
            'blocked_user_type' => 'REST',
        ])->first();
        if ($message_block){
            return response([
                'success' => false,
                'message' => 'You have been blocked from sending messages to this user.',
            ]);
        }

        // Found the Conversation, now Add the message
        $opponent_id = ((int)$conversation->user_id_1 === (int)$rest->id && $conversation->user_id_1_type === "REST") ? $conversation->user_id_2 : $conversation->user_id_1;
        $message = new Message([
            'conversation_id' => $conversation->id,
            'from_id' => $rest->id,
            'from_user_type' => 'REST',
            'to_id' => $opponent_id,
            'to_user_type' => 'USER',
        ]);

        if ($request->has('message')){
            $message->message = $request->input('message');
        }

        if ($request->has('image')){
            $file = $request->file('image');
            $image = \Image::make($file)->encode('jpg');
            $name = $rest->id . "-" . \Illuminate\Support\Str::random(20) . "-" . Carbon::now()->getTimestamp() . ".jpg";
            $name = str_replace(" ", "_", $name);

            Storage::disk('public')->put( 'message/'.$name, $image);

            $message->attachments = '[{"src":"message/'.$name.'"}]';
        }

        $message->save();

        SendMessageNotification::dispatch($user, "REST", $opponent_id, "USER", $message, $conversation);

        return response([
            'success' => true,
            'data' => [
                'message' => $message,
                'conversation' => $conversation
            ],
        ]);
    }


    public function getConversations(){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();
        $conversations = Conversation::byRestaurant($rest);
        Conversation::injectOpponentsAndMessageMeta($conversations, $rest, 'REST');

        return $conversations;
    }
    
    public function getMessages(Conversation $conversation){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        // Check if the conversation belongs to this user
        if ( Conversation::hasUser($conversation, $rest, "REST") ){
            // This conversation belongs to this user
        } else {
            return response([
                'success' => false,
                'message' => 'This conversation does not belong to you.',
            ]);
        }

        $opponent = Conversation::getOpponentModel($conversation,$rest,"REST");

        if (request()->has('after_message_id')){
            $after_message_id = request()->input('after_message_id');

            if (intval($after_message_id) === 0){
                $after_message_id = PHP_INT_MAX;
            }

            $messages = Message::where('conversation_id', $conversation->id)->where('id', '>', $after_message_id )->orderBy('id','desc')->get();
            Message::addPerspective($messages, $rest, "REST");

            return $messages;
        } else {
            $messages = Message::where('conversation_id', $conversation->id)->orderBy('id','desc')->paginate(20);
            Message::addPerspective($messages, $rest, "REST");
        }
        
        return response([
            'conversation' => $conversation,
            'user' => $rest->only(explode(',','id,rest_or_user,rest_name,imageUrl,fb_profile_name,profile_picture')),
            'opponent' => $opponent->only(explode(',','id,rest_or_user,rest_name,imageUrl,fb_profile_name,profile_picture')),
            'is_message_blocked' => Blocking::is_message_blocked(
                "REST", // Current user type
                $user->id, // Current user id
                get_user_type($opponent) , // Opponent user type
                $opponent->id // Opponent user or rest id
            ),
            'is_user_blocked' => Blocking::is_user_blocked($user, $opponent),
            'number_of_reviews' => "USER" === get_user_type($opponent) ? number_of_ratings($opponent->id) : number_of_ratings_blank(),
            'messages' => $messages,
        ]);
        
    }
}
