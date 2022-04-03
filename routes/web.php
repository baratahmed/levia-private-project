<?php

use App\Http\Controllers\GlobalController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Home Routes for Everyone (Public)

Route::get('/', ['uses'=>'HomeController@index', 'as'=>'home']);
Route::get('/home', ['uses'=>'HomeController@index', 'as'=>'home2']);
Route::get('/register', ['uses'=>'HomeController@getRegister', 'as'=>'getRegister']);
Route::post('/contact', ['uses'=>'HomeController@postContactForm', 'as'=>'postContactForm']);
Route::get('/login', ['uses'=>'HomeController@getLogin', 'as'=>'getLogin']);
Route::get('/login_alt', ['uses'=>'HomeController@getLogin', 'as'=>'login']);
Route::get('/forgotpassword', ['uses'=>'HomeController@getForgotPassword', 'as'=>'getForgotPassword']);
Route::get('/resetPassword', ['uses'=>'HomeController@getResetPassword', 'as'=>'getResetPassword']);
Route::get('/verifyEmail', ['uses'=>'HomeController@getVerifyEmail', 'as'=>'getVerifyEmail']);
Route::get('/logout', ['uses'=>'HomeController@logout', 'as'=>'logout']);

Route::post('/register', ['uses'=>'HomeController@postRegister', 'as'=>'postRegister']);
Route::post('/login', ['uses'=>'HomeController@postLogin', 'as'=>'postLogin']);
Route::post('/forgotpassword', ['uses'=>'HomeController@postForgotPassword', 'as'=>'postForgotPassword']);
Route::post('/resetPassword', ['uses'=>'HomeController@postResetPassword', 'as'=>'postResetPassword']);



// Globally Accessible Routes
Route::name('global.')->group(function(){
    Route::get('/food_category_list', ['uses' => 'GlobalController@foodCategoryList', 'as' => 'foodCategoryList']);
});

// Get Restaurant Food List as JSON
Route::get('restaurant/menu', [GlobalController::class, 'food_list'])->name('restaurant.foods');


Route::name('radmin.profile.')->middleware('auth:radmin')->group(function(){
    Route::get('/register/add-business', ['uses' => 'Restaurant\RegistrationController@add_business', 'as' => 'addBusiness']);
    Route::post('/register/add-business', ['uses' => 'Restaurant\RegistrationController@post_business', 'as' => 'postBusiness']);
    Route::get('/register/add-contact', ['uses' => 'Restaurant\RegistrationController@add_contact_person', 'as' => 'addContact']);
    Route::post('/register/add-contact', ['uses' => 'Restaurant\RegistrationController@post_contact_person', 'as' => 'postContact']);
});

