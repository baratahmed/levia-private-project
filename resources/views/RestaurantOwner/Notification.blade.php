@extends('Model/restaurantOwnerModel')

@section('title', 'Notifications')

@section('content')

    <div id="container">
        <div class="view" id="view-notification">
            <h1>Notifications</h1>
            <div class="card">
                <div class="card-body" id="notification-list">
                    <div class="card-list">
                        <div class="row">
                            <div class="col-md-2">
                                <img src="{{asset('content/img/man.ico')}}" class="avatar-img" style="height: 5rem;">
                            </div>
                            <div class="col-md-9">
                                <div class="notification-text">
                                    You have an order from Syed Mohammad Yasir of total 1050tk
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="close close-notification">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-list">
                        <div class="row">
                            <div class="col-md-2">
                                <img src="{{asset('content/img/man.ico')}}" class="avatar-img" style="height: 5rem;">
                            </div>
                            <div class="col-md-9">
                                <div class="notification-text">
                                    Syed Mohammad Yasir rated your restaurant Barcode Cafe
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="close close-notification">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-list">
                        <div class="row">
                            <div class="col-md-2">
                                <img src="{{asset('content/img/man.ico')}}" class="avatar-img" style="height: 5rem;">
                            </div>
                            <div class="col-md-9">
                                <div class="notification-text">
                                    Syed Mohammad Yasir reviewed the menu Chicken Masala of your restaurant Barcode Cafe
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="close close-notification">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection