<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestFoodDataResource extends JsonResource
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
            'food_id' => $this->food_id,
            'unit_price' => $this->unit_price,
            'description' => $this->description,
            'food_availability' => $this->food_availability,
            'food_name' => $this->food_name,
            'food_category_id' => $this->food_category_id,
            'food_category_name' => $this->food_category_name,
            'foodImage' => $this->food_image,
            'rating' => $this->rating,
            'isBookmarked' => $this->isBookmarked
        ];
    }
}
