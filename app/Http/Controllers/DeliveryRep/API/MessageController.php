<?php

namespace App\Http\Controllers\DeliveryRep\API;

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
            'user_id' => 'sometimes|exists:user_info,id',
            'rest_id' => 'sometimes|exists:rest_info,id',
        ]);

        $current_user = Auth::guard('api')->user();

        if ($request->has('user_id')){
            $user = User::find($request->input('user_id'));
        } else if ($request->has('rest_id')){
            $user = RestaurantInfo::find($request->input('rest_id'));
        }        

        $additional = [];
        if ($user->rest_or_user === "USER" && $user->isNormalUser){
            $additional['follwers'] = UserFollow::where('follow_id', $user->id)->count();
            $additional['reviews'] = RestaurantRatingReview::where('user_id', $user->id)->count() + FoodRatingReview::where('user_id', $user->id)->count();
        } else if ($user->rest_or_user === "REST"){
            $additional['reviews'] = RestaurantRatingReview::where('rest_id', $user->id)->count() + FoodRatingReview::where('rest_id', $user->id)->count();
        }

        $message_block = MessageBlock::where([
            'blocked_by' => $user->id,
            'blocked_by_type' => $user->rest_or_user,
            'blocked_user' => $current_user->id,
            'blocked_user_type' => 'USER',
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
        $user = Auth::guard('api')->user();
        

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
            $rest = RestaurantInfo::findOrFail($order->rest_id);
            $customer = User::findOrFail($order->user_id);

            // authorize
            if ( (int) $order->dr_id !== (int) $user->id){
                return response([
                    'success' => false,
                    'message' => 'This order does not belong to you',
                ]);
            }

            // Find or Create the Conversation
            if ($request->has('to') && strtoupper($request->input('to')) === "CUSTOMER"){ // Conversation between this restaurant and the DR
                // return ("TO DR");
                $conversation = Conversation::byOrderUserUser($order, $user, $customer);
            } else { // Conversation between this restaurant and the customer
                // return ("TO USER");
                $conversation = Conversation::byOrderRestUser($order, $rest, $user);
            }
        }
        else if ($request->has('conversation_id')){
            $conversation = Conversation::findOrFail($request->input('conversation_id'));

            // check if this conv belongs to the user
            if ( ! Conversation::hasUser($conversation, $user, "USER") ){
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

        $opponent_id = ((int)$conversation->user_id_1 === (int)$user->id && $conversation->user_id_1_type === "USER") ? $conversation->user_id_2 : $conversation->user_id_1;
        $opponent_type = ((int)$conversation->user_id_1 === (int)$user->id && $conversation->user_id_1_type === "USER") ? $conversation->user_id_2_type : $conversation->user_id_1_type;

        // Check if current user is blocked by the opponent
        $message_block = MessageBlock::where([
            'blocked_by' => $opponent_id,
            'blocked_by_type' => $opponent_type,
            'blocked_user' => $user->id,
            'blocked_user_type' => "USER",
        ])->first();
        if ($message_block){
            return response([
                'success' => false,
                'message' => 'You have been blocked from sending messages to this user.',
            ]);
        }

        // Found the Conversation, now Add the message

        $message = new Message([
            'conversation_id' => $conversation->id,
            'from_id' => $user->id,
            'from_user_type' => 'USER',
            'to_id' => $opponent_id,
            'to_user_type' => $opponent_type,
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

        SendMessageNotification::dispatch($user, "USER", $opponent_id, $opponent_type, $message, $conversation);

        return response([
            'success' => true,
            'data' => [
                'message' => $message,
                'conversation' => $conversation
            ],
        ]);
    }

    public function getConversations(){
        $user = Auth::guard('api')->user();
        $conversations = Conversation::byUser($user);
        Conversation::injectOpponentsAndMessageMeta($conversations, $user, 'USER');

        return $conversations;
    }

    public function getMessages(Conversation $conversation){
        $user = Auth::guard('api')->user();

        // Check if the conversation belongs to this user
        if ( Conversation::hasUser($conversation, $user, "USER") ){
            // This conversation belongs to this user
        } else {
            return response([
                'success' => false,
                'message' => 'This conversation does not belong to you.',
            ]);
        }

        $opponent = Conversation::getOpponentModel($conversation,$user,"USER");

        if (request()->has('after_message_id')){
            $after_message_id = request()->input('after_message_id');

            if (intval($after_message_id) === 0){
                $after_message_id = PHP_INT_MAX;
            }

            $messages = Message::where('conversation_id', $conversation->id)->where('id', '>', $after_message_id )->orderBy('id','desc')->get();
            Message::addPerspective($messages, $user, "USER");

            return $messages;
        } else {
            $messages = Message::where('conversation_id', $conversation->id)->orderBy('id','desc')->paginate(20);
            Message::addPerspective($messages, $user, "USER");
        }
        
        return response([
            'conversation' => $conversation,
            'user' => $user->only(explode(',','id,rest_or_user,rest_name,imageUrl,fb_profile_name,profile_picture')),
            'opponent' => $opponent->only(explode(',','id,rest_or_user,rest_name,imageUrl,fb_profile_name,profile_picture')),
            'is_message_blocked' => Blocking::is_message_blocked(
                "USER", // Current user type
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
