@extends('theme_landing.master')

@section('title', 'Reset your password')

@section('content')
  <!-- Form -->
  <div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="text-center" style="margin: 50px 0;">
					<h2>Reset your password</h2>
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

                    @if (session()->has('info'))
                        <div class="alert alert-info">
                            {{ session()->get('info') }}
                        </div>
                    @endif

					<form action="{{ route('postResetPassword') }}" method="POST">
                        {{ csrf_field () }}
                        <input type="hidden" name="token" value="{{ $token->token }}">
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" class="form-control" name="password" id="password" placeholder="Password">
						</div>
						<div class="form-group">
							<label for="re-password">Confirm Password</label>
							<input type="password" class="form-control" name="re-password" id="re-password" placeholder="Retype Password">
						</div>
						<div class="form-group">
							<button type="submit" class="main-btn">Submit</button>
						</div>
                    </form>
				</div>
			</div>
		</div>
  </div>
@endsection