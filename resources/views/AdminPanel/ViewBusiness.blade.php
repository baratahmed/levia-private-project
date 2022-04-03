@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Add a business')

@section('brand', 'Businesses > view')

@section('extra-css')
    <style>
        #map {
            height: 400px;
            width: 100%;
        }

        .flex-container {
            display:flex;
            flex-direction:row;
            flex-wrap:wrap;
        }
        .gallery .gallery-image {
            height:300px;
            
            margin-right:10px;
            margin-bottom:10px;
            text-align:center;
        }
        .gallery .gallery-image img {
            height:250px;
        }
        .gallery .gallery-image p {
            text-align:center;
            margin-top:10px;
            color:black;
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
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <img src="{{ $restaurant->getImageUrlAttribute() }}" alt="rest logo" class="img-responsive img-thumbnail">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="restaurant-name">Restaurant Name:</label>
                                        <input type="text" id="restaurant-name" value="{{ $restaurant->rest_name }}" readonly class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="restaurant-plan">Current Plan:</label>
                                        <input type="text" id="restaurant-plan" value="{{ $restaurant->plan }}" readonly class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="restaurant-address">Restaurant Address Line</label>
                                        <input type="text" id="restaurant-address" value="{{ $restaurant->rest_street }}" readonly class="form-control mb-3">
                                        <div class="container ml-4">
                                            <div class="row">
                                                <div class="col-md-5 form-view-in" id="form-view">
                                                    <label for="road-no">Road No:</label>
                                                    <input type="text" id="road-no" value="{{ $restaurant->road_no }}" readonly class="form-control">
                                                </div>
                                                <div class="col-md-5 form-view-in">
                                                    <label for="police-station">Area:</label>
                                                    <input type="text" id="police-station" value="{{ $restaurant->police_station }}" readonly class="form-control">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5 form-view-in" id="form-view">
                                                    <label for="postal-code">Postal Code:</label>
                                                    <input type="text" id="postal-code" value="{{ $restaurant->rest_post_code }}" readonly class="form-control">
                                                </div>
                                                <div class="col-md-5 form-view-in">
                                                    <label for="district">District:</label>
                                                    <input type="text" id="district" value="{{ $restaurant->district }}" readonly class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="registration-no">Registration no:</label>
                                        <input type="text" id="registration-no" value="{{ $restaurant->registration_number }}" readonly class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email Id:</label>
                                        <input type="email" id="email" value="{{ $restaurant->admin->email }}" readonly class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="type">Restaurant Type</label>
                                        <input disabled type="type" name="type" id="type" class="form-control"  value="{{ $restaurant->type }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="cuisines">Cuisines</label>
                                        <input disabled type="cuisines" name="cuisines" id="cuisines" class="form-control"  value="{{ $restaurant->cuisines }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="total_seats">Total Seats for Reservation</label>
                                        <input disabled type="total_seats" name="total_seats" id="total_seats" class="form-control"  value="{{ $restaurant->total_seats }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Average Cost : {{ $restaurant->cost }}</label><br>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact-no">Contact no:</label>
                                        <input type="tel" id="contact-no" value="{{ $restaurant->phone }}" readonly class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="restaurant-owner-name">Restaurant Owner name:</label>
                                        <input type="text" id="restaurant-owner-name" value="{{ $restaurant->admin->name }}" readonly class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="restaurant-owner-contact-no">Restaurant Owner Contact no:</label>
                                        <input type="tel" id="restaurant-owner-contact-no" value="{{ $restaurant->admin->contact_no }}" readonly class="form-control">
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="working-hours">
                    <div class="card">
                        <div class="card-body">
                            <?php
                                $i=0;
                            ?>
                            @foreach($weekdays as $day)
                                @if($i < count($schedules) && $schedules[$i]->day == $day)
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" name="{{ $schedules[$i]->day }}" id="{{ $schedules[$i]->day }}" disabled checked>
                                        <label class="form-check-label" for="{{ $schedules[$i]->day }}">{{ $schedules[$i]->day }}
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="text" name="opening_time[]" id="sunday-start-time" class="form-control" placeholder="Start" value="{{ $schedules[$i]->opening_time }}" readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <div style="height: 20px; width: 100%; border-bottom: 1px solid black;"></div>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" name="closing_time[]" id="sunday-end-time" class="form-control" placeholder="End" value="{{ $schedules[$i]->closing_time }}" readonly>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <?php
                                        $i++;
                                    ?>
                                @endif
                            @endforeach
                            @if ($i == 0)
                                <div class="text-center">No schedule defined</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="properties">
                    <div class="card">
                        <div class="card-body">
                            <div class="container">
                                <?php
                                    $propCount = 0;
                                ?>
                                @if ($properties)
                                    @if ($properties->live_music)
                                        <?php $propCount++; ?>
                                        <p class="text-dark">
                                            <i class="fas fa-long-arrow-alt-right"></i>&nbsp;&nbsp; Live Music <i class="fas fa-music"></i>
                                        </p>
                                    @endif
                                    @if ($properties->smoking_place)
                                        <?php $propCount++; ?>
                                        <p class="text-dark">
                                            <i class="fas fa-long-arrow-alt-right"></i>&nbsp;&nbsp; Smoking Area <i class="fas fa-smoking"></i>
                                        </p>
                                    @endif
                                    @if ($properties->kids_corner)
                                        <?php $propCount++; ?>
                                        <p class="text-dark">
                                            <i class="fas fa-long-arrow-alt-right"></i>&nbsp;&nbsp; Kids Corner <i class="fas fa-child"></i>
                                        </p>
                                    @endif
                                    @if ($properties->praying_area)
                                        <?php $propCount++; ?>
                                        <p class="text-dark">
                                            <i class="fas fa-long-arrow-alt-right"></i>&nbsp;&nbsp; Praying Area <i class="fas fa-archway"></i>
                                        </p>
                                    @endif
                                    @if ($properties->wifi)
                                        <?php $propCount++; ?>
                                        <p class="text-dark">
                                            <i class="fas fa-long-arrow-alt-right"></i>&nbsp;&nbsp; Wifi <i class="fas fa-wifi"></i>
                                        </p>
                                    @endif
                                    @if ($properties->parking)
                                        <?php $propCount++; ?>
                                        <p class="text-dark">
                                            <i class="fas fa-long-arrow-alt-right"></i>&nbsp;&nbsp; Parking <i class="fas fa-parking"></i>
                                        </p>
                                    @endif
                                    @if ($properties->self_service)
                                        <?php $propCount++; ?>
                                        <p class="text-dark">
                                            <i class="fas fa-long-arrow-alt-right"></i>&nbsp;&nbsp; Self Service <i class="fas fa-concierge-bell"></i>
                                        </p>
                                    @endif
                                    @if ($properties->tv)
                                        <?php $propCount++; ?>
                                        <p class="text-dark">
                                            <i class="fas fa-long-arrow-alt-right"></i>&nbsp;&nbsp; TV <i class="fas fa-tv"></i>
                                        </p>
                                    @endif
                                    @if ($properties->gamezone)
                                        <?php $propCount++; ?>
                                        <p class="text-dark">
                                            <i class="fas fa-long-arrow-alt-right"></i>&nbsp;&nbsp; Game Zone <i class="fas fa-futbol"></i>
                                        </p>
                                    @endif
                                @endif
                                @if ($propCount == 0)
                                    <div class="text-center">No properties defined</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="menu-details">
                    <div id="menuDetailsViewPage">
                        <menu-details-view
                            toggle_menu_action = "{{route('admin.toggleMenu', $restaurant->id)}}"
                            foodandcategories="{{ route('admin.getFoodAndCategories', $restaurant->id) }}"
                            food_category_list="{{ route('global.foodCategoryList') }}"
                            site_url = {{ url('/') }}
                        ></menu-details-view>
                    </div>
                </div>
                <div class="tab-pane fade" id="gallery">
                    <div class="card">
                        <div class="card-body">
                            <div class="gallery">
                                <div class="header">
                                    <h3>Gallery</h3>
                                </div>
                                <div class="body">
                                    <div class="flex-container">
                                        @if($restaurant->rest_image_url != null && $restaurant->rest_image_url != "default.jpg")
                                            
                                                <div class="gallery-image">
                                                    <img src="{{ $restaurant->imageUrl }}" alt="{{ $restaurant->rest_name }}">
                                                    <p>{{ $restaurant->rest_image_url }}</p>
                                                </div>
                                            
                                        @endif
                                        @foreach ($foods as $food)
                                            @if($food->food_image_url != null && $food->food_image_url != "default.jpg")
                                                
                                                    <div class="gallery-image">
                                                        <img src="{{ $food->foodImage }}" alt="{{ $food->food_name }}">
                                                        <p>{{ $food->food_image_url }}</p>
                                                    </div>
                                                
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
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

        function initMap() {
            var location = {lat: {{ $restaurant->rest_latitude }}, lng: {{ $restaurant->rest_longitude }} };

            var map = new google.maps.Map(
                document.getElementById('map'), {zoom: 17, center: location});

            var marker = new google.maps.Marker({position: location, map: map});
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDkBasAFrf0StPsb9UI1DPppKmitW5_Xc&callback=initMap"></script>
@endsection

<?php $VuePage = true ?>
@section('vue-js')
    <script src="{{ asset('js/pages/MenuDetailsView.js') }}"></script>
@endsection