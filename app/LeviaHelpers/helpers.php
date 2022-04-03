<?php

// Returns a random boolean

use App\Models\FoodRatingReview;
use App\Models\RestaurantInfo;
use App\Models\RestaurantRatingReview;
use App\Models\RestUserBlock;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserFollow;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

function random_boolean(){
    return random_int(1,100) % 2 === 1 ? true : false;
}

// Get a random day
// returns [Day ID, Day Name]
function getRandomDay(){
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thirsday', 'Friday', 'Saturday'];

    $random = random_int(1,7);

    return [$random, $days[$random-1]];
}

// Get a random boolean with Biased to True probability
function random_boolean_biased(){
    $int = random_int(1,10);

    if ($int % 2 == 0 || $int % 3 == 0){
        return true;
    }

    return false;
}

// Get a full number from 9-12 digit numbers || converts 01712345678, 1712345678, etc to +8801712345678
function getFullNumber($number){
    $prefix = '+880';
    $givenLength = strlen($number);
    $required = 14-$givenLength;
    $addFirst = substr($prefix, 0, $required);

    return $addFirst . $number;
}


/**
 * Send Firebase Cloud Messages Notification
 *
 * Firebase Cloud Messages - Send Notification
 *
 * @param $id The FIREBASE_NOTIFICATION_TOKEN of user who will receive the notification
 * @param $text The notification text
 * @param $payload Useful information/ids about the notification
 * @return boolean
 **/
function firebase_send_notification($id, $type = 'default', $payload = []){
    $data = array(
        // 'body' => $text,
        // 'payload' => json_encode($payload),
        'id' => isset($payload['notification_id']) ? $payload['notification_id'] : '', 
        'type' => $type,
        'title' => isset($payload['title']) ? $payload['title'] : '', 
        'sub_title' => isset($payload['sub_title']) ? $payload['sub_title'] : '', 
        'message' => isset($payload['message']) ? $payload['message'] : '', 
        'thumb' => isset($payload['thumb']) ? $payload['thumb'] : '', 
        'image' => isset($payload['image']) ? $payload['image'] : '', 
        'date' => Carbon::now(),
    );

    $notificationData = ['message' => $data];

    $fields = array(
        'registration_ids' 	=> [$id],
        'data' => $notificationData,  
        "priority" => "high",
        'sound' => "default"
    );
    
    
    $headers = array(
        'Authorization: key=AAAATc57oHk:APA91bFwEpAOwETKviH10Gc6MEp8B54Ulf7taAF6B-BjOjQ7KIUowI163uXDgVsZuvdoVXbdozqTqeQnRuw1TfIwRf1WPTmQpqE-w0J-PchSrmCvfTPlRIpYNGQSqCfeOSoshJfnfRKl',
        'Content-Type: application/json'
    );
                            
    #Send Reponse To FireBase Server	
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $result = curl_exec($ch );
    // echo json_encode($result);
    Log::info($result);
    curl_close( $ch );
}

function namesArrayToString($names=[], $additional_names = 0){
    if (0 < $additional_names){
        $names[count($names)-1] = $additional_names . ' ' . str_plural('other', $additional_names);
    }

    else if (count($names) === 1){
        return $names[0];
    } else if (count($names) === 2){
        return $names[0] . ' and ' . $names[1];
    } else if (count($names) > 2) {
        return 
            implode(
                ', ', 
                array_except(
                    $names, 
                    count($names)-1
                ) 
            ) 
            . ' and ' . 
            data_get($names, count($names)-1 )
        ;
    }
}

function image_exists_on_post($post, $imageName){
    $media = json_decode($post->media, true);
    $images = $media['image'];
    if ("string" === gettype($images)){
        return $images === $imageName;
    } else if ("array" === gettype($images)) {
        foreach($images as $image){
            if ($image === $imageName){
                return true;
            }
        }
    }

    return false;
}

