@extends('layouts.boxpage')

@section('title', 'Levia | Welcome')

@section('content')
    <div class="main-box">
        <h4 class="text-center">Login as a Restaurant Manager</h4>

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

        <form action="{{ route('postLogin') }}" method="post">
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

        Don't have an account? <a class="alert-link" href="{{ route('getRegister') }}">Register</a>
    </div>
@endsection