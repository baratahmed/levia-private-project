@extends('theme_landing.master')

@section('title', 'Login')

@section('content')
    <!-- Form -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center" style="margin: 50px 0;">
                    <h2>Login to your business</h2>
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
                    <form action="{{ route('postLogin') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="business-email">Business Email</label>
                            <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="main-btn">Login</button>
                        </div>
                    </form>
                    <span class="register-link">Don't have an account? <a class="alert-link" href="{{ route('getRegister') }}">Register</a></span>
                    <br /><span class="register-link">Forgot your password? <a class="alert-link" href="{{ route('getForgotPassword') }}">Reset it here</a></span>
                </div>
            </div>
        </div>
    </div>
@endsection