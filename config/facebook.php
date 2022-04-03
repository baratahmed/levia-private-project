<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application
    |--------------------------------------------------------------------------
    |
    | The facebook ID and secret from the developer's page
    |
    */

    'app' => [
        'id' => env('FACEBOOK_APP_ID'),
        'secret' => env('FACEBOOK_APP_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Registration Fields
    |--------------------------------------------------------------------------
    |
    | The name of the fields on the user model that need to be updated,
    | if null, they shall not be updated. (valid for name, first_name, last_name)
    |
    */

    'registration' => [
        'facebook_id' => env('FACEBOOK_ID_COLUMN', 'fb_user_no'),
        'email'       => env('EMAIL_COLUMN', 'user_email'),
        'password'    => env('PASSWORD_COLUMN', 'user_bio'),
        'first_name'  => env('FIRST_NAME_COLUMN', null),
        'last_name'   => env('LAST_NAME_COLUMN', null),
        'name'        => env('NAME_COLUMN', 'fb_profile_name'),
        'attach_role' => env('ATTACH_ROLE', null),
    ],
];
