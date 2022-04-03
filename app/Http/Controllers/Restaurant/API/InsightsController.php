<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FoodRatingReview;
use App\Models\Order;
use App\Models\RestaurantRatingReview;
use App\Models\RestFoodDetailsDataset;
use Illuminate\Support\Facades\DB;

class InsightsController extends Controller
{
    public function getInsights(){
        $user = auth('api_restaurant')->user();
        $rest = $user->restaurant;
        $ratings = RestaurantRatingReview::count() + FoodRatingReview::count();
        $reviews = RestaurantRatingReview::whereNotNull('has_review')->count() + FoodRatingReview::whereNotNull('has_review')->count();
        $menus = RestFoodDetailsDataset::count();
        $categories = RestFoodDetailsDataset::groupBy('food_category_id')->count();

        // Business
        // DB::enableQueryLog();
        $todays = [
            'revenue' => Order::joinCart()->filterDuration('today')->selectRaw('sum(total_payable) as total')->onlyDelivered()->get(),
            'delivered_orders' => Order::joinCart()->filterDuration('today')->onlyDelivered()->count(),
            'revenue_loss' => Order::joinCart()->filterDuration('today')->where('order_status','CANCELED')->selectRaw('sum(total_payable) as total')->get(),
            'canceled_orders' => Order::joinCart()->filterDuration('today')->where('order_status','CANCELED')->count(),
        ];
        // dd(DB::getQueryLog());
        $this_week = [
            'revenue' => Order::joinCart()->filterDuration('week')->selectRaw('sum(total_payable) as total')->onlyDelivered()->get(),
            'delivered_orders' => Order::joinCart()->filterDuration('week')->onlyDelivered()->count(),
            'revenue_loss' => Order::joinCart()->filterDuration('week')->where('order_status','CANCELED')->selectRaw('sum(total_payable) as total')->get(),
            'canceled_orders' => Order::joinCart()->filterDuration('week')->where('order_status','CANCELED')->count(),
        ];
        $this_month = [
            'revenue' => Order::joinCart()->filterDuration('month')->selectRaw('sum(total_payable) as total')->onlyDelivered()->get(),
            'delivered_orders' => Order::joinCart()->filterDuration('month')->onlyDelivered()->count(),
            'revenue_loss' => Order::joinCart()->filterDuration('month')->where('order_status','CANCELED')->selectRaw('sum(total_payable) as total')->get(),
            'canceled_orders' => Order::joinCart()->filterDuration('month')->where('order_status','CANCELED')->count(),
        ];

        // Trends
        // DB::enableQueryLog();
        $trends['revenue_and_delivered_orders'] = Order::joinCart()->filterDuration('week')->stats()->selectRaw('sum(total_payable) as total')->onlyDelivered()->get();
        $trends['revenue_loss_and_cancelled_orders'] = Order::joinCart()->filterDuration('week')->stats()->where('order_status','CANCELED')->selectRaw('sum(total_payable) as total')->get();
        // $trends['delivered_orders'] = Order::joinCart()->filterDuration('week')->onlyDelivered()->count();
        // $trends['canceled_orders'] = Order::joinCart()->filterDuration('week')->where('order_status','CANCELED')->count();
        
        $trends['revenue_and_delivered_orders_last_week'] = Order::joinCart()->filterDuration('last_week')->stats()->selectRaw('sum(total_payable) as total')->get();
        $trends['revenue_loss_and_canceled_orders_last_week'] = Order::joinCart()->filterDuration('last_week')->stats()->where('order_status','CANCELED')->selectRaw('sum(total_payable) as total')->get();
        // $trends['delivered_orders_last_week'] = Order::joinCart()->filterDuration('last_week')->onlyDelivered()->count();
        // $trends['canceled_orders_last_week'] = Order::joinCart()->filterDuration('last_week')->where('order_status','CANCELED')->count();
        // dd(DB::getQueryLog());

        // Add dates that don't have any data with zero values
        $trends['revenue_and_delivered_orders'] = Order::addZeroValues($trends['revenue_and_delivered_orders'], 'week');
        $trends['revenue_loss_and_cancelled_orders'] = Order::addZeroValues($trends['revenue_loss_and_cancelled_orders'], 'week');
        $trends['revenue_and_delivered_orders_last_week'] = Order::addZeroValues($trends['revenue_and_delivered_orders_last_week'], 'last_week');
        $trends['revenue_loss_and_canceled_orders_last_week'] = Order::addZeroValues($trends['revenue_loss_and_canceled_orders_last_week'], 'last_week');

        return response([
            'success' => true,
            'data' => [
                'user_rating' => $ratings,
                'reviews' => $reviews,
                'menus' => $menus,
                'categories' => $categories,
                'business' => [
                    'today' => $todays,
                    'this_week' => $this_week,
                    'this_month' => $this_month,
                ],
                'trends' => $trends
            ],
        ]);
        
    }
}
