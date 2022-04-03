@extends('theme_landing.master')

@section('title', 'Forgot Password')

@section('content')
    <!-- Form -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center" style="margin: 50px 0;">
                    <h2>Forgot your password?</h2>
                </div>

                <div style="margin: 100px 60px;">
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
    
                    @if (session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    <form action="{{ route('postForgotPassword') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="business-email">Business Email</label>
                            <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="main-btn">Request New Password</button>
                        </div>
                    </form>
                    <span class="register-link">Don't have an account? <a class="alert-link" href="{{ route('getRegister') }}">Register</a></span>
                </div>
            </div>
        </div>
    </div>
@endsection