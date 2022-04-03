@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Add a business')

@section('brand', 'Businesses > update')

@section('extra-css')
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
@endsection

@section('content')
<div id="container">
    <div class="view" id="view-business">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#business-details">Business Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#working-hours">Working Hours</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#properties">Properties</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#menu-details">Menu Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#gallery">Gallery</a>
            </li>
        </ul>

        <div class="container-fluid" style="margin-top: 30px;">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="business-details">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ol>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ol>
                            </div>
                            @endif
                            <form action="{{ route("admin.edit_business_details", $restaurant->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method("PUT")
                                <div class="row">
                                    <div class="col-md-2">
                                        <img src="{{ $restaurant->getImageUrlAttribute() }}" alt="rest logo" class="img-responsive img-thumbnail">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="restaurant-name">Restaurant Name:</label>
                                            <input type="text" id="restaurant-name" name="rest_name" class="form-control" value="{{ $restaurant->rest_name }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="restaurant-plan">Current Plan:</label>
                                            <select name="rest_plan" id="restaurant-plan" class="form-control">
                                                    <option value="Hype" {{ $restaurant->plan == "Hype" ? "selected" : "" }}>Hype</option>
                                                    <option value="Splash" {{ $restaurant->plan == "Splash" ? "selected" : "" }}>Splash</option>
                                                    <option value="Finix" {{ $restaurant->plan == "Finix" ? "selected" : "" }}>Finix</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="restaurant-logo">Restaurant Logo:</label>
                                            <input type="file" accept="image/*" id="restaurant-logo" name="rest_image_url" class="form-control-file">
                                        </div>
                                        <div class="form-group">
                                            <label for="restaurant-address">Restaurant Address Line</label>
                                            <input type="text" id="restaurant-address" name="rest_street" class="form-control mb-3" value="{{ $restaurant->rest_street }}">
                                            <div class="container ml-4">
                                                <div class="row">
                                                    <div class="col-md-5 form-view-in" id="form-view">
                                                        <label for="road-no">Road No:</label>
                                                        <input type="text" id="road-no" name="rest_road_no" class="form-control" value="{{ $restaurant->road_no }}">
                                                    </div>
                                                    <div class="col-md-5 form-view-in">
                                                        <label for="police-station">Area:</label>
                                                        <input type="text" id="police-station" name="police_station" class="form-control" value="{{ $restaurant->police_station }}">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5 form-view-in" id="form-view">
                                                        <label for="postal-code">Postal Code:</label>
                                                        <input type="text" id="postal-code" name="rest_post_code" class="form-control" value="{{ $restaurant->rest_post_code }}">
                                                    </div>
                                                    <div class="col-md-5 form-view-in">
                                                        <label for="district">District:</label>
                                                        <select name="district_id" id="district" class="form-control">
                                                            @foreach ($districts as $district)
                                                                <option value="{{ $district->district_id }}" {{ $restaurant->district_id == $district->district_id ? "selected" : "" }}>{{ $district->district_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="registration-no">Registration no:</label>
                                            <input type="text" id="registration-no" name="rest_registration_no" class="form-control" value="{{ $restaurant->registration_number }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email Id:</label>
                                            <input type="email" id="email" name="rest_email" class="form-control" value="{{ $restaurant->admin->email }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="type">Restaurant Type</label>
                                            <input type="type" name="type" id="type" class="form-control"  value="{{ $restaurant->type }}">
                                            <small>Example: Fine Dining, Casual Dining, Family Style, Fast Casual,Fast Food, Cafe</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="type">Business Category</label>
                                            <select name="business_category" id="business_category" class="form-control">
                                                <option value="Restaurant" {{ $restaurant->business_category == "Restaurant" ? 'selected' : '' }}>Restaurant</option>
                                                <option value="Catering House"{{ $restaurant->business_category == "Catering House" ? 'selected' : '' }}>Catering House</option>
                                                <option value="Home Kitchen"{{ $restaurant->business_category == "Home Kitchen" ? 'selected' : '' }}>Home Kitchen</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="cuisines">Cuisines</label>
                                            <input type="cuisines" name="cuisines" id="cuisines" class="form-control"  value="{{ $restaurant->cuisines }}">
                                            <small>Example: Mexican, Italian, Indian, Thai ,Chinese</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="total_seats">Total Seats for Reservation</label>
                                            <input type="total_seats" name="total_seats" id="total_seats" class="form-control"  value="{{ $restaurant->total_seats }}">
                                            <small>Example: Mexican, Italian, Indian, Thai ,Chinese</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Average Cost</label><br>
                                            <label for="cost_$"><input type="radio" name="cost" value="$" id="cost_$" {{ $restaurant->cost == '$' ? 'checked' : '' }}> $</label>
                                            <label for="cost_$$"><input type="radio" name="cost" value="$$" id="cost_$$" {{ $restaurant->cost == '$$' ? 'checked' : '' }}> $$</label>
                                            <label for="cost_$$$"><input type="radio" name="cost" value="$$$" id="cost_$$$" {{ $restaurant->cost == '$$$' ? 'checked' : '' }}> $$$</label>
                                            <label for="cost_$$$$"><input type="radio" name="cost"  value="$$$$" id="cost_$$$$" {{ $restaurant->cost == '$$$$' ? 'checked' : '' }}> $$$$</label>
                                        </div>
                                        <div class="form-group">
                                            <label for="contact-no">Contact no:</label>
                                            <input type="tel" id="contact-no" name="rest_contact_no" class="form-control" value="{{ $restaurant->phone }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="restaurant-owner-name">Restaurant Owner name:</label>
                                            <input type="text" id="restaurant-owner-name" name="rest_owner_name" class="form-control" value="{{ $restaurant->admin->name }}">
                                        </div>
                                        <div class="form-group">
                                            <label for="restaurant-owner-contact-no">Restaurant Owner Contact no:</label>
                                            <input type="tel" id="restaurant-owner-contact-no" name="rest_owner_contact_no" class="form-control" value="{{ $restaurant->admin->contact_no }}">
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
                                    </div>
                                    <div class="col-md-4">
                                        <div id="map"></div>
                                        <input type="hidden" name="lat" id="lat" value="{{ $restaurant->rest_latitude }}"><input type="hidden" name="lng" id="lng" value="{{ $restaurant->rest_longitude }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="working-hours">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route("admin.edit_business_schedule", $restaurant->id) }}" method="post">
                                @csrf
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
                                <div class="form-group m-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="properties">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="{{route('admin.edit_business_property', $restaurant->id)}}">
                                @csrf
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
                                            <label class="form-check-label" for="parking">Parking <i class="fas fa-parking"></i></label>
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
                                            <label class="form-check-label" for="game_zone">Game zone <i class="fas fa-futbol"></i></label>
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
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="menu-details">
                    <div id="menuDetailsPage">
                        <menu-details
                            add_category_action="{{route('admin.addCategory', $restaurant->id)}}"
                            edit_category_action="{{route('admin.editCategory', $restaurant->id)}}"
                            add_menu_action = "{{route('admin.addMenu', $restaurant->id)}}"
                            edit_menu_action = "{{route('admin.editMenu', $restaurant->id)}}"
                            delete_menu_action = "{{route('admin.deleteMenu', $restaurant->id)}}"
                            toggle_menu_action = "{{route('admin.toggleMenu', $restaurant->id)}}"
                            foodandcategories="{{ route('admin.getFoodAndCategories', $restaurant->id) }}"
                            food_category_list="{{ route('global.foodCategoryList') }}"
                            site_url = {{ url('/') }}
                        ></menu-details>
                    </div>
                </div>
                <div class="tab-pane fade" id="gallery">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="business-name">Business name</label>
                                <div class="col-md-5">
                                    <select class="form-control" id="business-name">
                                        <option value="0">Select a business</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="menu-name">Menu name</label>
                                <div class="col-md-5">
                                    <select class="form-control" id="menu-name">
                                        <option value="0">Select a menu</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="business-name">Image</label>
                                <div class="col-md-5">
                                    <input type="file" accept="image/*" class="form-control-file">
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
    <script>
        $('li').removeClass('active');
        $('#menu-business').parent().addClass('active');

        $(document).ready(function() {
            let selectedTab = window.location.hash;
            $('.nav-link[href="' + selectedTab + '"]' ).trigger('click');
        });

        var the_lat = {{ $restaurant->rest_latitude != null ? $restaurant->rest_latitude : 22.357274 }};
        var the_lng = {{ $restaurant->rest_longitude != null ? $restaurant->rest_longitude : 91.837522 }};

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

<?php $VuePage = true ?>
@section('vue-js')
    <script src="{{ asset('js/pages/MenuDetails.js') }}"></script>
@endsection
