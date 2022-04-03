@extends('theme_landing.master')

@section('title', 'Email has been sent.')

@section('content')
    <!-- Form -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center" style="margin: 50px 0;">
                    <h2>Email has been sent.</h2>
                </div>

                <div style="margin: 100px 60px;">
                    <div class="alert alert-success">
                        A link with password recovery information has been sent to your email address. Please check your email.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection