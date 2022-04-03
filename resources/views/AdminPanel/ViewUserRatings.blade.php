@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Users')

@section('brand', 'Users > View')

@section('content')
    <div id="ApplicationEntry">
        {{-- <div class="text-center">
            <p>No data is available right now</p>
        </div> --}}
        <rating-and-review
            restaurantdataurl="{{ route('admin.getRestRatingsUser', $user->id) }}"
            foodandcategories="{{ route('admin.getFoodAndCategoriesUser', $user->id) }}"
            fooddataurl="{{ route('admin.getFoodRatingsUser', $user->id) }}"
            isadmin = "true"
        >
        </rating-and-review>
       
    </div>
@endsection

@section('extra-js')
    <script>
        $('li').removeClass('active');
        $('#menu-users').parent().addClass('active');
    </script>
@endsection

<?php $VuePage = true ?>
@section('vue-js')
    @parent
    <script src="{{ asset('js/pages/RatingAndReview.js') }}"></script>
@endsection
