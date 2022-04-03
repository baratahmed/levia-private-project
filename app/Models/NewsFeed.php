<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsFeed extends Model
{
    protected $guarded = [];
    protected $hidden = ['media'];
    public $appends = ['review_image'];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getReviewImageAttribute(){
        if ($this->media != null){
            $decoded = json_decode($this->media);

            $images = collect($decoded->image);
            // Generate URL from names
            $images = $images->map(function($image){
                return asset('storage/review_media_photos/'. $image);
            });

            return $images;
        }
    }
}
