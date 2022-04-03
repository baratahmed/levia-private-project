<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\District;
use App\Models\OfferType;
use App\Models\Offer;
use App\Models\RestaurantInfo;
use Illuminate\Support\Facades\Auth;
use App\Models\Stats\Search;
use App\Models\Stats\Bookmark;
use App\Models\Stats\MapDirection;
use App\Models\Stats\MobileCalls;
use App\Models\FoodRatingReview;
use App\Models\Promotion;
use App\Models\PromotionPackage;
use App\Models\Reservation;
use App\Models\RestaurantRatingReview;
use App\Models\RestFoodDetailsDataset;
use App\Models\Stats\TotalHeads;

class RestaurantStaticController extends Controller
{
    //

    public function trends(Request $r) {
        $duration = $r->get('duration');

        $radmin = auth('radmin')->user();

        $rest = $radmin->restaurant;

        $stats['rating'] = RestaurantRatingReview::filterDuration($duration)->count() + 
                            FoodRatingReview::filterDuration($duration)->count();
        $stats['review'] = RestaurantRatingReview::filterDuration($duration)->where('has_review', '>=', 1)->count() +
                            FoodRatingReview::filterDuration($duration)->where('has_review', '>=', 1)->count();
        $stats['search'] = Search::filterDuration($duration)->count();
        $stats['bookmark'] = Bookmark::filterDuration($duration)->count();
        $stats['mapdirection'] = MapDirection::filterDuration($duration)->count();
        $stats['mobilecall'] = MobileCalls::filterDuration($duration)->count();

        $totalheads = TotalHeads::filterDuration($duration)->stats()->get();
        // dd($totalheads->implode('needle', ','));

        return view('RestaurantOwner/Trends', compact('rest','stats', 'totalheads'));
    }

    public function dashboard(){
        return redirect()->route('radmin.trends');
        $radmin = auth('radmin')->user();
        $rest = $radmin->restaurant;
        return view('RestaurantOwner/Dashboard', compact('rest'));
    }

    public function ratings(){
        return view('RestaurantOwner/Ratings');
    }

    public function myorders(){
        return view('RestaurantOwner/MyOrders');
    }

    public function notification(){
        return view('RestaurantOwner/Notification');
    }

    public function payments(){
        return view('RestaurantOwner/Payments');
    }

    public function gallery(){
        // Get restaurant logo
        $rest = auth('radmin')->user()->restaurant;

        $foods = RestFoodDetailsDataset::where('rest_id', $rest->id)->groupBy('food_image_url')->get();

        return view('RestaurantOwner/Gallery', compact('rest', 'foods'));
    }

    public function reservations(){
        // Get restaurant logo
        $rest = auth('radmin')->user()->restaurant;

        $reservations = $rest->reservations()->orderBy('id', 'desc')->get();

        Reservation::where('rest_id', $rest->id)->where('is_seen', false)->update(['is_seen' => true]);

        return view('RestaurantOwner/Reservations', compact('rest', 'reservations'));
    }

    public function promotion(){
        $rest = auth('radmin')->user()->restaurant;
        $promotions = Promotion::where('rest_id', $rest->id)->orderBy('id', 'desc')->paginate(10);
        $packages = PromotionPackage::with('prices')->get();
        return view('RestaurantOwner/Promotion', compact('promotions', 'packages'));
    }

    public function addRestaurant(){
        $districts = District::orderBy('district_name', 'asc')->get();
        $weekdays = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
        // dd($weekdays);
        return view('RestaurantOwner/AddRestaurant')->with(compact("districts", "weekdays"));
    }

    public function createOffer(){
        $offer_type = OfferType::orderBy("offer_type_name", "asc")->get();
        $userID = Auth::guard('radmin')->user()->id;
        $restaurant = RestaurantInfo::where("radmin_id", $userID)->first();

        return view('RestaurantOwner/CreateOffer')->with(compact("offer_type","restaurant"));
    }

    public function Offer()
    {
        $userID = Auth::guard('radmin')->user()->id;
        $rest = RestaurantInfo::where("radmin_id", $userID)->first();

        $offers = Offer::where("rest_id", $rest->id)->with("type")->orderBy('offer_id','desc')->get();

        return view('RestaurantOwner/Offer')->with(compact("offers"));
    }

    public function ViewOffer($id)
    {
        $offer = Offer::where("offer_id", $id)->with(["type", "restaurant"])->first();

        return view('RestaurantOwner/ViewOffer')->with(compact("offer"));
    }

    public function EditOffer($id)
    {
        $userID = Auth::guard('radmin')->user()->id;
        $rest = RestaurantInfo::where("radmin_id", $userID)->first();
        $offer_type = OfferType::orderBy("offer_type_name", "asc")->get();

        $offer = Offer::where("offer_id", $id)->where("rest_id", $rest->id)->first();

        if ($offer->status !== "Ongoing"){
            return redirect()->back()->with('message', 'Only Ongoing offers can be edited.');
        }

        return view('RestaurantOwner/EditOffer')->with(compact("offer", "offer_type", "rest"));
    }

}
