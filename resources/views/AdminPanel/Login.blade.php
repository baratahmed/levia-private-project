@extends('layouts.boxpage')

@section('content')
    <div class="main-box">
        <h4 class="text-center">Login as an Administrator</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session()->has('danger'))
            <div class="alert alert-danger">
                {{ session()->get('danger') }}
            </div>
        @endif

        <form action="{{ route('postLoginAdmin') }}" method="post">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
            </div>

            <input type="submit" class="btn btn-primary" value="Login">
        </form>

        <p class="small">This module is only supposed to be used by Admins. If you are a general user, please head to <a href="{{ url('/') }}">Homepage</a> to use Restaurant services.</p>
    </div>
@endsection

@section('css')
    @parent
    <style>
        body {
            background:#0F0F17;
        }

        .main-box {
            background:#0a0a14;
            color:white;
            border-color:#0a0a14;
        }
    </style>
@endsection