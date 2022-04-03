@extends('theme_landing.master')

@section('title', 'Password has been reset.')

@section('content')
    <!-- Form -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center" style="margin: 50px 0;">
                    <h2>Password has beem reset.</h2>
                </div>

                <div style="margin: 100px 60px;">
                    <div class="alert alert-success">
                        Your password has been reset. Please <a href="{{ route('getLogin') }}">Login</a> to your account.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection