@extends('layouts.boxpage')

@section('title', 'Levia | Welcome')

@section('content')

    <form method="post" action="{{route('addrestaurent')}}">
        {{ csrf_field() }}

        {{-- Basic Information --}}
        <div class="modal-dialog modal-lg" style="margin-bottom: -25px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="request-label">Restaurant Details</h5>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="modal-body">
                    <div class="form-group">
                        <label for="restaurant-name">Restaurant name</label>
                        <input type="text" name="rest_name" id="restaurant-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="restaurant-logo">Restaurant logo</label>
                        <input type="file" class="rest_image_url" name="rest_image_url" id="restaurant-logo">
                    </div>
                    <div class="form-group">
                        <label for="restaurant-address">Restaurant Address</label>
                        <input type="text" class="form-control" id="restaurant-address" name="rest_street" >
                        <div class="row">
                            <div class="col-md-6">
                                <label for="restaurant-address-road-no">Road no</label>
                                <input type="text" name="road_no" id="restaurant-address-road-no" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="restaurant-address-police-station">Police Station</label>
                                <input type="text"  name="police_station" id="restaurant-address-police-station" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="restaurant-address-postal-code">Postal code</label>
                                <input type="text" name="rest_post_code" id="restaurant-address-postal-code" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="restaurant-address-district">District</label>
                                <select class="form-control" name="district_id" id="restaurant-address-district" >
                                        @foreach($districts as $district)
                                            <option value="{{ $district->district_id }}">{{ $district->district_name }}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tax-no">Tax no</label>
                        <input type="text"  name="rest_tax_no" id="tax-no" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Id</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact no</label>
                        <input type="text" name="phone_no" id="contact" class="form-control" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Working Hours --}}
        <div class="modal-dialog modal-lg"  style="margin-bottom: -25px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="request-label">Working hours</h5>
                </div>

                <div class="modal-body">
                    @foreach($weekdays as $day)
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" name="{{ $day }}" id="{{ $day }}">
                            <label class="form-check-label" for="{{ $day }}">{{ $day }}
                                <div class="row">
                                    <div class="col-md-5">
                                        <input type="text" name="opening_time[]" id="sunday-start-time" class="form-control" placeholder="Start">
                                    </div>
                                    <div class="col-md-2">
                                        <div style="height: 20px; width: 100%; border-bottom: 1px solid black;"></div>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="closing_time[]" id="sunday-end-time" class="form-control" placeholder="End">
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Properties --}}
        <div class="modal-dialog modal-lg"  style="margin-bottom: -25px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="request-label">Restaurant Properties</h5>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="live_music" id="live-music">
                                <label class="form-check-label" for="live-music">Live Music <i class="fas fa-music"></i></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="smoking_place" id="smoking">
                                <label class="form-check-label" for="smoking">Smoking Area <i class="fas fa-smoking"></i></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="kids_corner" id="kids_corner">
                                <label class="form-check-label" for="kids_corner">Kids Corner<i class="fas fa-child"></i></label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <input type="checkbox" class="form-check-input" name="praying_area" id="praying_area">
                            <label class="form-check-label" for="praying_area">Praying area <i class="fas fa-archway"></i></label>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="wifi" id="wifi">
                                <label class="form-check-label" for="wifi">Wifi <i class="fas fa-wifi"></i></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="parking" id="parking">
                                <label class="form-check-label" for="parking">Parking <i class="fas fa-parking"></i></label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="self_service" id="self_service">
                                <label class="form-check-label" for="self_service">Self Service <i class="fas fa-concierge-bell"></i></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="tv" id="tv">
                                <label class="form-check-label" for="tv">TV <i class="fas fa-tv"></i></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" name="game_zone" id="game_zone">
                                <label class="form-check-label" for="game_zone">Game zone <i class="fas fa-futbol-o"></i></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save and Continue</button>
                </div>
            </div>
        </div>

    </form>

@endsection
