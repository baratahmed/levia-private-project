@extends('theme_landing.master')

@section('title', 'Add Business')

@section('content')
  <!-- Form -->
  <div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="text-center" style="margin: 50px 0;">
					<h2>Tell us about yourself</h2>
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

					<form action="{{ route('radmin.profile.postContact') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="business-owner-name">Business Owner Name</label>
                            <input type="text" name="owner_name" class="form-control" id="business-owner-name">
                        </div>
                        <div class="form-group">
                            <label for="owner-contact-no">Owner Contact No</label>
                            <input type="tel" name="owner_contact" class="form-control" id="owner-contact-no">
                        </div>
                        <div class="form-group">
                            <label for="business-registration-no">Business Registration No</label>
                            <input type="text" name="business_registration" class="form-control" id="business-registration-no">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="main-btn">Register</button>
                        </div>    
                    </form>
				</div>
			</div>
		</div>
  </div>
@endsection

@section('x-js')
    @parent
    <script>
        var loadFile = function(event) {
            var output = document.getElementById('output');
            output.src = URL.createObjectURL(event.target.files[0]);
        };
    </script>
@endsection