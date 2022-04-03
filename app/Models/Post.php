<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    protected $hidden = ['media'];
    protected $appends = ['post_image', 'shared_post'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function restaurant(){
        return $this->belongsTo(RestaurantInfo::class, 'rest_id', 'id');
    }

    public function meta(){
        return $this->hasOne(NewsFeed::class, 'id', 'post_meta_id');
    }
    
    public function comments(){
        return $this->hasMany(PostComment::class, 'post_id', 'id');
    }

    public function likes(){
        return $this->hasMany(PostLike::class, 'post_id', 'id');
    }
    
    public function getPostImageAttribute(){
        if ($this->media != null){
            $decoded = json_decode($this->media);

            $images = collect($decoded->image);
            // Generate URL from names
            $images = $images->map(function($image){
                return asset('storage/post_media_photos/'. $image);
            });

            return $images;
        }
    }
    public function getSharedPostAttribute(){
        if ($this->shared_post_id != null){
            $post = static::with('user:id,fb_profile_name,fb_profile_pic_url')->with('restaurant:id,rest_name,rest_image_url')->with('meta')->where('id', $this->shared_post_id)->firstOrFail();
            if ($post){
                $post->created_at_string = $post->created_at->diffForHumans();
                return $post;
            }
        }
    }

    public static function addNewsfeedPost($newsfeed_id, $user_id){
        $post = new static();
        $post->user_id = $user_id;
        $post->post = null;
        $post->media = null;
        $post->shared_post_id = null;
        $post->post_meta_type = "news_feeds";
        $post->post_meta_id = $newsfeed_id;

        $post->save();
    }
}
