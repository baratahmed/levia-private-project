<?php

namespace App\Http\Controllers;

use App\Jobs\SendForgotPasswordEmail;
use App\Jobs\SendRegistrationEmail;
use App\Models\ContactFormData;
use App\Models\RestAdmin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RadminProfileCompleteness;
use App\Models\RestaurantInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:radmin')->except(['logout', 'index', 'postContactForm']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return view('pages.global.index');
        return view('theme_landing.home');
    }

    public function getRegister()
    {
        // return view('pages.global.rest-register');
        return view('theme_landing.pages.register');
    }

    public function getLogin(){
        // return view('pages.global.rest-login');
        return view('theme_landing.pages.login');
    }

    public function getForgotPassword(){
        return view('theme_landing.pages.forgot');
    }

    public function postForgotPassword(Request $r){
        $this->validate($r, [
            'email' => 'required|email|exists:rest_admins,email'
        ]);

        $token = str_random(50);

        // TODO: User may have existing reset tokens, we're not checking it here. In that case, all tokens might work. But once a token is used against a particular user, all remaining tokens will automatically get deleted.
        $reset = DB::table('rest_admins_password_resets')->insertGetId([
            'email' => $r->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $user = RestAdmin::where('email', $r->email)->firstOrFail();

        $link = env('APP_URL').'/resetPassword?token='.$token;


        SendForgotPasswordEmail::dispatch($link, $user, $r->email);

        return view('theme_landing.pages.postforgot');
    }

    public function getResetPassword(Request $r){
        $this->validate($r, [
            'token' => 'required|exists:rest_admins_password_resets,token'
        ]);

        $token = DB::table('rest_admins_password_resets')->where('token', $r->token)->first();

        $diff = Carbon::now()->diffInHours($token->created_at);

        if ($diff > 3){
            abort(404);
        }

        return view('theme_landing.pages.resetpassword', compact('token'));
    }

    public function postResetPassword(Request $r){
        $this->validate($r, [
            'token' => 'required|exists:rest_admins_password_resets,token',
            'password' => 'required|min:6|max:255',
            're-password' => 'required|same:password'
        ]);

        $token = DB::table('rest_admins_password_resets')->where('token', $r->token)->first();

        $diff = Carbon::now()->diffInHours($token->created_at);

        if ($diff > 3){
            abort(404);
        }

        $radmin = RestAdmin::where('email', $token->email)->firstOrFail();

        $radmin->password = bcrypt($r->password);
        $radmin->save();

        DB::table('rest_admins_password_resets')->where('email', $token->email)->delete();

        return view('theme_landing.pages.postreset');
    }

    public function getVerifyEmail(Request $r){
        $this->validate($r, [
            'token' => 'required|exists:rest_admins_password_resets,token'
        ]);

        $token = DB::table('rest_admins_password_resets')->where('token', $r->token)->first();

        $email = $token->email;

        $user = RestAdmin::where('email', $email)->firstOrFail();
        $user->email_verified = true;
        $user->save();


        DB::table('rest_admins_password_resets')->where('token', $r->token)->delete();

        return view('theme_landing.pages.emailverified', compact('user'));
    }

    public function postContactForm(Request $r){
        // dd($r->all());

        $this->validate($r, [
            'name' => 'required|max:50',
            'email' => 'required|email|max:100',
            'subject' => 'required|max:200',
            'message' => 'required'
        ]);

        $formdata = new ContactFormData([
            'name' => $r->name,
            'email' => $r->email,
            'subject' => $r->subject,
            'message' => $r->message
        ]);

        $formdata->save();

        return view('theme_landing.contactform');
    }

    public function postRegister(Request $r){
        $this->validate($r, [
            'email' => 'required|email|unique:rest_admins|max:255',
            'password' => 'required|min:6|max:255',
            're-password' => 'required|same:password'
        ]);

        // Create the user
        // $user = RestAdmin::insert([
        //     'email' => $r->email,
        //     'password' => bcrypt($r->password),
        //     'created_at' => Carbon::now(),
        //     'updated_at' => Carbon::now()
        // ]);

        $user = new RestAdmin([
            'email' => $r->email,
            'password' => bcrypt($r->password),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $user->save();

        // Generate Token and Store
        $token = str_random(50);
        $otp = random_int(100000,999999);
        DB::table('rest_admins_password_resets')->insertGetId([
            'email' => $user->email,
            'token' => $token,
            'otp' => $otp,
            'created_at' => Carbon::now()
        ]);

        // Send Registration Email
        $link = env('APP_URL').'/verifyEmail?token='.$token;

        SendRegistrationEmail::dispatch($link, $user, $otp);

        // Show him the second page of registration
        Auth::guard('radmin')->login($user);

        return redirect()->route('radmin.profile.addBusiness');

        // return redirect()->route('getLogin')->with('success', __('auth.regsuccess'));
    }

    public function postLogin(Request $r){
        $this->validate($r, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $email = $r->email;
        $password = $r->password;

        if(Auth::guard('radmin')->attempt(['email' => $email, 'password' => $password], true)){
            //if user already added a resturant, redirect to dashboard
            if(RestaurantInfo::where("radmin_id", Auth::guard('radmin')->user()->id)->first()){
                return redirect()->intended(route('radmin.dashboard'));
            }else{ 
                //else add restaurent
                return redirect("/addrestaurent");
            }
        }

        return redirect()->back()->withInput($r->only('email'))->with('danger', __('auth.wrongemailpass'));
    }


    public function logout(){
        Auth::guard('radmin')->logout();

        return redirect()->route('home');
    }
}
