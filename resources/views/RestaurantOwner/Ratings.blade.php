@extends('Model/restaurantOwnerModel')

@section('title', 'Ratings')

@section('content')

    <div id="ApplicationEntry">
        {{-- <div class="text-center">
            <p>No data is available right now</p>
        </div> --}}
        
        <rating-and-review
            restaurantdataurl="{{ route('radmin.getRestRatings') }}"
            foodandcategories="{{ route('radmin.getFoodAndCategories') }}"
            fooddataurl="{{ route('radmin.getFoodRatings') }}"
            postreplyurl="{{ route('radmin.postRatingReply') }}"
            postfoodreplyurl="{{ route('radmin.postFoodRatingReply') }}"
        >
        </rating-and-review>
       
    </div>

@endsection

<?php $VuePage = true ?>
@section('vue-js')
    @parent
    <script src="{{ asset('js/pages/RatingAndReview.js') }}"></script>
@endsection

@section('extra-js')
    <!-- Inline JS -->
    <script>
        $('li').removeClass('active');
        $('#menu-rating').parent().addClass('active');
    </script>
@endsection
