@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Offer Details')

@section('brand', 'Offer Details')

@section('content')

    <div id="container">
            <div class="view">
                <div class="row">
                    <div class="col-md-3">
                    <img src="{{asset('/picture/offer/'.$offer->offer_image)}}" width="100%"/>
                    </div>
                    <div class="col-md-9">
                        <h3>{{$offer->offer_title}}</h3>
                        <p style="color: #000"><b>Restaurant:</b> {{$offer["restaurant"]->rest_name}}</p>
                        <p style="color: #000"><b>Offer Type:</b> {{$offer["type"]->offer_type_name}}</p>
                        <p style="color: #000"><b>Start Date:</b> {{$offer->offer_starting_date}}</p>
                        <p style="color: #000"><b>End Date:</b> {{$offer->offer_ending_date}}</p>
                        <p style="color: #000"><b>Food:</b> {{$offer->food !== null ? $offer->food->food_name : 'N/A'}}</p>
                        <p style="color: #000"><b>Offer Price:</b> {{$offer->price !== null ? $offer->price : "N/A"}}</p>
                        <p style="color: #000"><h6>Description:</h6> {{$offer->offer_desc}}</p>
                        <p style="color: #000"><h6>Terms and Condition:</h6> {{$offer->offer_tc}}</p>
                    </div>
                </div>
            </div>
    </div>

@endsection
