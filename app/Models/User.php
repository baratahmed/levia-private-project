<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Danjdewhurst\PassportFacebookLogin\FacebookLoginTrait;
use Facebook\Facebook;
use League\OAuth2\Server\Exception\OAuthServerException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SMartins\PassportMultiauth\HasMultiAuthApiTokens;

class User extends Authenticatable
{
    use HasMultiAuthApiTokens, Notifiable, FacebookLoginTrait, SoftDeletes;

    public $table = 'user_info';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded=[];


    protected $appends=['profile_picture'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token','password'
    ];

    public function getProfilePicture(){
        if ($this->fb_profile_pic_url == 'default.jpg' || $this->fb_profile_pic_url == null || $this->fb_profile_pic_url == "-1"){
            return asset('storage/logo/default.jpg');
        }
        else if (substr($this->fb_profile_pic_url,0,5) === 'file:'){
            $filename = substr($this->fb_profile_pic_url,5);
            // return $filename;
            if (Storage::exists('public/propic/'.$filename)){
                return asset('storage/propic/'. $filename);
            } else {
                return asset('storage/logo/default.jpg');
            }
        }
        return $this->fb_profile_pic_url.'?width=80&height=80';
    }

    public function getProfilePictureAttribute(){
        return $this->getProfilePicture();
    }

    public function scopeNotDeleted(){
        return $this->deleted_at == null;
    }

    public function getIsDeliveryRepAttribute(){
        return $this->user_type === "DR";
    }

    public function getIsNormalUserAttribute(){
        return $this->user_type === "USER";
    }

    public function getRestOrUserAttribute(){
        return "USER";
    }

    // Overriding loginFacebook method from Trait, in order to customize it.
    public function loginFacebook(Request $request)
    {
        // dd($request->all());
        try {
            /**
             * Check if the 'fb_token' as passed.
             */
            if ($request->get('fb_token')) {

                /**
                 * Initialise Facebook SDK.
                 */
                $fb = new Facebook([
                    'app_id' => config('facebook.app.id'),
                    'app_secret' => config('facebook.app.secret'),
                    'default_graph_version' => 'v2.5',
                ]);
                $fb->setDefaultAccessToken($request->get('fb_token'));

                /**
                 * Make the Facebook request.
                 */
                $response = $fb->get('/me?locale=en_GB&fields=first_name,last_name,email,name');


                // Log::info('FB Response: '. json_encode($response));

                $fbUser = $response->getDecodedBody();
                // Log::info('FB User: '. json_encode($fbUser));

                // dd($fbUser);

                /**
                 * Check if the user has already signed up.
                 */
                $userModel = config('auth.providers.users.model');

                /**
                 * Create a new user if they haven't already signed up.
                 */
                $facebook_id_column = config('facebook.registration.facebook_id', 'facebook_id');
                $name_column        = config('facebook.registration.name', 'name');
                $first_name_column  = config('facebook.registration.first_name', 'first_name');
                $last_name_column   = config('facebook.registration.last_name', 'last_name');
                $email_column       = config('facebook.registration.email', 'email');
                $password_column    = config('facebook.registration.password', 'password');

                $user = $userModel::where($facebook_id_column, $fbUser['id'])->first();
                // Log::info("Levia User: ". json_encode($user));

                if (!$user) {
                    // Log::info("Registration Request: " . json_encode($request->all()));
                    if ($request->has(['action', 'contact_no']) && $request->get('action') == "register"){
                        $user = new $userModel();
                        $user->{$facebook_id_column} = $fbUser['id'];

                        if ($first_name_column) {
                            $user->{$first_name_column} = $fbUser['first_name'];
                        }
                        if ($last_name_column) {
                            $user->{$last_name_column} = $fbUser['last_name'];
                        }
                        if ($name_column) {
                            $user->{$name_column} = $fbUser['name'];
                        }


                        $user->fb_profile_pic_url = "https://graph.facebook.com/".$fbUser['id']."/picture";
                        $user->{$email_column}    = array_key_exists('email', $fbUser) && $fbUser['email'] != null ? $fbUser['email'] : $request->get('email');
                        $user->{$password_column} = uniqid('fb_', true); // Random password.
                        $user->contact_no = $request->get('contact_no');
                        $user->save();

                        /**
                         * Attach a role to the user.
                         */
                        if (!is_null(config('facebook.registration.attach_role'))) {
                            $user->attachRole(config('facebook.registration.attach_role'));
                        }
                    } else {
                        return null;
                    }
                }

                return $user;
            }
        } catch (\Exception $e) {
            Log::info('Error Exception: '. $e);
            throw OAuthServerException::accessDenied($e->getMessage());


        }
        return null;
    }

    public function getFollowers(){
        return UserFollow::where('follow_id', $this->id)->selectRaw('user_id as follower_user_id')->get();
    }

    public function getFollowings(){
        return UserFollow::where('user_id', $this->id)->selectRaw('follow_id as following_user_id')->get();
    }
}
