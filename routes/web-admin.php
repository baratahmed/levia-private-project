<?php
// Routes for Admin Panel
// TODO: Admin panel middleware must be changed later

Route::get('admin/', ['uses' => 'Admin\AuthController@home', 'as' => 'adminPanel']);
Route::post('admin/login', ['uses' => 'Admin\AuthController@postLogin', 'as' => 'postLoginAdmin']);
Route::get('admin/logout', ['uses' => 'Admin\AuthController@logout', 'as' => 'adminLogout']);

Route::name('admin.')->prefix('admin')->middleware('auth:admin')->group(function() {
    Route::get('/trends', ['uses' => 'AdminPanelStaticController@trends', 'as' => 'trends']);
    Route::get('/promotions', ['uses' => 'AdminPanelStaticController@promotions', 'as' => 'promotions']);
    Route::get('/payments', ['uses' => 'AdminPanelStaticController@payments', 'as' => 'payments']);


    //User Blog Posts

    // Route::resource('/posts','PostController');
    Route::get('/posts',['uses' => 'PostController@index', 'as' => 'posts']);
    Route::get('/posts/details/{id}',['uses' => 'PostController@show', 'as' => 'posts.details']);
    Route::get('/posts/edit/{id}',['uses' => 'PostController@edit', 'as' => 'posts.edit']);
    Route::post('/posts/update/{id}',['uses' => 'PostController@update', 'as' => 'posts.update']);
    Route::get('/posts/delete/{id}',['uses' => 'PostController@destroy', 'as' => 'posts.delete']);



    Route::get('/users', ['uses' => 'AdminPanelStaticController@users', 'as' => 'users']);
    Route::get('/users/view/{user}', ['uses' => 'AdminPanelStaticController@ViewUser', 'as' => 'view_users']);
    Route::get('/users/view/{user}/ratings', ['uses' => 'AdminPanelStaticController@ViewUserRatings', 'as' => 'view_user_ratings']);

    Route::get('/business', ['uses' => 'AdminPanelStaticController@business', 'as' => 'business']);
    Route::get('/business/create', ['uses' => 'AdminPanelStaticController@CreateBusiness', 'as' => 'create_business']);
    Route::get('/business/view/{id}', ['uses' => 'AdminPanelStaticController@ViewBusiness', 'as' => 'view_business']);
    Route::get('/business/edit/{id}', ['uses' => 'AdminPanelStaticController@EditBusiness', 'as' => 'edit_business']);

    Route::get('/orders', ['uses' => 'AdminPanelStaticController@orders', 'as' => 'orders']);
    Route::get('/orders/view/{user}', ['uses' => 'AdminPanelStaticController@ViewOrders', 'as' => 'view_orders']);

    Route::get('/ratings', ['uses' => 'AdminPanelStaticController@ratings', 'as' => 'ratings']);
    Route::get('/ratings/of/{rest}', ['uses' => 'AdminPanelStaticController@ratingsOfRest', 'as' => 'ratingsOfRest']);
    Route::get('/settings', ['uses' => 'AdminPanelStaticController@settings', 'as' => 'settings']);
    Route::post('/settings', ['uses' => 'AdminPanelController@postSettings', 'as' => 'postSettings']);
    Route::get('/reservations', ['uses' => 'AdminPanelStaticController@reservations', 'as' => 'reservations']);
    Route::post('/reservations/accept', ['uses'=>'AdminPanelController@acceptReservation', 'as'=>'acceptReservation']);
    Route::get('/contacts', ['uses' => 'AdminPanelStaticController@contacts', 'as' => 'contacts']);

    Route::get('/offers', ['uses' => 'AdminPanelStaticController@offers', 'as' => 'offers']);
    Route::get('/offers/create', ['uses' => 'AdminPanelStaticController@createoffer', 'as' => 'createoffer']);
    Route::get('/offers/edit/{id}', ['uses' => 'AdminPanelStaticController@editOffer', 'as' => 'editOffer']);
    Route::post('/offers/create', ['uses' => 'AdminPanelController@addOffer', 'as' => 'postaddoffer']);
    Route::post('/offers/edit/{id}', ['uses' => 'AdminPanelController@editOffer', 'as' => 'postEditOffer']);
    Route::get('/offers/view/{id}', ['uses' => 'AdminPanelStaticController@ViewOffers', 'as' => 'view_offer']);

    // Admin panel post routes
    Route::post('/business/create', ['uses' => 'AdminPanelController@addRestaurant']);
    Route::put('/business/edit/{id}/details', ['uses' => 'AdminPanelController@updateRestaurantDetails', 'as' => 'edit_business_details']);
    Route::post('/business/edit/{id}/schedule', ['uses' => 'AdminPanelController@updateRestaurantSchedule', 'as' => 'edit_business_schedule']);
    Route::post('/business/edit/{id}/property', ['uses' => 'AdminPanelController@updateRestaurantProperty', 'as' => 'edit_business_property']);
    Route::post('/business/edit/{id}/addCategory', ['uses' => 'AdminPanelController@addCategory', 'as' => 'addCategory']);
    Route::post('/business/edit/{id}/editCategory', ['uses' => 'AdminPanelController@editCategory', 'as' => 'editCategory']);
    Route::post('/business/edit/{id}/addMenu', ['uses' => 'AdminPanelController@addMenu', 'as' => 'addMenu']);
    Route::post('/business/edit/{id}/editMenu', ['uses' => 'AdminPanelController@editMenu', 'as' => 'editMenu']);
    Route::post('/business/edit/{id}/deleteMenu', ['uses' => 'AdminPanelController@deleteMenu', 'as' => 'deleteMenu']);
    Route::post('/business/edit/{id}/toggleMenu', ['uses' => 'AdminPanelController@toggleMenu', 'as' => 'toggleMenu']);
    Route::post('/promotions/manage', ['uses' => 'AdminPanelController@managePromotion', 'as' => 'managePromotion']);
    Route::post('/business/{rest}/publisher', ['uses' => 'AdminPanelController@restaurantPublisher', 'as' => 'restaurantPublisher']);
    Route::post('/business/{rest}/deleteRestaurant', ['uses' => 'AdminPanelController@deleteRestaurant', 'as' => 'deleteRestaurant']);

    Route::delete('users/delete', ['uses' => 'AdminPanelController@deleteUser', 'as' => 'deleteUser']);
    Route::delete('offers/delete', ['uses' => 'AdminPanelController@deleteOffer', 'as' => 'deleteOffer']);


    // API calls
    Route::get('/food_categories/{id}', ['uses' => 'AdminPanelController@getFoodAndCategories', 'as' => 'getFoodAndCategories']);
    // Rating and Review API
    Route::get('rapi/restaurant/ratings/{restaurant}', ['uses'=>'RatingAndReviewController@getRestaurantRatingsAdmin', 'as' => 'getRestRatings']);
    Route::get('rapi/restaurant/food/ratings/{restaurant}', ['uses'=>'RatingAndReviewController@getFoodRatingsAdmin', 'as' => 'getFoodRatings']);
    Route::get('rapi/restaurant/food_categories/{restaurant}', ['uses'=>'RatingAndReviewController@getFoodAndCategoriesAdmin', 'as' => 'getFoodAndCategories']);

    // Rating and Review for Users API
    Route::get('rapi/user/ratings/{user}', ['uses'=>'RatingAndReviewController@getRestaurantRatingsUser', 'as' => 'getRestRatingsUser']);
    Route::get('rapi/user/food/ratings/{user}', ['uses'=>'RatingAndReviewController@getFoodRatingsUser', 'as' => 'getFoodRatingsUser']);
    Route::get('rapi/user/food_categories/{user}', ['uses'=>'RatingAndReviewController@getFoodAndCategoriesUser', 'as' => 'getFoodAndCategoriesUser']);
});
