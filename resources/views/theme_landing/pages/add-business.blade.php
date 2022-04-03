@extends('theme_landing.master')

@section('title', 'Add Business')

@section('content')
  <!-- Form -->
  <div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="text-center" style="margin: 50px 0;">
					<h2>Tell us about your business</h2>
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

					<form action="{{ route('radmin.profile.postBusiness') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                          <div class="col-md-3">
                            <input type="file" name="image" class="form-control-file" accept="image/*" onchange="loadFile(event)">
                            <img id="output" class="img-thumbnail"/>
                          </div>
                          <div class="col-md-9">
                            <div class="form-group">
                              <label for="business-name">Business Name</label>
                              <input type="text" name="business_name" class="form-control" id="business-name">
                            </div>
                            <div class="form-group">
                              <label for="business-contact-no">Business Contact No</label>
                              <input type="text" name="business_contact_no" class="form-control" id="business-contact-no">
                            </div>
                            <div class="form-group">
                              <label for="business-district">District</label>
                              <select name="district" id="business-district" class="form-control">
                                  @foreach($districts as $district)
                                     <option value="{{ $district->district_id }}">{{ $district->district_name }}</option>
                                  @endforeach
                              </select>
                            </div>
                            <div class="form-group">
                              <label for="post_code">Postal Code (Zip Code)</label>
                              <input type="text"
                                class="form-control" name="post_code" id="post_code" aria-describedby="helpId" placeholder="Postal Code">
                            </div>
                            {{-- Someone please integrate google map --}}
                            {{-- <div class="form-group">
                              <label for="business-location">Business Location</label>
                              <div id="map"></div>
                            </div> --}}
                            <div class="form-group">
                              <button type="submit" class="main-btn">Next</button>
                            </div>
                          </div>
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