@extends('theme_landing.master')

@section('title', 'Register')

@section('content')
  <!-- Form -->
  <div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="text-center" style="margin: 50px 0;">
					<h2>Register your business</h2>
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

					<form action="{{ route('postRegister') }}" method="POST">
                        {{ csrf_field () }}
						<div class="form-group">
							<label for="email">Business Email</label>
							<input type="text" class="form-control" name="email" id="email" placeholder="Email">
						</div>
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" class="form-control" name="password" id="password" placeholder="Password">
						</div>
						<div class="form-group">
							<label for="re-password">Confirm Password</label>
							<input type="password" class="form-control" name="re-password" id="re-password" placeholder="Retype Password">
						</div>
						<div class="form-group">
							<button type="submit" class="main-btn">Next</button>
						</div>
                    </form>

                    <span class="login-link">Already have an account? <a class="alert-link" href="{{ route('getLogin') }}">Login</a></span>
				</div>
			</div>
		</div>
  </div>
@endsection
