@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Add a business')

@section('brand', 'Businesses > create')

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
        <div class="container-fluid" style="margin-top: 20px;">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ol>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ol>
            </div>
            @endif
            <form action="{{ route('admin.create_business') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="restaurant-name">Restaurant Name:</label>
                                    <input type="text" id="restaurant-name" name="rest_name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="restaurant-plan">Select Plan:</label>
                                    <select name="rest_plan" id="restaurant-plan" class="form-control">
                                            <option value="Hype">Hype</option>
                                            <option value="Splash">Splash</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="restaurant-logo">Restaurant Logo:</label>
                                    <input type="file" accept="image/*" id="restaurant-logo" name="rest_image_url" class="form-control-file">
                                </div>
                                <div class="form-group">
                                    <label for="restaurant-address">Restaurant Address Line</label>
                                    <input type="text" id="restaurant-address" name="rest_street" class="form-control mb-3">
                                    <div class="container ml-4">
                                        <div class="row">
                                            <div class="col-md-5 form-view-in" id="form-view">
                                                <label for="road-no">Road No:</label>
                                                <input type="text" id="road-no" name="rest_road_no" class="form-control">
                                            </div>
                                            <div class="col-md-5 form-view-in">
                                                <label for="police-station">Area:</label>
                                                <input type="text" id="police-station" name="police_station" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 form-view-in" id="form-view">
                                                <label for="postal-code">Postal Code:</label>
                                                <input type="text" id="postal-code" name="rest_post_code" class="form-control">
                                            </div>
                                            <div class="col-md-5 form-view-in">
                                                <label for="district">District:</label>
                                                <select name="district_id" id="district" class="form-control">
                                                    @foreach ($districts as $district)
                                                        <option value="{{ $district->district_id }}">{{ $district->district_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="registration-no">Registration no:</label>
                                    <input type="text" id="registration-no" name="rest_registration_no" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Id:</label>
                                    <input type="email" id="email" name="rest_email" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="contact-no">Contact no:</label>
                                    <input type="tel" id="contact-no" name="rest_contact_no" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="restaurant-owner-name">Restaurant Owner name:</label>
                                    <input type="text" id="restaurant-owner-name" name="rest_owner_name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="restaurant-owner-contact-no">Restaurant Owner Contact no:</label>
                                    <input type="tel" id="restaurant-owner-contact-no" name="rest_owner_contact_no" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="restaurant-owner-password">Restaurant Owner Password:</label>
                                    <input type="password" id="restaurant-owner-password" name="rest_owner_password" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Accepted Payment Method:</label>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <label>
                                                <input type="checkbox" class="form-check-inline" id="payment-method"> Cash
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>
                                                <input type="checkbox" class="form-check-inline" id="payment-method"> Rocket
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>
                                                <input type="checkbox" class="form-check-inline" id="payment-method"> Visa Card
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>
                                                <input type="checkbox" class="form-check-inline" id="payment-method"> Nexas Pay
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>
                                                <input type="checkbox" class="form-check-inline" id="payment-method"> Master Card
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>
                                                <input type="checkbox" class="form-check-inline" id="payment-method"> Upay
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>
                                                <input type="checkbox" class="form-check-inline" id="payment-method"> BKash
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="map"></div>
                                <input type="hidden" id="lat" name="lat" value="22.3245"><input type="hidden" id="lng" name="lng" value="91.8117">
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </div>
                </div>
            </form>
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
            var agrabad = {lat: 22.3245, lng: 91.8117};
            var map = new google.maps.Map(
                document.getElementById('map'), {
                    zoom: 17, center: agrabad
                });
            var marker = new google.maps.Marker({
                position: agrabad,
                map: map,
                draggable: true,
                title: "Select restaurant location"
            });

            google.maps.event.addListener(marker,'dragend',function(event) {
                $('#lat').val(event.latLng.lat());
                $('#lng').val(event.latLng.lng());
            });
        }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDkBasAFrf0StPsb9UI1DPppKmitW5_Xc&callback=initMap"></script>
@endsection