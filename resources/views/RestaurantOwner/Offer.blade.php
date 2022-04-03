@extends('Model/restaurantOwnerModel')

@section('title', 'Offer')

@section('content')

    <div id="container">
        
        <div class="view">
            <div class="d-flex">
                <div class="mr-auto p-2"><h2>Offers</h2></div>
                <div class="p-2"><a class="btn btn-success" href="{{ route('radmin.createoffer') }}">Add Offer</a></div>
            </div>
            
            @if($offers->count() > 0)
                <table class="table">
            <thead class="thead-light">
                <tr>
                <th scope="col">SL</th>
                <th scope="col">Title</th>
                <th scope="col">Type</th>
                <th scope="col">Food</th>
                <th scope="col">Offer Price</th>
                <th scope="col">Start</th>
                <th scope="col">End</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody style="background: #fff">
                @foreach($offers as $offer)
                    <tr>
                        <th scope="row">{{$offer->offer_id}}</th>
                        <td>{{$offer->offer_title}}</td>
                        <td>{{$offer["type"]->offer_type_name}}</td>
                        <td>{{$offer->food !== null ? $offer->food->food_name : 'N/A'}}</td>
                        <td>{{$offer->price !== null ? $offer->price : "N/A"}}</td>
                        <td>{{$offer->offer_starting_date}}</td>
                        <td>{{$offer->offer_ending_date}}</td>
                        <td style="{{$offer->status == 'Ongoing' ? 'color:var(--green)' : 'color:var(--red)'}}">{{$offer->status}}</td>
                        <td>
                            <a href="{{asset("offer/view/".$offer->offer_id)}}">View</a> •
                            @if($offer->status === 'Ongoing')
                                <a href="{{asset("offer/edit/".$offer->offer_id)}}">Edit</a> •
                            @endif
                            <a href="{{asset("offer/delete/".$offer->offer_id)}}" onclick="return confirm('Are you sure you want to delete this offer?');">Delete</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            </table>
            @else
            <div class="text-center">
                <p>No data is available right now</p>
            </div>
            @endif
            
        </div>
    </div>

@endsection

@section('extra-js')
    <!-- Inline JS -->
    <script>
        $('li').removeClass('active');
        $('#menu-offers').parent().addClass('active');
    </script>
@endsection
