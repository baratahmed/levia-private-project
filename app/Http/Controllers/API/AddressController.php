<?php

namespace App\Http\Controllers\API;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();

        $addresses = UserAddress::where('user_id', $user->id)->get();

        return response([
            'success' => true,
            'data' => [
                'addresses' => $addresses
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'latitude' => ['required','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'division' => 'required|exists:divisions,id',
            'district' => 'required|exists:districts,district_id',
            'upazila' => 'required|exists:upazilas,id',
            'police_station' => 'required',
            'post_code' => 'required',
            'phone' => 'required',
        ]);

        $division = DB::table('divisions')->find($request->division);
        $district = DB::table('districts')->where('district_id', $request->district)->first();
        $upazila = DB::table('upazilas')->find($request->upazila);

        // dd($district);

        $user = auth('api')->user();

        $address = new UserAddress($request->only([
            'latitude',
            'longitude',
            'police_station',
            'post_code',
            'road_no',
            'flat_no',
            'other_details',
            'phone'
        ]));

        $address->user_id = $user->id;
        $address->city = $division->name;
        $address->district = $district->district_name;
        $address->upazila = $upazila->name;
        $address->save();

        return response([
            'success' => true,
            'address' => $address
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
        $user = auth('api')->user();

        $address = UserAddress::where('id', $id)->firstOrFail();

        // dd($address->user_id, $user->id);

        if ((int)$address->user_id !== (int)$user->id){
            return response([
                'success' => false,
                'message' => 'You are not authorized to perform this action.',
            ], 422);
        }

        $address->delete();

        return response([
            'success' => true,
            'message' => 'Address has been deleted',
        ]);
    }
}
