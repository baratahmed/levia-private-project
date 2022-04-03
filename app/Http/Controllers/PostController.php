<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $posts = Post::paginate(100);
        return view('AdminPanel.Posts.Posts', compact('posts'));

    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        $post = Post::findOrfail($id);
        return view('AdminPanel.Posts.PostsDetails', compact('post'));
        
    }


    public function edit($id)
    {
        $post = Post::findOrfail($id);
        return view('AdminPanel.Posts.PostsEdit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        // dd(strlen($request->created_at));

        $validatedData = $request->validate([
            'post' => 'required',
            'created_at' => 'required',
        ]);

        if(strlen($request->created_at) == 16){
            $date = $request->created_at;
            $d =  explode('T', $date);
            $date = $d[0] ." ". $d[1].":00";
        }else{
            $date = $request->created_at;
            $d =  explode('T', $date);
            $date = $d[0] ." ". $d[1];
        }
        
    
  
        $post = Post::find($id);
        $post->post = $request->post;
        $post->created_at = $date;
        $post->save();

        session()->flash('success', 'Post Updated Successfully!');

        return redirect()->route('admin.posts');
        
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        $post->delete();
        session()->flash('success', 'Post deleted Successfully!');
        return redirect()->route('admin.posts');
    }
}
