@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Trends')

@section('brand', 'Ratings and Reviews')

@section('content')
    <div id="ApplicationEntry">
        {{-- <div class="text-center">
            <p>No data is available right now</p>
        </div> --}}
        
        <rating-and-review
            restaurantdataurl="{{ route('admin.getRestRatings', $rest->id) }}"
            foodandcategories="{{ route('admin.getFoodAndCategories', $rest->id) }}"
            fooddataurl="{{ route('admin.getFoodRatings', $rest->id) }}"
            isadmin="true"
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
    <script>
        $('li').removeClass('active');
        $('#menu-ratings').parent().addClass('active');
    </script>
@endsection