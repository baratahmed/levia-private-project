<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }
    public function home(){
        return view('AdminPanel.Login');
    }

    public function postLogin(Request $r){
        $this->validate($r,[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (auth('admin')->attempt($r->only(['email', 'password']))){
            return redirect()->route('admin.trends');
        } else {
            return redirect()->back()->with('danger', 'Email or Password is invalid.');
        }
    }

    public function logout(){
        auth('admin')->logout();

        return redirect('admin')->with('danger', 'You have successfully logged out');
    }
}
