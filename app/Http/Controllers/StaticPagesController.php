<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPagesController extends Controller
{
    public function privacy(){
        return view('theme_landing.pages.privacy');
    }
    
    
    public function terms(){
        return view('theme_landing.pages.terms');
    }
}
