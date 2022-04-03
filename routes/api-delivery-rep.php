<?php

use App\Http\Controllers\API\MessageBlockingController;
use App\Http\Controllers\DeliveryRep\API\MessageController;
use Illuminate\Support\Facades\Route;

// Route::post('/phone/submit', 'API\PhoneVerificationController@requestSms');
// Route::post('/phone/check', 'API\PhoneVerificationController@checkNumber');
// Route::post('/phone/code/submit', 'API\PhoneVerificationController@submitCode');

Route::post('/user/register', 'DeliveryRep\API\AuthController@register');
Route::post('/user/login', 'DeliveryRep\API\AuthController@login');
Route::post('/user/resetPassword', 'DeliveryRep\API\AuthController@postResetPassword');

Route::post('/login', 'DeliveryRep\API\AuthController@login');
Route::middleware(['multiauth:api', 'delivery_rep'])->post('/user/logout', 'DeliveryRep\API\AuthController@logout');

Route::middleware(['multiauth:api', 'delivery_rep'])->get('user', function(){
    return auth('api')->user();
});

Route::middleware(['multiauth:api', 'delivery_rep'])->group(function(){
    Route::post('change_password', ['uses' => 'DeliveryRep\API\AuthController@changePassword', 'as' => 'changePassword']);
    // User Information Related
    Route::get('/status', 'DeliveryRep\API\DeliveryRepController@getStatus');
    Route::post('/update_dr_info', 'DeliveryRep\API\DeliveryRepController@postUpdateStatus');

    // Delivery Related
    Route::get('/delivery_requests', 'DeliveryRep\API\DeliveryRequestController@getDeliveryRequests');
    Route::get('/history', 'DeliveryRep\API\DeliveryRequestController@getHistory');
    Route::get('/wallet', 'DeliveryRep\API\DeliveryRequestController@getWallet');
    Route::get('/delivery/{order}', 'DeliveryRep\API\DeliveryRequestController@getOrderDetails');
    Route::post('/delivery_requests/accept', 'DeliveryRep\API\DeliveryRequestController@acceptOrder');
    Route::post('/delivery_requests/reject', 'DeliveryRep\API\DeliveryRequestController@rejectOrder');
    Route::get('/current_deliveries', 'DeliveryRep\API\DeliveryRequestController@getCurrentDeliveries');

    Route::post('/current_deliveries/pick', 'DeliveryRep\API\DeliveryRequestController@pickOrder');
    Route::post('/current_deliveries/pick/confirm', 'DeliveryRep\API\DeliveryRequestController@pickOrderConfirm');
    Route::post('/current_deliveries/deliver', 'DeliveryRep\API\DeliveryRequestController@deliverOrder');
    Route::post('/current_deliveries/deliver/confirm', 'DeliveryRep\API\DeliveryRequestController@deliverOrderConfirm');

    // Messages
    Route::get('/user_short_profile', [MessageController::class, 'getUserShortProfile']);
    Route::post('/send_message', [MessageController::class, 'sendMessage']);
    Route::get('/conversations', [MessageController::class, 'getConversations']);
    Route::get('/conversations/{conversation}/messages', [MessageController::class, 'getMessages']);

    // Message Blocking
    Route::get('/message_blocked_users', [MessageBlockingController::class, 'blockedUsers']);
    Route::post('/message/block', [MessageBlockingController::class, 'blockUser']);
    Route::post('/message/unblock', [MessageBlockingController::class, 'unblockUser']);

    // Notifications
    Route::get('notifications', ['uses' => 'DeliveryRep\API\NotificationController@get', 'as' => 'get']);
});