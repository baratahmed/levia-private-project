@extends('theme_landing.master')

@section('title', 'Your email has been verified.')

@section('content')
    <!-- Form -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center" style="margin: 50px 0;">
                    <h2>Email Verified</h2>
                </div>

                <div style="margin: 100px 60px;">
                    <div class="alert alert-success">
                        Your email <strong col>{{ $user->email }}</strong> has been verified successfully.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection