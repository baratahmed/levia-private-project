@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Offers')

@section('brand', 'Offers')

@section('content')

    <div id="container">
        @if($offers->count() < 1)
            <div class="text-center">
                <p>No data is available right now</p>
            </div>
        @endif
        <div class="view">
            <div class="d-flex">
                <div class="mr-auto p-2"><h2>Offers</h2></div>
                <div class="p-2"><a class="btn btn-success" href="{{ route('admin.createoffer') }}">Add Offer</a></div>
            </div>
            <div id="pagination">Showing {{ $offers->count() }} records out of {{ $total }} offers</div>

            <table class="table table-striped" style="margin-top: 10px;">
            <thead class="thead-dark">
                <tr>
                <th scope="col">SL</th>
                <th scope="col">Business Name</th>
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
                        <td>{{$offer['restaurant']->rest_name}}</td>
                        <td>{{$offer->offer_title}}</td>
                        <td>{{$offer["type"]->offer_type_name}}</td>
                        <td>{{$offer->food !== null ? $offer->food->food_name : 'N/A'}}</td>
                        <td>{{$offer->price !== null ? $offer->price : "N/A"}}</td>
                        <td>{{$offer->offer_starting_date}}</td>
                        <td>{{$offer->offer_ending_date}}</td>
                        <td style="{{$offer->status == 'Ongoing' ? 'color:var(--green)' : 'color:var(--red)'}}">{{$offer->status}}</td>
                        <td>
                            <form action="{{ route('admin.deleteOffer') }}?offer_id={{ $offer->offer_id }}" onsubmit="return confirm('Are you sure you want to delete this offer?')" method="POST">
                                @csrf
                                @method('delete')
                                <a href="{{route('admin.view_offer', [$offer->offer_id])}}" class="btn btn-sm btn-primary">View</a>
                                <a href="{{route('admin.editOffer', [$offer->offer_id])}}" class="btn btn-sm btn-primary">Edit</a>
                                <button name="action" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            </table>
            {{ $offers->links() }}
        </div>
    </div>

@endsection
