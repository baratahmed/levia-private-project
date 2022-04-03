@extends('Model/restaurantOwnerModel')

@section('title', 'Edit Offer')

@section('content')

    <form method="post" action="{{route('editOffer')}}" enctype="multipart/form-data">
        {{ csrf_field() }}

        {{-- Basic Information --}}
        <div class="modal-dialog modal-lg" style="margin-bottom: -25px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="request-label">Edit Offer</h5>
                </div>

                <input type="hidden" name="offer_id" value="{{$offer->offer_id}}"/>

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
                        <label for="restaurant-name">Title</label>
                        <input type="text" name="offer_title" value="{{$offer->offer_title}}" id="restaurant-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="restaurant-logo">Picture</label>
                        <input type="file" class="rest_image_url" name="offer_image" id="restaurant-logo">
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="restaurant-address-district">Type</label>
                                <select class="form-control" name="offer_type_id" id="restaurant-address-district" required>
                                    @foreach ($offer_type as $type)
                                        @if($offer->offer_type_id == $type->offer_type_id)
                                            <option value="{{ $type->offer_type_id }}" selected>{{ $type->offer_type_name }}</option>
                                        @else
                                            <option value="{{ $type->offer_type_id }}">{{ $type->offer_type_name }}</option>
                                        @endif

                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="restaurant-address-road-no">Start</label>
                                <input type="date" name="offer_starting_date" value="{{$offer->offer_starting_date}}" id="restaurant-address-road-no" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="restaurant-address-police-station">End</label>
                                <input type="date"  name="offer_ending_date" value="{{$offer->offer_ending_date}}"  id="restaurant-address-police-station" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="restaurant-logo">Description</label>
                        <textarea name="offer_desc" id="restaurant-name" class="form-control" rows="3">{{$offer->offer_desc}}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="restaurant-logo">Terms and Condition</label>
                        <textarea name="offer_tc" id="restaurant-name" class="form-control" rows="3">{{$offer->offer_tc}}</textarea>
                    </div>

                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="specific_food" id="specific_food" value="yes" {{ $offer->food_id !== null ? "checked" : "" }}>
                            Offer for specific menu?
                        </label>
                    </div>
                    <div class="form-group specific_food_box" style="{{ $offer->food_id === null ? "display:none;" : "" }}">
                        <label for="choose_food">Choose Menu</label>
                        <select class="form-control" name="food_id" id="choose_food">
                            <option value="-1">All</option>
                            @php
                                $food_details = \App\Models\RestFoodDetailsDataset::for($rest->id)->get();
                            @endphp
                            @foreach ($food_details as $food)
                                <option value="{{ $food->food_id }}" class="for_rest" {{ $offer->food_id === $food->food_id ? 'selected' : '' }}>{{ $food->food_name }} ({{ number_format($food->unit_price, 2) }} BDT)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="offer_price">Offer Price (Optional)</label>
                        <input type="text" name="offer_price" id="offer_price" class="form-control" value="{{ number_format($offer->price, 2) }}">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </form>

@endsection


@section('extra-js')
    <script>
        $(function(){
            $('#specific_food').on('change', function(e){
                if ($(this).is(':checked')){
                    $('.specific_food_box').slideDown(200);
                } else {
                    $('.specific_food_box').slideUp(200);
                }
            });
        });
    </script>
@endsection