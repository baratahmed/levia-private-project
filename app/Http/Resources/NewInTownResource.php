<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewInTownResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'rest_name' => $this->rest_name,
            'imageUrl' => $this->imageUrl,
            'district_id' => $this->district_id,
            // 'district' => $this->district,
            'district_name' => $this->district->district_name,
            'rest_street' => $this->rest_street,
            'city_id' => $this->city_id,
            'post_code' => $this->rest_post_code,
            'road_no' => $this->road_no,
            'police_station' => $this->police_station,
            'police_station' => $this->police_station,
            'phone' => $this->phone,
            'rest_post_code' => $this->rest_post_code,
            'rest_description' => $this->rest_description,
            'rest_reg_date' => $this->rest_reg_date,
            'rating' => $this->rating,
            'plan' => $this->plan,
            'isBookmarked' => $this->isBookmarked
        ];
    }
}
