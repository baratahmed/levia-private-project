<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationsResource extends JsonResource
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
            'rest_name' => $this->restaurant->rest_name,
            'rest_id' => $this->restaurant->id,
            'seats' => $this->seats,
            'reservation_time' => $this->reservation_time,
            'is_paid' => $this->is_paid,
            'is_accepted' => $this->is_accepted,
            'is_seen' => $this->is_seen,
            'checked_in_time' => $this->checked_in_time,
            'checked_in_lat' => $this->checked_in_lat,
            'checked_in_long' => $this->checked_in_long,
            'created_at' => $this->created_at,
        ];
    }
}
