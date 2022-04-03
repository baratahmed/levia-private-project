<?php

use App\Http\Controllers\API\MessageBlockingController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\UserBlockingController;
use Illuminate\Http\Request;

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

Route::middleware(['multiauth:api', 'normal_user'])->get('/user', function (Request $request) {
    $user = $request->user();

    $user['following_count'] = \App\Models\UserFollow::where('user_id', $user->id)->count();
    $user['followers_count'] = \App\Models\UserFollow::where('follow_id', $user->id)->count();

    return $user;
});

// Phone Verification Related
Route::post('/phone/submit', 'API\PhoneVerificationController@requestSms');
Route::post('/phone/check', 'API\PhoneVerificationController@checkNumber');
Route::post('/phone/code/submit', 'API\PhoneVerificationController@submitCode');
Route::post('/user/register', 'API\AuthController@register');
Route::post('/user/login', 'API\AuthController@login');
Route::post('/user/resetPassword', 'API\AuthController@postResetPassword');

Route::get('/places', [\App\Http\Controllers\API\PlacesController::class, 'getPlaces']);

// Route::post('/user/recover', 'API\RecoverPasswordController@recover');
Route::middleware(['multiauth:api', 'normal_user'])->post('/user/logout', 'API\AuthController@logout');

Route::middleware(['multiauth:api', 'normal_user'])->name('api.')->group(function(){
    Route::post('change_password', ['uses' => 'API\AuthController@changePassword', 'as' => 'changePassword']);
    Route::get('home', ['uses' => 'API\HomeController@home', 'as' => 'home']);
    // Route::get('test', ['uses' => 'API\HomeController@test', 'as' => 'test']);
    Route::get('homeandothers', ['uses' => 'API\HomeController@homeWithAdditionalData', 'as' => 'homeandothers']);
    Route::get('restaurants', ['uses' => 'API\RestaurantController@getNearbyRestaurants', 'as' => 'getNearbyRestaurants']);
    Route::get('restaurants/sponsored', ['uses' => 'API\RestaurantController@getSponsored', 'as' => 'getSponsored']);
    Route::get('restaurants/offers', ['uses' => 'API\RestaurantController@getOffers', 'as' => 'getOffers']);
    Route::get('restaurant/{restaurant}', ['uses' => 'API\RestaurantController@getRestaurant', 'as' => 'getRestaurant']);
    Route::get('restaurant/{restaurant}/ratings', ['uses' => 'API\RestaurantController@getRestRatingAndReviews', 'as' => 'getRestRatingAndReviews']);
    Route::post('restaurant/{restaurant}/ratings/add', ['uses' => 'API\RestaurantController@postRestRatingAndReviews', 'as' => 'postRestRatingAndReviews']);
    Route::get('restaurant/{restaurant}/food/{food}/ratings', ['uses' => 'API\RestaurantController@getRestFoodRatingAndReviews', 'as' => 'getRestFoodRatingAndReviews']);
    Route::post('restaurant/{restaurant}/food/{food}/ratings/add', ['uses' => 'API\RestaurantController@postRestFoodRatingAndReviews', 'as' => 'postRestFoodRatingAndReviews']);
    Route::get('categoryandmenu', 'API\HomeController@getCategoryAndMenu');
    Route::get('search', 'API\HomeController@getSearch');
    Route::get('search/people', 'API\HomeController@getSearchPeople');
    Route::get('searchAdvanced', 'API\HomeController@getSearchAdvanced');

    // Bookmark, Map, Mobile Call related api
    Route::post('restaurant/{restaurant}/bookmark', ['uses' => 'API\RestaurantController@addBookmark', 'as' => 'addBookmark']);
    Route::delete('restaurant/{restaurant}/bookmark', ['uses' => 'API\RestaurantController@deleteBookmark', 'as' => 'deleteBookmark']);
    Route::post('restaurant/{restaurant}/food/{food}/bookmark', ['uses' => 'API\RestaurantController@addBookmarkFood', 'as' => 'addBookmarkFood']);
    Route::delete('restaurant/{restaurant}/food/{food}/bookmark', ['uses' => 'API\RestaurantController@deleteBookmarkFood', 'as' => 'deleteBookmarkFood']);
    Route::post('restaurant/{restaurant}/mapdirection', ['uses' => 'API\RestaurantController@mapDirection', 'as' => 'mapDirection']);
    Route::post('restaurant/{restaurant}/mobilecall', ['uses' => 'API\RestaurantController@mobileCall', 'as' => 'mobileCall']);

    // Wishlist
    Route::post('wishlist/add', ['uses' => 'API\WishlistController@addWish', 'as' => 'addWish']);
    Route::post('wishlist/{wish}/delete', ['uses' => 'API\WishlistController@deleteWish', 'as' => 'deleteWish']);
    Route::get('wishlist', ['uses' => 'API\WishlistController@get', 'as' => 'get']);

    // Reservation
    Route::get('reservations', ['uses' => 'API\ReservationController@get', 'as' => 'get']);
    Route::get('reservations/availableSeats', ['uses' => 'API\ReservationController@availableSeats', 'as' => 'availableSeats']);
    Route::post('reservations/make', ['uses' => 'API\ReservationController@make', 'as' => 'make']);
    Route::post('reservations/{reserve}/check_in', ['uses' => 'API\ReservationController@check_in', 'as' => 'check_in']);

    // Notifications
    Route::get('notifications', ['uses' => 'API\NotificationController@get', 'as' => 'get']);

    // User Related
    Route::post('user/edit', 'API\UserController@editUser');
    Route::post('user/contact_no/update', 'API\UserInfoUpdateController@requestForUpdate');
    Route::get('user/contact_no/requests', 'API\UserInfoUpdateController@getPendingRequests');
    Route::post('user/contact_no/update/{update}', 'API\UserInfoUpdateController@executeUpdateRequest');
    Route::get('my/ratings', 'API\UserController@getRestRatings');
    Route::get('my/foodratings', 'API\UserController@getFoodratings');
    Route::get('my/reviews', 'API\UserController@getRestReviews');
    Route::get('my/foodreviews', 'API\UserController@getFoodreviews');
    Route::get('my/bookmarks', 'API\UserController@getBookmarks');
    Route::get('my/bookmarksFood', 'API\UserController@getBookmarksFood');
    Route::get('my/followings', 'API\UserFollowController@getMyFollowings');
    Route::get('my/followers', 'API\UserFollowController@getMyFollowers');

    // Mention in Posts and Comments
    Route::get('mention/search', 'API\MentionController@searchKeyword');



    Route::get('user/ratingandreview', ['uses' => 'API\UserController@getRatingReviews']);


    Route::get('newsfeed', ['uses' => 'API\NewsFeedController@index']);

    // User Profile
    Route::get('profile/{user}', ['uses'=>'API\ProfileController@getProfile']);
    Route::get('profile/{user}/ratings', 'API\UserController@getRestRatings');
    Route::get('profile/{user}/foodratings', 'API\UserController@getFoodratings');
    Route::get('profile/{user}/reviews', 'API\UserController@getRestReviews');
    Route::get('profile/{user}/foodreviews', 'API\UserController@getFoodreviews');
    Route::post('profile/{profile}/follow', 'API\UserFollowController@postFollowAction');
    Route::get('profile/{profile}/followers', 'API\UserFollowController@getMyFollowers');
    Route::get('profile/{profile}/followings', 'API\UserFollowController@getMyFollowings');

    // Block Unblock User
    Route::post('profile/{profile}/block', ['uses'=>'API\UserBlockingController@blockUser']);
    Route::post('profile/{profile}/unblock', ['uses'=>'API\UserBlockingController@unblockUser']);
    Route::get('/blocked_users', [UserBlockingController::class, 'blockedUsers']);

    Route::resource('/posts', 'API\PostController')->only(['index', 'show', 'store', 'destroy']);
    Route::post('/posts/share', 'API\PostController@share');
    Route::post('/posts/{post}/update', 'API\PostController@update');
    Route::resource('/posts/{post}/comments', 'API\PostCommentController')->only(['index', 'store', 'destroy', 'update']);
    Route::resource('/posts/{post}/likes', 'API\PostLikeController')->only(['index', 'store']);

    Route::resource('/address', 'API\AddressController');
    // Route::post('/cart/add', 'API\CartController@addItem');
    // Route::post('/cart/add_bulk', 'API\CartController@addBulkItems');
    // Route::get('/cart', 'API\CartController@showCart');
    Route::post('/orders', 'API\OrdersController@placeOrder');
    Route::get('/orders', 'API\OrdersController@getOrders');
    Route::get('/orders/{order}', 'API\OrdersController@getOrderDetails');
    Route::get('/orders/{order}/delivery_code', 'API\OrdersController@getOrderDeliveryCode');


    // Messages
    Route::get('/user_short_profile', [MessageController::class, 'getUserShortProfile']);
    Route::post('/send_message', [MessageController::class, 'sendMessage']);
    Route::get('/conversations', [MessageController::class, 'getConversations']);
    Route::post('/conversation_details', [MessageController::class, 'getConversationDetails']);
    Route::get('/conversations/{conversation}/messages', [MessageController::class, 'getMessages']);


    // Message Blocking
    Route::get('/message_blocked_users', [MessageBlockingController::class, 'blockedUsers']);
    Route::post('/message/block', [MessageBlockingController::class, 'blockUser']);
    Route::post('/message/unblock', [MessageBlockingController::class, 'unblockUser']);
});

// Auth Related Routes

// Route::post('user/register', 'Auth\RegisterController@register');
// Route::post('user/login', 'Auth\LoginController@login');
// Route::post('user/logout', 'Auth\LoginController@logout');
