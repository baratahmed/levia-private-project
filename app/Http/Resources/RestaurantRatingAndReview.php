<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantRatingAndReview extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user->id,
            'user_img' => $this->user->getProfilePicture(),
            'user_name' => $this->user->fb_profile_name,
            'starsGiven' => $this->rest_rating_value,
            'created_at' => $this->created_at->diffForHumans(),
            'has_review' => $this->has_review != null,
            'review_text' => $this->review_text,
            'review_image' => $this->review_image,
            'has_reply' => $this->replies()->count() > 0,
            'review_reply' => $this->replies()->first()
        ];
    }
}
