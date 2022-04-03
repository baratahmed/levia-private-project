@extends('layouts.boxpage')

@section('title', 'Levia | Welcome')

@section('content')
    <div class="main-box">
        <h4 class="text-center">Welcome to Levia</h4>

        @if(auth('radmin')->guest())
            <a href="{{ route('getLogin') }}" class="btn btn-primary form-control">Login as Restaurant Owner</a>
            <a href="{{ route('getRegister') }}" class="btn btn-success form-control mt-3">Register as Restaurant Owner</a>
        @else
            <a href="{{ route('radmin.dashboard') }}" class="btn btn-success">Go to Dashboard</a>
            <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
        @endif
    </div>

    <div class="main-box" style="margin-top:50px !important;">
        <h4 class="text-center">Download our App</h4>
    </div>
@endsection