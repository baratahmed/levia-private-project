<?php

namespace App\Http\Controllers;

use App\Models\ContactFormData;
use Illuminate\Http\Request;
use App\Models\District;
use App\Models\Promotion;
use App\Models\PromotionPackage;
use App\Models\RestaurantInfo;
use App\Models\RestSchedule;
use App\Models\RestProperty;
use App\Models\RestFoodDetailsDataset;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferType;
use App\Models\Reservation;
use App\Models\RestRating;
use App\Models\RestReview;
use App\Models\ReviewInfo;
use Illuminate\Support\Facades\DB;

class AdminPanelStaticController extends Controller
{
    public function trends()
    {
        return view('AdminPanel.Trends');
    }

    public function promotions(Request $r)
    {
        $packages = PromotionPackage::with('prices')->get();
        $total = Promotion::count();

        $promotions = Promotion::with('restaurant');

        if ($r->has('business_name') && $r->business_name != ''){
            $rest = RestaurantInfo::where('rest_name', 'LIKE', '%'.$r->business_name.'%')->first();
            if ($rest != null){
                $promotions = $promotions->where('rest_id', $rest->id);
            }
        } else {
            $rest = 'not null';
        }
        if ($r->has('package') && $r->package != 'all'){
            $promotions = $promotions->where('package_id', $r->package);
        }
        if ($r->has('only')){
            if ($r->only == 'active'){
                $promotions = $promotions->where('is_active', true);
            }
        }

        $promotions = $promotions->orderBy('id', 'desc')->paginate(10);
        return view('AdminPanel.Promotions', compact('promotions', 'total', 'packages', 'rest'));
    }

    public function payments()
    {
        return view('AdminPanel.Payments');
    }

    public function users(Request $r)
    {

        $users = User::orderBy('id', 'desc');
        if ($r->has('username') && $r->username != null){
            $users = $users->where('fb_profile_name', 'LIKE', '%'.$r->username.'%');
        }
        if ($r->has('email') && $r->email != null){
            $users = $users->where('user_email', 'LIKE', $r->email);
        }
        if ($r->has('date') && $r->date != null){
            $users = $users->whereDate('created_at',$r->date);
        }
        $users = $users->paginate(5);
        return view('AdminPanel.Users', compact('users'));
    }

    public function business(Request $r)
    {
        $restaurants = RestaurantInfo::select('rest_info.*')->orderBy('id', 'desc');

        if ( $r->has('name')  && !is_null($r->name) ) {
            $restaurants = $restaurants->where('rest_name', 'LIKE', '%'.$r->name.'%');
        }
        if ( $r->has('email')  && !is_null($r->email) ) {
            $restaurants = $restaurants->leftJoin('rest_admins', 'rest_info.radmin_id', 'rest_admins.id');
            $restaurants = $restaurants->where('rest_admins.email' , $r->email);
        }
        if ( $r->has('status')  && !is_null($r->status) ) {
            if ($r->status == 'Published'){
                $restaurants = $restaurants->onlyPublished();
            }
            else if ($r->status == 'Unpublished'){
                $restaurants = $restaurants->onlyUnpublished();
            }
        }
        $restaurants = $restaurants->paginate(10);
        return view('AdminPanel.Business', compact('restaurants'));
    }

    public function orders()
    {
        return view('AdminPanel.Orders');
    }

    public function ratings()
    {
        //$rests = RestaurantInfo::paginate(5);
        //$rests = RestaurantInfo::all();
        
        $all_reviews = ReviewInfo::paginate(100); 

        // foreach ($all_reviews as $review) {
        //    $related_rest_review = RestReview::where('review_id',$review->id)->first();
        //    $restaurant = RestaurantInfo::find($related_rest_review->rest_id);  
        //    dd($related_rest_review->rest_id); 
        //    dd($review.$restaurant);
        // }

        // $all_reviews = DB::table('review_info')
        //             ->join('user_info', 'review_info.user_id', '=', 'user_info.id')
        //             ->select('review_info.*', 'user_info.*')
        //             ->get();
     
        
        return view('AdminPanel.Ratings', compact('all_reviews'));
    }

    public function ratingsOfRest(RestaurantInfo $rest){

        return view('AdminPanel.RatingsOf', compact('rest'));
    }

    public function settings()
    {
        return view('AdminPanel.Settings');
    }

    public function reservations(){

        $reservations = Reservation::orderBy('id', 'desc')->get();

        return view('AdminPanel/Reservations', compact('reservations'));
    }

    public function offers()
    {
        $total = Offer::count();
        $offers = Offer::orderBy('offer_id', 'desc')->with(['type', 'restaurant', 'food'])->paginate(10);


        return view('AdminPanel.Offers', compact('offers','total'));
    }

    public function createOffer(){
        $offer_type = OfferType::orderBy("offer_type_name", "asc")->get();
        $restaurants = RestaurantInfo::select(['id','rest_name'])->orderBy('rest_name', 'asc')->get();
        return view('AdminPanel.CreateOffer')->with(compact("offer_type", "restaurants"));
    }

    public function editOffer($id){
        $offer_type = OfferType::orderBy("offer_type_name", "asc")->get();
        $offer = Offer::where('offer_id', $id)->with(['type', 'restaurant', 'food'])->first();
        $restaurants = RestaurantInfo::select(['id','rest_name'])->orderBy('rest_name', 'asc')->get();

        return view('AdminPanel.EditOffer')->with(compact("offer_type", "offer", "restaurants"));
    }

    public function ViewOffers($id)
    {
        $offer = Offer::where('offer_id', $id)->with(['type', 'restaurant', 'food'])->first();

        return view('AdminPanel.ViewOffer', compact('offer'));
    }

    public function ViewUser(User $user)
    {
        // dd($user->user_email);
        return view('AdminPanel.ViewUser', compact('user'));
    }

    public function ViewUserRatings(User $user)
    {
        // dd($user->user_email);
        return view('AdminPanel.ViewUserRatings', compact('user'));
    }

    public function ViewOrders($id)
    {
        return view('AdminPanel.ViewOrder');
    }


    public function contacts()
    {
        $contacts = ContactFormData::orderBy('id', 'desc')->paginate(10);

        return view('AdminPanel.ContactRequests', compact('contacts'));
    }

    public function CreateBusiness()
    {
        $districts = District::all();
        return view('AdminPanel.CreateBusiness', compact('districts'));
    }

    public function ViewBusiness($id)
    {
        $restaurant = RestaurantInfo::find($id);
        $districts = District::all();
        $schedules = RestSchedule::where('rest_id', $id)->orderBy('day_id','asc')->get();
        $weekdays = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
        $properties = RestProperty::where('rest_id', $id)->first();
        $paymethod = $restaurant->paymethod;
        $foods = RestFoodDetailsDataset::where('rest_id', $restaurant->id)->get();
        return view('AdminPanel.ViewBusiness', compact('restaurant', 'districts', 'schedules', 'weekdays', 'properties', 'paymethod', 'foods'));
    }

    public function EditBusiness($id)
    {
        $restaurant = RestaurantInfo::find($id);
        $districts = District::all();
        $schedules = RestSchedule::where('rest_id', $id)->orderBy('day_id','asc')->get();
        $weekdays = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
        $properties = RestProperty::where('rest_id', $id)->first();
        $paymethod = $restaurant->paymethod;
        return view('AdminPanel.EditBusiness', compact('restaurant', 'districts', 'schedules', 'weekdays', 'properties', 'paymethod'));
    }
}
