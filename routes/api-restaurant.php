<?php

use App\Http\Controllers\Restaurant\API\BankAccountController;
use App\Http\Controllers\Restaurant\API\GalleryController;
use App\Http\Controllers\Restaurant\API\InsightsController;
use App\Http\Controllers\Restaurant\API\MenuController;
use App\Http\Controllers\Restaurant\API\MessageBlockingController;
use App\Http\Controllers\Restaurant\API\MessageController;
use App\Http\Controllers\Restaurant\API\NotificationController;
use App\Http\Controllers\Restaurant\API\OfferController;
use App\Http\Controllers\Restaurant\API\OrderController;
use App\Http\Controllers\Restaurant\API\PartnerController;
use App\Http\Controllers\Restaurant\API\ReviewsController;
use App\Http\Controllers\Restaurant\API\SettingsController;
use App\Http\Controllers\Restaurant\API\TimelineController;
use App\Http\Controllers\Restaurant\API\UserBlockingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware(['api'])->get('/user', function (Request $request) {
//     return 'user';
// });

Route::middleware(['multiauth:api_restaurant'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['multiauth:api_restaurant'])->post('/user/logout', 'Restaurant\API\AuthController@logout');

Route::post('/login', 'Restaurant\API\AuthController@login');
Route::post('/register', 'Restaurant\API\AuthController@register');

Route::middleware(['multiauth:api_restaurant'])->group(function(){
    Route::post('change_password', ['uses' => 'Restaurant\API\AuthController@changePassword', 'as' => 'changePassword']);

    Route::get('/partner_info', [PartnerController::class, 'partnerInfo']);
    Route::post('/partner_info/restaurant_info', [PartnerController::class, 'saveRestaurantInfo']);
    Route::post('/partner_info/contact_info', [PartnerController::class, 'saveContactInfo']);
    Route::post('/partner_info/restaurant_properties', [PartnerController::class, 'updateRestaurantProperty']);
    Route::post('/partner_info/restaurant_schedule', [PartnerController::class, 'updateRestaurantSchedule']);
    Route::post('/partner_info/contact_information', [PartnerController::class, 'saveContactInfo']);

    // Menu Related
    Route::get('/category_and_menu', [MenuController::class, 'getCategoriesAndMenus']);
    Route::get('/system_categories', [MenuController::class, 'system_categories']);
    Route::post('/add_category', [MenuController::class, 'addCategory']);
    Route::post('/edit_category', [MenuController::class, 'editCategory']);
    Route::delete('/delete_category', [MenuController::class, 'deleteCategory']);
    Route::post('/add_menu', [MenuController::class, 'addMenu']);
    Route::post('/edit_menu', [MenuController::class, 'editMenu']);
    Route::post('/toggle_menu', [MenuController::class, 'toggleMenu']);
    Route::delete('/delete_menu', [MenuController::class, 'deleteMenu']);

    // Offer Related
    Route::get('/offers', [OfferController::class, 'myOffers']);
    Route::get('/offers/{offer}', [OfferController::class, 'viewOffer']);
    Route::post('/offers/{offer}', [OfferController::class, 'editOffer']);
    Route::post('/offers/{offer}/relaunch', [OfferController::class, 'relaunchOffer']);
    Route::get('/offer_types', [OfferController::class, 'offer_types']);
    Route::post('/add_offer', [OfferController::class, 'add_offer']);
    Route::delete('/delete_offer/{offer}', [OfferController::class, 'delete_offer']);

    // Reviews Related
    Route::get('/restaurant_reviews', [ReviewsController::class, 'getRestaurantReviews']);
    Route::get('/restaurant_food_reviews/{food}', [ReviewsController::class, 'getRestaurantFoodReviews']);

    // Is Receiving Orders
    Route::post('/settings/receiving_orders', [SettingsController::class, 'setIsReceivingOrders']);
    
    // Block/Unblock User
    Route::post('/profile/{profile}/block', [UserBlockingController::class, 'blockUser']);
    Route::post('/profile/{profile}/unblock', [UserBlockingController::class, 'unblockUser']);
    Route::get('/blocked_users', [UserBlockingController::class, 'blockedUsers']);

    // Insights
    Route::get('/insights', [InsightsController::class, 'getInsights']);
    
    // Timeline
    Route::get('/timeline', [TimelineController::class, 'getTimeline']);
    Route::get('/timeline/comments', [TimelineController::class, 'getComments']);
    Route::get('/timeline/likes', [TimelineController::class, 'getLikes']);
    Route::post('/create_post', [TimelineController::class, 'postCreatePost']);

    // Orders
    Route::get('/orders', [OrderController::class, 'getOrders']);
    Route::get('/orders/{order}', [OrderController::class, 'getOrderDetails']);
    Route::get('/orders/{order}/track_dr', [OrderController::class, 'getTrackDR']);
    Route::get('/orders/{order}/pickup_code', [OrderController::class, 'getOrderPickupCode']);
    Route::post('/orders/accept', [OrderController::class, 'acceptOrder']);
    Route::post('/orders/cancel', [OrderController::class, 'cancelOrder']);
    Route::post('/orders/ready', [OrderController::class, 'readyOrder']);

    // Messages
    Route::get('/user_short_profile', [MessageController::class, 'getUserShortProfile']);
    Route::post('/send_message', [MessageController::class, 'sendMessage']);
    Route::get('/conversations', [MessageController::class, 'getConversations']);
    Route::get('/conversations/{conversation}/messages', [MessageController::class, 'getMessages']);


    // Message Blocking
    Route::get('/message_blocked_users', [MessageBlockingController::class, 'blockedUsers']);
    Route::post('/message/block', [MessageBlockingController::class, 'blockUser']);
    Route::post('/message/unblock', [MessageBlockingController::class, 'unblockUser']);


    // Banks
    Route::resource('/bank_accounts', "Restaurant\API\BankAccountController")->only(['store', 'index']);
    Route::post('/bank_accounts/{account}', [BankAccountController::class, 'update']);
    Route::delete('/bank_accounts/{account}', [BankAccountController::class, 'destroy']);

    // Gallery (Images)
    Route::get('/gallery', [GalleryController::class, 'getImages']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'get']);
});