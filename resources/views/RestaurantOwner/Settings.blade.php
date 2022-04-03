@extends('Model/restaurantOwnerModel')

@section('title', 'Settings')

@section('content')

    <div id="container">
        <div class="view" id="view-settings">
            <h1>Settings</h1>


            {{--  Restaurant Info Row  --}}
            <div class="row">
                <div class="col-md-1"></div>


                {{--  Restaurant Info  --}}
                <div class="col-md-4">
                    <div class="card" style="width: 20rem;">
                        <div class="card-body">
                            <div class="settings-wrapper">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="settings-header">
                                            Restaurant Info
                                        </div>
                                        <div class="edit-btn">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#restaurant-basics">Edit</button>
                                            <div class="modal fade" id="restaurant-basics" tabindex="-1" role="dialog" aria-labelledby="request-label" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="request-label">Restaurant Details</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="post" action="{{route('radmin.saverestinfo')}}" enctype="multipart/form-data">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="restaurant-name">Restaurant name</label>
                                                                    <input type="text" name="rest_name" id="restaurant-name" class="form-control" required value="{{ $rest->rest_name }}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="restaurant-plan">Current Plan</label>
                                                                    <input type="text" name="rest_plan" id="restaurant-plan" class="form-control" readonly value="{{ $rest->plan }}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="restaurant-logo">Restaurant logo</label>
                                                                    <input type="file" class="rest_image_url" name="rest_image_url" id="restaurant-logo">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="restaurant-address">Restaurant Address</label>
                                                                    <input type="text" class="form-control" id="restaurant-address" name="rest_street" value="{{ $rest->rest_street }}">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label for="restaurant-address-road-no">Road no</label>
                                                                            <input type="text" name="road_no" id="restaurant-address-road-no" class="form-control" value="{{ $rest->road_no }}">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label for="restaurant-address-police-station">Area</label>
                                                                            <input type="text" name="police_station" id="restaurant-address-police-station" class="form-control"  value="{{ $rest->police_station }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label for="restaurant-address-postal-code">Postal code</label>
                                                                            <input type="text" name="rest_post_code" id="restaurant-address-postal-code" class="form-control"  value="{{ $rest->rest_post_code }}">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label for="restaurant-address-district">District</label>
                                                                            <select class="form-control" name="district_id" id="restaurant-address-district" >
                                                                                @foreach($districts as $district)
                                                                                    @if($district->district_id == $rest->district_id)
                                                                                     <option value="{{ $district->district_id }}" selected>{{ $district->district_name }}</option>
                                                                                    @else
                                                                                     <option value="{{ $district->district_id }}">{{ $district->district_name }}</option>
                                                                                     @endif

                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {{-- <div class="form-group">
                                                                    <label for="tax-no">Tax no</label>
                                                                    <input type="text" name="rest_tax_no" id="tax-no" class="form-control" value="{{ $rest->rest_tax_no }}">
                                                                </div> --}}
                                                                <div class="form-group">
                                                                    <label for="email">Email Id</label>
                                                                    <input type="email" disabled name="email" id="email" class="form-control"  value="{{ $rest->admin->email }}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="type">Restaurant Type</label>
                                                                    <input type="type" name="type" id="type" class="form-control"  value="{{ $rest->type }}">
                                                                    <small>Example: Fine Dining, Casual Dining, Family Style, Fast Casual,Fast Food, Cafe</small>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="type">Business Category</label>
                                                                    <select name="business_category" id="business_category" class="form-control">
                                                                        <option value="Restaurant" {{ $rest->business_category == "Restaurant" ? 'selected' : '' }}>Restaurant</option>
                                                                        <option value="Catering House"{{ $rest->business_category == "Catering House" ? 'selected' : '' }}>Catering House</option>
                                                                        <option value="Home Kitchen"{{ $rest->business_category == "Home Kitchen" ? 'selected' : '' }}>Home Kitchen</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="total_seats">Cuisines</label>
                                                                    <input type="cuisines" name="cuisines" id="cuisines" class="form-control"  value="{{ $rest->cuisines }}">
                                                                    <small>Example: Mexican, Italian, Indian, Thai ,Chinese</small>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="total_seats">Total Seats for Reservation</label>
                                                                    <input type="total_seats" name="total_seats" id="total_seats" class="form-control"  value="{{ $rest->total_seats }}">
                                                                    <small>Example: 50</small>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Average Cost</label><br>
                                                                    <label for="cost_$"><input type="radio" name="cost" value="$" id="cost_$" {{ $rest->cost == '$' ? 'checked' : '' }}> $</label>
                                                                    <label for="cost_$$"><input type="radio" name="cost" value="$$" id="cost_$$" {{ $rest->cost == '$$' ? 'checked' : '' }}> $$</label>
                                                                    <label for="cost_$$$"><input type="radio" name="cost" value="$$$" id="cost_$$$" {{ $rest->cost == '$$$' ? 'checked' : '' }}> $$$</label>
                                                                    <label for="cost_$$$$"><input type="radio" name="cost"  value="$$$$" id="cost_$$$$" {{ $rest->cost == '$$$$' ? 'checked' : '' }}> $$$$</label>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="contact">Contact no</label>
                                                                    <input type="text" name="phone" id="contact" class="form-control"  value="{{ $rest->phone }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Accepted Payment Method:</label>
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                <input type="checkbox" {{ isset($paymethod) && $paymethod->cash == true ? "checked" : " " }} name="payment_methods[]" value="cash" class="form-check-inline" id="payment-method"> Cash
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                <input type="checkbox" {{ isset($paymethod) && $paymethod->rocket == true ? "checked" : " " }} name="payment_methods[]" value="rocket" class="form-check-inline" id="payment-method"> Rocket
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                <input type="checkbox" {{ isset($paymethod) && $paymethod->visa == true ? "checked" : " " }} name="payment_methods[]" value="visa" class="form-check-inline" id="payment-method"> Visa Card
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                <input type="checkbox" {{ isset($paymethod) && $paymethod->nexaspay == true ? "checked" : " " }} name="payment_methods[]" value="nexaspay" class="form-check-inline" id="payment-method"> Nexas Pay
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                <input type="checkbox" {{ isset($paymethod) && $paymethod->mastercard == true ? "checked" : " " }} name="payment_methods[]" value="mastercard" class="form-check-inline" id="payment-method"> Master Card
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                <input type="checkbox" {{ isset($paymethod) && $paymethod->upay == true ? "checked" : " " }} name="payment_methods[]" value="upay" class="form-check-inline" id="payment-method"> Upay
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                <input type="checkbox" {{ isset($paymethod) && $paymethod->bkash == true ? "checked" : " " }} name="payment_methods[]" value="bkash" class="form-check-inline" id="payment-method"> BKash
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div id="map"></div>
                                        <input type="hidden" name="lat" id="lat" value="{{ $rest->rest_latitude }}"><input type="hidden" name="lng" id="lng" value="{{ $rest->rest_longitude }}">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                                                                <button type="submit" class="btn btn-primary">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div align="center">
                                            <i class="fas fa-university" style="font-size: 5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{--  Restaurant Properties  --}}
                <div class="col-md-4">
                    <div class="card" style="width: 20rem;">
                        <div class="card-body">
                            <div class="settings-wrapper">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="settings-header">
                                            Restaurant Properties
                                        </div>
                                        <div class="edit-btn">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#restaurant-properties">Edit</button>
                                            <div class="modal fade" id="restaurant-properties" tabindex="-1" role="dialog" aria-labelledby="request-label" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="request-label">Restaurant Properties</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="post" action="{{route('radmin.saverestproperty')}}">
                                                            {{ csrf_field() }}
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">
                                                                            @if($properties && $properties->live_music)
                                                                                <input type="checkbox" class="form-check-input" name="live_music" id="live-music" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="live_music" id="live-music">
                                                                            @endif
                                                                            <label class="form-check-label" for="live-music">Live Music <i class="fas fa-music"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">
                                                                            @if($properties && $properties->smoking_place)
                                                                                 <input type="checkbox" class="form-check-input" name="smoking_place" id="smoking" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="smoking_place" id="smoking">
                                                                            @endif

                                                                            <label class="form-check-label" for="smoking">Smoking Area <i class="fas fa-smoking"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">
                                                                            @if($properties && $properties->kids_corner)
                                                                                 <input type="checkbox" class="form-check-input" name="kids_corner" id="kids_corner" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="kids_corner" id="kids_corner">
                                                                            @endif
                                                                            <label class="form-check-label" for="kids_corner">Kids Corner<i class="fas fa-child"></i></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->praying_area)
                                                                                 <input type="checkbox" class="form-check-input" name="praying_area" id="praying_area" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="praying_area" id="praying_area">
                                                                            @endif
                                                                            <label class="form-check-label" for="praying_area">Praying area <i class="fas fa-archway"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->wifi)
                                                                                 <input type="checkbox" class="form-check-input" name="wifi" id="wifi" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="wifi" id="wifi">
                                                                            @endif
                                                                            <label class="form-check-label" for="wifi">Wifi <i class="fas fa-wifi"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">   
                                                                            @if($properties && $properties->parking)
                                                                                <input type="checkbox" class="form-check-input" name="parking" id="parking" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="parking" id="parking">
                                                                            @endif
                                                                            <label class="form-check-label" for="parking">Street Parking <i class="fas fa-parking"></i></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->self_service)
                                                                                <input type="checkbox" class="form-check-input" name="self_service" id="self_service" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="self_service" id="self_service">
                                                                            @endif
                                                                            <label class="form-check-label" for="self_service">Self Service <i class="fas fa-concierge-bell"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->tv)
                                                                                <input type="checkbox" class="form-check-input" name="tv" id="tv" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="tv" id="tv">
                                                                            @endif
                                                                            <label class="form-check-label" for="tv">TV <i class="fas fa-tv"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->game_zone)
                                                                                <input type="checkbox" class="form-check-input" name="game_zone" id="game_zone" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="game_zone" id="game_zone">
                                                                            @endif
                                                                            <label class="form-check-label" for="game_zone">Game zone <i class="fas fa-futbol-o"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->catering)
                                                                                <input type="checkbox" class="form-check-input" name="catering" id="catering" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="catering" id="catering">
                                                                            @endif
                                                                            <label class="form-check-label" for="catering">Catering <i class="fas fa-futbol-o"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->delivery)
                                                                                <input type="checkbox" class="form-check-input" name="delivery" id="delivery" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="delivery" id="delivery">
                                                                            @endif
                                                                            <label class="form-check-label" for="delivery">Delivery <i class="fas fa-futbol-o"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->good_for_kids)
                                                                                <input type="checkbox" class="form-check-input" name="good_for_kids" id="good_for_kids" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="good_for_kids" id="good_for_kids">
                                                                            @endif
                                                                            <label class="form-check-label" for="good_for_kids">Good for Kids <i class="fas fa-futbol-o"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->good_for_groups)
                                                                                <input type="checkbox" class="form-check-input" name="good_for_groups" id="good_for_groups" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="good_for_groups" id="good_for_groups">
                                                                            @endif
                                                                            <label class="form-check-label" for="good_for_groups">Good for Groups <i class="fas fa-futbol-o"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->takes_reservations)
                                                                                <input type="checkbox" class="form-check-input" name="takes_reservations" id="takes_reservations" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="takes_reservations" id="takes_reservations">
                                                                            @endif
                                                                            <label class="form-check-label" for="takes_reservations">Takes Reservations <i class="fas fa-futbol-o"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->take_out)
                                                                                <input type="checkbox" class="form-check-input" name="take_out" id="take_out" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="take_out" id="take_out">
                                                                            @endif
                                                                            <label class="form-check-label" for="take_out">Take Out <i class="fas fa-futbol-o"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->waiter_service)
                                                                                <input type="checkbox" class="form-check-input" name="waiter_service" id="waiter_service" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="waiter_service" id="waiter_service">
                                                                            @endif
                                                                            <label class="form-check-label" for="waiter_service">Waiter Service <i class="fas fa-futbol-o"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->walk_ins_welcome)
                                                                                <input type="checkbox" class="form-check-input" name="walk_ins_welcome" id="walk_ins_welcome" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="walk_ins_welcome" id="walk_ins_welcome">
                                                                            @endif
                                                                            <label class="form-check-label" for="walk_ins_welcome">Walk-Ins Welcome <i class="fas fa-futbol-o"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->parking_lot)
                                                                                <input type="checkbox" class="form-check-input" name="parking_lot" id="parking_lot" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="parking_lot" id="parking_lot">
                                                                            @endif
                                                                            <label class="form-check-label" for="parking_lot">Parking Lot <i class="fas fa-futbol-o"></i></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group form-check">    
                                                                            @if($properties && $properties->soft_music)
                                                                                <input type="checkbox" class="form-check-input" name="soft_music" id="soft_music" checked>
                                                                            @else
                                                                                <input type="checkbox" class="form-check-input" name="soft_music" id="soft_music">
                                                                            @endif
                                                                            <label class="form-check-label" for="soft_music">Soft Music <i class="fas fa-futbol-o"></i></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                                                                <button type="submit" class="btn btn-primary">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div align="center">
                                            <i class="fas fa-home" style="font-size: 5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{--  Working Hours Row  --}}
            <div class="row">
                <div class="col-md-1"></div>


                {{--  Working Hours  --}}
                <div class="col-md-4">
                    <div class="card" style="width: 20rem;">
                        <div class="card-body">
                            <div class="settings-wrapper">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="settings-header">
                                            Working Hours
                                        </div>
                                        <div class="edit-btn">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#working-hours">Edit</button>
                                            <div class="modal fade" id="working-hours" tabindex="-1" role="dialog" aria-labelledby="request-label" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="request-label">Working hours</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="post" action="{{route('radmin.saverestschedule')}}">
                                                            {{ csrf_field() }}
                                                            <div class="modal-body">

                                                            <?php
                                                                $i=0;
                                                                $startHour = 0;
                                                                $startMinute = 0;
                                                                $endHour = 0;
                                                                $endMinute = 0;
                                                            ?>
                                                            @foreach($weekdays as $day) 
                                                                @if($i < count($schedules) && $schedules[$i]->day == $day)
                                                                    <div class="form-group form-check">
                                                                        <input type="checkbox" class="form-check-input" name="{{ $schedules[$i]->day }}" id="{{ $schedules[$i]->day }}" checked>
                                                                        <label class="form-check-label" for="{{ $schedules[$i]->day }}">{{ $schedules[$i]->day }}
                                                                            <div class="row">
                                                                                <div class="col-md-5">
                                                                                    <select name="opening_time[]" id="start-time" class="form-control">
                                                                                        <option value="">Select start time</option>
                                                                                        @for ($hour = 0; $hour < 24; $hour++)
                                                                                            @for ($minute = 0; $minute < 59; $minute += 30)
                                                                                                <option value="{{ str_pad($hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minute, 2, '0', STR_PAD_LEFT) }}" {{ ($schedules[$i]->opening_time == str_pad($hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minute, 2, '0', STR_PAD_LEFT) . ":00") ? "selected" : "" }}>{{ str_pad($hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minute, 2, '0', STR_PAD_LEFT) }}</option>
                                                                                            @endfor
                                                                                        @endfor
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <div style="height: 20px; width: 100%; border-bottom: 1px solid black;"></div>
                                                                                </div>
                                                                                <div class="col-md-5">
                                                                                    <select name="closing_time[]" id="end-time" class="form-control">
                                                                                        <option value="">Select end time</option>
                                                                                        @for ($hour = 0; $hour < 24; $hour++)
                                                                                            @for ($minute = 0; $minute < 59; $minute += 30)
                                                                                                <option value="{{ str_pad($hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minute, 2, '0', STR_PAD_LEFT) }}" {{ ($schedules[$i]->closing_time == str_pad($hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minute, 2, '0', STR_PAD_LEFT) . ":00") ? "selected" : "" }}>{{ str_pad($hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minute, 2, '0', STR_PAD_LEFT) }}</option>
                                                                                            @endfor
                                                                                        @endfor
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </label>
                                                                    </div>

                                                                    <?php
                                                                        $i++;
                                                                    ?>
                                                                @else
                                                                    <div class="form-group form-check">
                                                                        <input type="checkbox" class="form-check-input" name="{{ $day }}" id="{{ $day }}">
                                                                        <label class="form-check-label" for="{{ $day }}">{{ $day }}
                                                                            <div class="row">
                                                                                <div class="col-md-5">
                                                                                    <select name="opening_time[]" id="start-time" class="form-control">
                                                                                        <option value="">Select start time</option>
                                                                                        @for ($hour = 0; $hour < 24; $hour++)
                                                                                            @for ($minute = 0; $minute < 59; $minute += 30)
                                                                                                <option value="{{ str_pad($hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minute, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minute, 2, '0', STR_PAD_LEFT) }}</option>
                                                                                            @endfor
                                                                                        @endfor
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <div style="height: 20px; width: 100%; border-bottom: 1px solid black;"></div>
                                                                                </div>
                                                                                <div class="col-md-5">
                                                                                    <select name="closing_time[]" id="end-time" class="form-control">
                                                                                        <option value="">Select end time</option>
                                                                                        @for ($hour = 0; $hour < 24; $hour++)
                                                                                            @for ($minute = 0; $minute < 59; $minute += 30)
                                                                                                <option value="{{ str_pad($hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minute, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minute, 2, '0', STR_PAD_LEFT) }}</option>
                                                                                            @endfor
                                                                                        @endfor
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                @endif
                                                            @endforeach

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                                                                <button type="submit" class="btn btn-primary">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div align="center">
                                            <i class="fas fa-clock" style="font-size: 5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{--  Menu Details  --}}
                <div class="col-md-4">
                    <div class="card" style="width: 20rem;">
                        <div class="card-body">
                            <div class="settings-wrapper">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="settings-header">
                                            Menu details
                                        </div>
                                        <div class="edit-btn">
                                            <a href="{{ route('radmin.menuDetails') }}" class="btn btn-primary">Edit</a>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div align="center">
                                            <i class="fas fa-book-open" style="font-size: 5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{--  Gallery Module  --}}
            {{--  <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-4">
                    <div class="card" style="width: 20rem;">
                        <div class="card-body">
                            <div class="settings-wrapper">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="settings-header">
                                            Gallery
                                        </div>
                                        <div class="edit-btn">
                                            <button class="btn btn-primary">Edit</button>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div align="center">
                                            <i class="fas fa-images" style="font-size: 5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  --}}
        </div>
    </div>

@endsection

@section('extra-js')
<script>
    $('li').removeClass('active');
    $('#menu-settings').parent().addClass('active');
    var the_lat = {{ $rest->rest_latitude != null ? $rest->rest_latitude : 22.357274 }};
    var the_lng = {{ $rest->rest_longitude != null ? $rest->rest_longitude : 91.837522 }};

    {{-- or 
    or  --}}

    function initMap() {
        var location = {lat: the_lat , lng: the_lng  };
        var map = new google.maps.Map(
            document.getElementById('map'), {
                zoom: 17, center: location
            });
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            draggable: true,
            title: "Change restaurant location"
        });

        google.maps.event.addListener(marker,'dragend',function(event) {
            $('#lat').val(event.latLng.lat());
            $('#lng').val(event.latLng.lng());
        });
    }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDkBasAFrf0StPsb9UI1DPppKmitW5_Xc&callback=initMap"></script>
@endsection

@section('extra-css')
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
@endsection