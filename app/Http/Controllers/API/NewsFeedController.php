<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NewsFeed;

class NewsFeedController extends Controller
{
    public function index(){
        $feed = NewsFeed::with(['user' =>function($query){
            $query->select(['id','fb_profile_name','fb_profile_pic_url']);
        }])->orderBy('id', 'desc')->paginate(15);

        return response([
          'message' => 'success',
          'data'    => $feed,
        ], 200);
    }
}
