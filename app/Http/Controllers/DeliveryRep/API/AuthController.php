<?php

namespace App\Http\Controllers\DeliveryRep\API;

use App\Http\Controllers\API\AuthController as APIAuthController;
use App\Models\RestAdmin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserDrStatus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request){
        $auth_controller = new APIAuthController();
        return $auth_controller->register($request, true);
    }

    public function postResetPassword(Request $request){
        $auth_controller = new APIAuthController();
        return $auth_controller->postResetPassword($request);
    }

    public function login(Request $request){
        $this->validate($request, [
            'phone' => 'required|min:10|max:14',
            'password' => 'required|min:8'
        ]);

        $full_number = $this->getFullNumber($request->phone);

        $user = User::where('contact_no', $full_number)->first();

        if (!$user){
            throw ValidationException::withMessages([
                'phone' => __('auth.no_account')
            ]);
        }

        if ( !$user->isDeliveryRep ){
            return response([
                'success' => false,
                'message' => 'User is not a Delivery Representative'
            ], 200);
        }

        // return $user;

        // return [
        //     'password' => $request->password,
        //     'hashed_password' => $user->password,
        //     'check' => Hash::check($request->password, $user->password)
        // ];

        if (Hash::check($request->password, $user->password)){
            $tokenBody = $user->createToken('Delivery Rep Personal Access Token');
            $token = $tokenBody->token;

            $token->expires_at =  Carbon::now()->addMonth(1);
            $token->save();

            if ($request->has('firebase_notification_token')){
                $user->firebase_notification_token = $request->firebase_notification_token;
                $user->save();
            }

            $is_accepting_orders = UserDrStatus::getOrCreate($user)->accepting_orders;

            return response([
                'success' => 'true',
                'user' => $user,
                'is_accepting_orders' => null === $is_accepting_orders ? "1" : $is_accepting_orders,
                'token' => $tokenBody->accessToken
            ], 200);
        } else {
            throw ValidationException::withMessages([
                'credentials' => __('auth.invalid_credentials')
            ]);
        }
    }

    private function getFullNumber($number){
        $prefix = '+880';
        $givenLength = strlen($number);
        $required = 14-$givenLength;
        $addFirst = substr($prefix, 0, $required);

        return $addFirst . $number;
    }

    public function changePassword(Request $request){
        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = auth('api')->user();

        if ( ! Hash::check($request->current_password, $user->password) ){
            return response([
                'success' => false,
                'message' => 'Your current password is incorrect.',
            ]);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return response([
            'success' => true,
            'message' => 'Your password has been updated.',
        ]);
        
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();

        return response()->json([
            'success' => 'true',
            'message' => 'Successfully logged out'
        ]);
    }
}
