@extends('theme_landing.master')

@section('title', 'Home')

<?php $home = true ?>

@section('content')
    @include('theme_landing.parts.pricing')
    @include('theme_landing.parts.contact')
@endsection