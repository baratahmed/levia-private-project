<?php

namespace App\Http\Resources;

use App\LeviaHelpers\UserBookmarks;
use Illuminate\Http\Resources\Json\JsonResource;

class SponsoredResource extends JsonResource
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
            'id' => $this->restaurant->id,
            'rest_name' => $this->restaurant->rest_name,
            'imageUrl' => $this->restaurant->imageUrl,
            'district_id' => $this->restaurant->district_id,
            // 'district' => $this->district,
            'district_name' => $this->restaurant->district->district_name,
            'rest_street' => $this->restaurant->rest_street,
            'city_id' => $this->restaurant->city_id,
            'post_code' => $this->restaurant->rest_post_code,
            'road_no' => $this->road_no,
            'police_station' => $this->restaurant->police_station,
            'phone' => $this->restaurant->phone,
            'rest_post_code' => $this->restaurant->rest_post_code,
            'rest_description' => $this->restaurant->rest_description,
            'rest_reg_date' => $this->restaurant->rest_reg_date,
            'sponsored_id' => $this->id,
            'starting_at' => $this->starting_at,
            'ending_at' => $this->ending_at,
            'package_id' => $this->package_id,
            'amount' => $this->amount,
            'method' => $this->method,
            'is_active' => $this->is_active,
            'isBookmarked' => UserBookmarks::get(auth()->user())->contains($this->restaurant),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'plan' => $this->plan,
            'rating' => $this->restaurant->rating
        ];
    }
}
