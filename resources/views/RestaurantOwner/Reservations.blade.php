@extends('Model/restaurantOwnerModel')

@section('title', 'Offer')

@section('content')

    <div id="container">
        
        <div class="view">
            <div class="d-flex">
                <div class="mr-auto p-2"><h2>Reservations</h2></div>
            </div>
            
            @if($reservations->count() > 0)
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">SL</th>
                            <th scope="col">Reservation Time</th>
                            <th scope="col">User</th>
                            <th scope="col">Seats</th>
                            <th scope="col">Paid</th>
                            <th scope="col">Accepted</th>
                            <th scope="col">Reserved On</th>
                        </tr>
                    </thead>
                    <tbody style="background: #fff">
                    @foreach($reservations as $reserve)
                        <tr {!! $reserve->is_seen == false ? 'style="background:#efafa2"' : '' !!}>
                            <th scope="row">{{$reserve->id}}</th>
                            <td scope="row" title="{{\Carbon\Carbon::parse($reserve->reservation_time)->diffForHumans()}}">{{\Carbon\Carbon::parse($reserve->reservation_time)->toDayDateTimeString()}}</td>
                            <?php $user = \App\Models\User::findOrFail($reserve->user_id); ?>
                            <td scope="row" >{{ $user->fb_profile_name }} | ID: {{ $user->id }}</td>
                            <td scope="row" >{{ $reserve->seats }}</td>
                            <td scope="row" >{{ $reserve->is_paid ? "Yes" : "No" }}</td>
                            <td scope="row" >
                                <div>
                                    @if($reserve->is_accepted)
                                        Accepted
                                    @else
                                        <button class="btn btn-sm btn-success accept-button" data-reserveid="{{ $reserve->id }}">Accept</button>
                                    @endif
                                </div>
                            </td>
                            <td scope="row" >{{ $reserve->created_at->toDayDateTimeString() }}</td>
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
        $('#menu-reservations').parent().addClass('active');

        $(document).on('click', '.accept-button', function(e){
            e.preventDefault();
            $elem = $(this);

            $.ajax({
                method: "POST",
                url: "{{ route('radmin.acceptReservation') }}",
                data: { id: $(this).data('reserveid'), _token: "{{ csrf_token() }}" }
            })
                .done(function( msg ) {
                    {{-- console.log(msg); --}}
                    $elem.parent().html("Accepted");
                })
                .fail(function(msg){
                    console.log(msg);
                    alert('Something went wrong');
                });
        })
    </script>
@endsection