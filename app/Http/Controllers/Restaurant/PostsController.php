<?php

namespace App\Http\Controllers\Restaurant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::where('rest_id', auth('radmin')->user()->restaurant->id)->orderBy('id','desc')->get();

        return view('RestaurantOwner/Posts', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $is_api_call = false)
    {
        $this->validate($request, [
            'attached_images' => 'sometimes',
            'attached_images.*' => 'image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        // dd($request->file('attached_images'));

        if ((!$request->has('post_content') || $request->post_content == null) && (!$request->has('attached_images') || $request->file('attached_images') == null)){
            throw ValidationException::withMessages([
                'post_content' => 'Post can not be empty. Please write something or upload an image.'
            ]);
        }

        if ($is_api_call){
			$radmin = auth('api_restaurant')->user();
		} else {
			$radmin = auth('radmin')->user();
		}
        // dd('Done');
        $post = new Post();
        $post->user_id = 0; // That means, this is a post created by Restaurant
        $post->rest_id = $radmin->restaurant->id;
        $post->post = $request->has('post_content') ? $request->post_content : null;
        $post->media = null;
        $post->shared_post_id = null;

        if ($request->has('attached_images')){
            $files = $request->file('attached_images');
            $names = [];
            // Process multiple images
            foreach($files as $file){
                $name = str_random(10) . '-' .Carbon::now()->toDayDateTimeString() . '-LEVIA' . '.' . $file->getClientOriginalExtension();
                $name = preg_replace('/[,:\ \t]/i', '-', $name);
    
                // Store the image
                Storage::disk('local')->put('public/post_media_photos/'.$name, file_get_contents($file));
                $names[] = $name; 
            }

            $post->media = json_encode(['image' => $names]);
        }

        $post->save();

        return response([
            'success' => 'true',
            'data'    => $post
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
