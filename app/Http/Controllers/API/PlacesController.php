<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PlacesController extends Controller
{
    public function getPlaces(Request $r){
        $this->validate($r, [
            'type' => 'required',
            'division_id' => 'sometimes|exists:divisions,id',
            'district_id' => 'sometimes|exists:districts,district_id'
        ]);

        $type = strtolower(trim($r->type));

        if ($type === 'city' || $type === 'cities'){
            $cities = DB::table('cities')
                ->orderBy('city_name', 'ASC')
                ->get();

            return $cities;
        } else if ($type === 'division' || $type === 'divisions'){
            $districts = DB::table('divisions')
                ->orderBy('name', 'ASC')
                ->select(['id', 'name'])
                ->get();

            return $districts;
        } else if ($type === 'district' || $type === 'districts'){
            $districts = DB::table('districts')
                ->orderBy('district_name', 'ASC')
                ->select(['district_id', 'district_name']);

            if ($r->has('division_id')){
                $districts->where('division_id', $r->division_id);
            }

            $districts = $districts->get();

            return $districts;
        } else if ($type === 'upazila' || $type === 'upazilas'){
            $upazilas = DB::table('upazilas')
                ->orderBy('name', 'ASC');

            if ($r->has('district_id')){
                $upazilas->where('district_id', $r->district_id);
            }

            $upazilas = $upazilas->get();

            return $upazilas;
        }

        return response([
            'message' => 'Please define the type of address'
        ], 422);
    }
}