/**
 * Extract array style string values into real array for parsing the request perfectly in laravel
 * 
 * Convert $request[ "key[]" ] => "value" to $request["key"] => ["value"]
 * and $request[ "key[property]" ] => "value" to $request["key"] => ["property"=>"value"]
 */
function extractStringToArray(Request $request){

    $generated_values = [];

    // Find out string values to use as array
    foreach ( $request->keys() as $key ){
        $matches = [];
        $result = preg_match("~(.*)\[(.*)\]~", $key, $matches);
        // dd($result, $matches);
        if ( $result === 1 ){
            if ( $matches[2] === "" ){
                $generated_values[$matches[1]] = $request->input($matches[0]);
            } else {
                $generated_values[$matches[1]][$matches[2]] = $request->input($matches[0]);
            }
        }
    }

    // var_dump($generated_values);

    // Keep existing array values, if exists
    foreach ( $generated_values as $key=>$value ){
        $generated_values[$key] = array_merge( is_array($request->input($key)) ? $request->input($key) : [] , is_array($value) ? $value : [$value]);
    }

    return $generated_values;
}

/**
 * Get the user type of the given model in "USER" or "REST"
 *
 * @param  User|RestaurantInfo $model
 * @return string "USER" or "REST"
 */
function get_user_type($model){
    // Checks based on the fact that there's a "user_type" field in User object, if that exists, it's a USER, otherwise REST
    return "REST" === data_get($model, 'user_type', 'REST') ? "REST" : "USER";
}


function my_follow_status($my_id, $user_id){
    return UserFollow::where('follow_id', $user_id)->where('user_id', $my_id)->exists();
}

function number_of_ratings($user_id){
    $ratings = RestaurantRatingReview::where('user_id', $user_id)
        ->selectRaw('count(id) as rest_rating, count(has_review) as rest_review')
        ->first();

    $foodratings = FoodRatingReview::where('user_id', $user_id)
        ->selectRaw('count(id) as food_rating, count(has_review) as food_review')
        ->first();

    return [
        'ratings_count' => (int) data_get($ratings, 'rest_rating', 0) + (int) data_get($ratings, 'food_rating', 0),
        'reviews_count' => (int) data_get($ratings, 'rest_review', 0) + (int) data_get($ratings, 'food_review', 0),
    ];
}

function number_of_ratings_blank(){
    return [
        'ratings_count' => 0,
        'reviews_count' => 0
    ];
}

function sendNotification($token, $payload=[], $type = "selected", $topic = null)
        {

            $server_key=env('FIREBASE_SERVER_KEY');

            // $type will come from parameters "selected" or "broadcast"

            $fcmUrl = 'https://fcm.googleapis.com/fcm/send' ;

            $notification = [
                'id' => isset($payload['id']) ? $payload['id'] : '',  // reservation/post/offer id comes from Job
                'type' => isset($payload['type']) ? $payload['type'] : '', // reservation/post/offer
                'title' => isset($payload['title']) ? $payload['title'] : '', 
                'sub_title' => isset($payload['sub_title']) ? $payload['sub_title'] : '', 
                'message' => isset($payload['message']) ? $payload['message'] : '', 
                'thumb' => isset($payload['thumb']) ? $payload['thumb'] : '', 
                'image' => isset($payload['image']) ? $payload['image'] : '', 
            ];

            $notificationData = ["message" => $notification];
            if ($type == 'selected') {
                $fcmNotification = [
                    'registration_ids' => [$token],
                    'data' => $notificationData
                ];

            } 
            else{
                $fcmNotification = [
                    'to' => '/topics/'.$topic,
                    'data' => $notificationData
                ];
            }


            $headers = [
                'Authorization: key=' . $server_key,
                'Content-Type: application/json'
            ];


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fcmUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
            $result = curl_exec($ch);
            curl_close($ch);

            Log::info ("Push Notification Sent: ". $result);
        } 