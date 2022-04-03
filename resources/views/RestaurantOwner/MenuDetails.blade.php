@extends('Model/restaurantOwnerModel')

@section('title', 'Settings')

@section('content')

    <div class="container" id="menuDetailsPage">
        <h1>
            Menu details
            <div class="float-right">
                <a href="{{ route('radmin.settings') }}" class="btn btn-primary btn-lg menu">Back</a>
            </div>
        </h1>

        <menu-details
            add_category_action="{{route('radmin.addcategory')}}"
            edit_category_action="{{route('radmin.editcategory')}}"
            add_menu_action = "{{route('radmin.addmenu')}}"
            edit_menu_action = "{{route('radmin.editmenu')}}"
            delete_menu_action = "{{route('radmin.deletemenu')}}"
            toggle_menu_action = "{{route('radmin.togglemenu')}}"
            foodandcategories="{{ route('radmin.getFoodAndCategories') }}"
            food_category_list="{{ route('global.foodCategoryList') }}"
            site_url = {{ url('/') }}
        ></menu-details>
    </div>

@endsection

<?php $VuePage = true ?>
@section('vue-js')
    <script src="{{ asset('js/pages/MenuDetails.js') }}"></script>
@endsection