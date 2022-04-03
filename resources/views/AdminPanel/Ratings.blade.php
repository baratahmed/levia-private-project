@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Trends')

@section('brand', 'Ratings and Reviews')

@section('content')
    <div class="container">
        <div class="row">

            @foreach($all_reviews as $review)

            @php
                $related_rest_review = \App\Models\RestReview::where('review_id',$review->id)->first();
            @endphp

                @if($related_rest_review)
                    @php
                         $restaurant = \App\Models\RestaurantInfo::find($related_rest_review->rest_id);
                    @endphp

                    <div class="col-md-4">
                        <div class="card">
                            <div class="row">
                                <div class="col-md-7 text-left pt-3 pl-5">
                                    <h6 class="card-title">@php  echo e($restaurant->rest_name);  @endphp</h6>
                                    @php
                                    $rest_ratings = \App\Models\RestRating::where('rest_id',$restaurant->id)->where('user_id',$review->user->id)->first();                                                       
                                
                                    if($rest_ratings){
                                        for ($i = 0; $i < $rest_ratings->rest_rating_value; $i++){
                                        @endphp
                                            <img src="{{asset('/img/rating_icon.svg')}}" alt="">        
                                        @php
                                     }
                                    }
                                    @endphp
                                
                                    <p >{{$review->created_at->format('H:i A,D, M d, Y')}}</p>

                                </div>
                                <div class="col-md-5 text-center">
                                    <img src="{{asset('/img')}}/{{$review->user->fb_profile_pic_url}}" alt="Card image cap" class="rounded-circle img-fluid p-2" width="70" height="70">
                                    <p class="card-text">{{$review->user->fb_profile_name}}</p>
                                </div>
                            </div>
                            
                            {{-- <h5 class="card-title">Card title</h5> --}}
                            <p class="px-3">{{$review->review_text}}</p>

                           
                        </div>
                    </div>
                @else

                @php  
                                       
                     $related_food_review = \App\Models\FoodReview::where('review_id',$review->id)->first();
                     
                     $food = \App\Models\Food::find($related_food_review->food_id);                      
                     
                     $restaurant = \App\Models\RestaurantInfo::find($related_food_review->rest_id);

                     
                @endphp

                <div class="col-md-4">
                    <div class="card">
                        <div class="row">
                            <div class="col-md-7 text-left pt-3 pl-5">
                                <h6 class="card-title">@php  echo e($restaurant->rest_name);  @endphp</h6>
                                <h6 class="card-title">@php  echo e($food->food_name);  @endphp</h6>
                                @php
                                   $food_ratings = \App\Models\FoodRating::where('food_id',$food->food_id)->where('user_id',$review->user->id)->first(); 
                                    if($food_ratings){
                                        for ($i = 0; $i < $food_ratings->food_rating_value; $i++){
                                        @endphp
                                            <img src="{{asset('/img/rating_icon.svg')}}" alt="">        
                                        @php
                                     }
                                    }
                                    
                                     
                                    @endphp 
                                <p >{{$review->created_at->format('H:i A, D, M d, Y')}}</p>

                            </div>
                            <div class="col-md-5 text-center">
                                <img src="{{asset('/img')}}/{{$review->user->fb_profile_pic_url}}" alt="Card image cap" class="rounded-circle img-fluid p-2" width="70" height="70">
                                <p class="card-title">{{$review->user->fb_profile_name}}</p>
                            </div>
                        </div>
                        
                        {{-- <h5 class="card-title">Card title</h5> --}}
                        <p class="px-3">{{$review->review_text}}</p>

                       
                    </div>
                </div>
                @endif

            @endforeach

        </div>
        <div class="d-flex justify-content-center">
            {{$all_reviews->links()}}
        </div>
    </div>
      
@endsection

@section('extra-js')
    <script>
        $('li').removeClass('active');
        $('#menu-ratings').parent().addClass('active');
    </script>
@endsection