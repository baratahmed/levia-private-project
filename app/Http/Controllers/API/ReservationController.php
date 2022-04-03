<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationsResource;
use App\Models\Reservation;
use App\Models\RestaurantInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function availableSeats(Request $r){
        // dd($r->all());
        $this->validate($r, [
            'rest_id' => 'required|exists:rest_info,id',
            'date_time' => 'required|date|after:now'
        ]);

        $available = $this->getAvailableSeats($r->only(['rest_id', 'date_time']));

        return response()->json([
            'success' => true,
            'available_seats' => $available
        ], 200);
    }

    private function getAvailableSeats($data){
        $time_previous = Carbon::parse($data['date_time'], 'Asia/Dhaka');
        $time_next = Carbon::parse($data['date_time'], 'Asia/Dhaka');

        // echo($time_next);
        
        $rest = RestaurantInfo::findOrFail($data['rest_id']);

        // DB::enableQueryLog();

        $reservations = Reservation::where('reservation_time', '>=', $time_previous->subMinutes(30))
                                   ->where('reservation_time', '<=', $time_next->addMinutes(30))
                                   ->selectRaw('sum(seats) as booked_seats')
                                   ->first();

        // dd(DB::getQueryLog())                                   ;
        // dd($reservations->booked_seats);

        if ($rest->total_seats <= 0){
            return 0;
        }

        $available = !empty($reservations->booked_seats) ? $rest->total_seats - $reservations->booked_seats : $rest->total_seats ;

        return $available;
    }

    public function make(Request $r){
        $this->validate($r, [
            'rest_id' => 'required|exists:rest_info,id',
            'date_time' => 'required|date|after:now',
            'seats' => 'required|integer'
        ]);

        $available = $this->getAvailableSeats($r->only(['rest_id', 'date_time']));
        $user = auth('api')->user();
        $rest = RestaurantInfo::findOrFail($r->rest_id);
        $time = Carbon::parse($r->date_time);
        $time_previous = Carbon::parse($r->date_time);
        $time_next = Carbon::parse($r->date_time);

        if ($r->seats > $available){
            return response()->json([
                'success' => false,
                'message' => 'Not enough seats'
            ], 200);
        }

        if ($rest->plan != "Splash"){
            return response()->json([
                'success' => false,
                'message' => 'This restaurant is not a Splash subscriber. You can\'t reserve seats here.'
            ], 200);
        }

        // DB::enableQueryLog();
        $userReservations = Reservation::where('user_id', $user->id)
                            ->where('reservation_time', '>=', $time_previous->subMinutes(30))
                            ->where('reservation_time', '<=', $time_next->addMinutes(30))
                            ->count();
        // dd(DB::getQueryLog())        ;


        if($userReservations> 0){
            return response()->json([
                'success' => false,
                'message' => 'You already have a reservation at the given time.'
            ], 200);
        }

        $reservation = new Reservation([
            'rest_id' => $r->rest_id,
            'user_id' => $user->id,
            'seats' => $r->seats,
            'reservation_time' => $time,
            'is_paid' => true, // TODO: change it after payment api integration
        ]);

        if ($reservation->save()){
            return response()->json([
                'success' => true,
                'message' => 'Your reservation is complete. Please wait for confirmation by restaurant.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong'
        ], 200);
    }


    public function check_in(Reservation $reserve, Request $r){
        $this->validate($r, [
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
        ]);

        $user = auth('api')->user();

        if ($reserve->user_id != $user->id || $reserve->checked_in_time != null){
            return response()->json([
                'success' => false,
                'message' => 'Not Authorized. You do not have permission to visit this page.'
            ], 200);
        }

        $reserve->checked_in_time = Carbon::now();
        $reserve->checked_in_lat = $r->lat;
        $reserve->checked_in_long = $r->long;

        // TODO: Location validation and other logics
        // TODO: Charge the user for late after payment gateway integration



        if ($reserve->save()){
            return response()->json([
                'success' => true,
                'message' => 'Successfully checked in. Have a good time.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong'
        ], 200);
    }

    public function get(Request $request){
        $user = auth('api')->user();

        $reservations = Reservation::with('restaurant:id,rest_name,rest_image_url')->where('user_id', $user->id)->orderBy('id', 'desc');
        
        if ($request->has('filter')){
            if (strtolower($request->filter) == "upcoming"){
                $reservations = $reservations->where('reservation_time', '>=', Carbon::now('Asia/Dhaka'));
            }
            else if (strtolower($request->filter) == "past"){
                $reservations = $reservations->where('reservation_time', '<', Carbon::now('Asia/Dhaka'));
            }
        }
        
        $reservations = $reservations->paginate(20);
        // $resource = ReservationsResource::collection($reservations);

        return response()->json([
            'success' => true,
            'reservations' => $reservations
        ], 200);
    }
}
