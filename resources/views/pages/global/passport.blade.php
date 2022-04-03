@extends('Model.restaurantOwnerModel')

@section('title', 'Passport')

@section('content')
    <div id="passportThings">
        <passport-clients></passport-clients>
        <passport-authorized-clients></passport-authorized-clients>
        <passport-personal-access-tokens></passport-personal-access-tokens>
    </div>
@endsection

<?php $VuePage = true ?>
@section('vue-js')
    @parent
    <script src="{{ asset('js/pages/Passport.js') }}"></script>
@endsection