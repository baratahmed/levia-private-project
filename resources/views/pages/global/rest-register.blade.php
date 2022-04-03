@extends('layouts.boxpage')

@section('title', 'Levia | Welcome')

@section('content')
    <div class="main-box">
        <h4 class="text-center">Register as a Restaurant Manager</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('postRegister') }}" method="post">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" name="email" id="email" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="re-password">Retype Password</label>
                <input type="password" class="form-control" name="re-password" id="re-password" placeholder="Retype Password">
            </div>
            <input type="submit" class="btn btn-primary" value="Register">
        </form>

        Already have an account? <a class="alert-link" href="{{ route('getLogin') }}">Login</a>
    </div>
@endsection