// Routes for Restaurant Admin Panel
Route::name('radmin.')->middleware(['radmin_profile', 'auth:radmin'])->group(function(){
    Route::get('/trends', ['uses' => 'RestaurantStaticController@trends', 'as' => 'trends']);
    Route::get('/dashboard', ['uses'=>'RestaurantStaticController@dashboard', 'as'=>'dashboard']);
    Route::get('/settings', 'RestaurantController@viewSettingsPage')->name('settings');
    Route::get('/menu-details', 'RestaurantController@viewMenuDetailsPage')->name('menuDetails');
    Route::get('/ratings', ['uses'=>'RestaurantStaticController@ratings', 'as'=>'ratings']);
    Route::get('/addrestaurent', ['uses'=> 'RestaurantStaticController@addRestaurant', 'as'=> 'addrestaurent']);
    Route::get('/myorders', ['uses'=>'RestaurantStaticController@myorders', 'as'=>'myorders']);
    Route::get('/notification', ['uses'=>'RestaurantStaticController@notification', 'as'=>'notification']);
    Route::get('/payments', ['uses'=>'RestaurantStaticController@payments', 'as'=>'payments']);
    Route::get('/promotion', ['uses'=>'RestaurantStaticController@promotion', 'as'=>'promotion']);
    Route::get('/gallery', ['uses'=>'RestaurantStaticController@gallery', 'as'=>'gallery']);
    Route::get('/reservations', ['uses'=>'RestaurantStaticController@reservations', 'as'=>'reservations']);
    Route::post('/reservations/accept', ['uses'=>'RestaurantController@acceptReservation', 'as'=>'acceptReservation']);
    Route::get('/categoryList', ['uses' => 'RestaurantController@food_category_list', 'as' => 'addmenu']);

    Route::get('/offer', ['uses' => 'RestaurantStaticController@Offer', 'as' => 'offer']);
    Route::get('/offer/view/{id}', ['uses' => 'RestaurantStaticController@ViewOffer', 'as' => 'offer']);
    Route::get('/offer/edit/{id}', ['uses' => 'RestaurantStaticController@EditOffer', 'as' => 'edtoffer']);
    Route::get('/offer/delete/{id}', ['uses' => 'RestaurantController@deleteOffer', 'as' => 'deleteoffer']);
    Route::get('/createoffer', ['uses' => 'RestaurantStaticController@createOffer', 'as' => 'createoffer']);

    Route::post('/saverestinfo',['uses' => 'RestaurantController@saveRestaurantInfo', 'as' => 'saverestinfo']);
    Route::post('/saverestschedule',['uses' => 'RestaurantController@updateRestaurantSchedule', 'as' => 'saverestschedule']);
    Route::post('/saverestproperty',['uses' => 'RestaurantController@updateRestaurantProperty', 'as' => 'saverestproperty']);
    Route::post('/addcategory',['uses' => 'RestaurantController@addCategory', 'as' => 'addcategory']);
    Route::post('/editcategory',['uses' => 'RestaurantController@editcategory', 'as' => 'editcategory']);
    Route::post('/addmenu',['uses' => 'RestaurantController@addMenu', 'as' => 'addmenu']);
    Route::post('/editmenu',['uses' => 'RestaurantController@editMenu', 'as' => 'editmenu']);
    Route::post('/togglemenu',['uses' => 'RestaurantController@toggleMenu', 'as' => 'togglemenu']);
    Route::post('/deletemenu',['uses' => 'RestaurantController@deleteMenu', 'as' => 'deletemenu']);
    Route::post('/promotion/create', ['uses'=>'RestaurantController@createPromotion', 'as'=>'createPromotion']);
    Route::get('/categoryList', ['uses' => 'RestaurantController@food_category_list', 'as' => 'addmenu']);

    
    Route::resource('/posts', 'Restaurant\PostsController')->only(['index', 'store']);


    // Rating and Review API
    Route::get('rapi/restaurant/ratings', ['uses'=>'RatingAndReviewController@getRestaurantRatings', 'as' => 'getRestRatings']);
    Route::post('rapi/restaurant/ratings/reply', ['uses'=>'RatingAndReviewController@postRatingReply', 'as' => 'postRatingReply']);
    Route::get('rapi/restaurant/food/ratings', ['uses'=>'RatingAndReviewController@getFoodRatings', 'as' => 'getFoodRatings']);
    Route::post('rapi/restaurant/food/ratings/reply', ['uses'=>'RatingAndReviewController@postFoodRatingReply', 'as' => 'postFoodRatingReply']);
    Route::get('rapi/restaurant/food_categories', ['uses'=>'RatingAndReviewController@getFoodAndCategories', 'as' => 'getFoodAndCategories']);
});

// Route::post('/saverestschedule',['uses' => 'RestaurantController@updateRestaurantSchedule', 'as' => 'saverestschedule']);
// Route::post('/saverestproperty',['uses' => 'RestaurantController@updateRestaurantProperty', 'as' => 'saverestproperty']);
// Route::post('/saverestinfo', ['uses' => 'RestaurantController@saveRestaurantInfo', 'as' => 'saverestinfo']);
// Route::post('/addrestaurent', ['uses' => 'RestaurantController@addRestaurant', 'as' => 'addrestaurent']);


// Route::post('/saverestinfo', ['uses' => 'RestaurantController@saveRestaurantInfo', 'as' => 'saverestinfo']);
// Route::post('/addcategory', ['uses' => 'RestaurantController@addCategory', 'as' => 'addcategory']);
// Route::post('/addmenu', ['uses' => 'RestaurantController@addMenu', 'as' => 'addmenu']);

Route::post('/addoffer', ['uses' => 'RestaurantController@addOffer', 'as' => 'addoffer']);
Route::post('/editOffer', ['uses' => 'RestaurantController@editOffer', 'as' => 'editOffer']);


Route::get('/picture/{filetype}/{filename}', function ($filetype, $filename) {
    // Check if file exists in app/storage/file folder
    $file_path = storage_path() . '/app/public/' . $filetype . '/' . $filename;
    if (file_exists($file_path)) {
        // Send Download
        return Response::download($file_path, $filename, [
            'Content-Length: ' . filesize($file_path)
        ]);
    }
});

Route::get('/privacy', ['uses' => 'StaticPagesController@privacy', 'as' => 'privacy']);
Route::get('/terms', ['uses' => 'StaticPagesController@terms', 'as' => 'terms']);

// Route::get("test", "TestController@test");

Route::get('info', function(){
    Log::info("Info logged");
    return "logged";
});