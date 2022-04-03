@extends('Model/restaurantOwnerModel')

@section('title', 'Promotion')

@section('content')

    <div id="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session()->has('danger'))
            <div class="alert alert-danger">
                {{ session()->get('danger') }}
            </div>
        @endif

        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif
        @if($promotions->count() < 1)
        <div class="text-center">
            <p>No data is available right now</p>
        </div>
        @endif
        <div class="view" id="view-promotions">
            <h1>
                Promotion
                <div class="float-right">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#boost-modal">Boost</button>
                </div>
            </h1>
            <div class="card">
                <div class="card-body">
                    <div class="modal fade" id="boost-modal" tabindex="-1" role="dialog" aria-labelledby="request-label" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="request-label">Promotion Rates</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('radmin.createPromotion') }}" method="POST">
                                    {{ csrf_field() }}
                                    <div class="modal-body">
                                        <div class="row">
                                            @foreach ($packages as $package)
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="sponsor-ad">{{ $package->name }}</label>
                                                        <div id="sponsor-ad">
                                                            @php
                                                                $counter = 1;    
                                                            @endphp
                                                            @foreach ($package->prices as $price)
                                                                <div class="form-group form-check">
                                                                    <input type="radio" name="price" value="{{ $price->id }}" class="form-check-input" id="{{ $package->name }}-{{ $price->id }}" {{ $counter++ == 1 ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="{{ $package->name }}-{{ $price->id }}">Ad for {{ $price->duration }} days</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="payable">Payable</label>
                                                    <input type="text" name="payable" id="payable" class="form-control" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="method">Method</label>
                                                    <select name="method" id="method" class="form-control">
                                                        <option value="bkash">bKash</option>
                                                        <option value="cash">Cash</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Starting Date</th>
                                <th scope="col">Ending Date</th>
                                <th scope="col">Remaining Time</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Method</th>
                                <th scope="col">Transaction</th>
                                <th scope="col">Created at</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($promotions as $promotion)
                                <tr>
                                    <th scope="col">{{ $promotion->id }}</th>
                                    <td scope="col">{{ $promotion->starting_at != null ? \Carbon\Carbon::parse($promotion->starting_at)->toDateTimeString() : 'NULL' }}</td>
                                    <td scope="col">{{ $promotion->ending_at != null ? \Carbon\Carbon::parse($promotion->ending_at)->toDateTimeString() : 'NULL' }}</td>
                                    <td scope="col">
                                        @if ($promotion->is_active && $promotion->ending_at != NULL && $promotion->ending_at > \Carbon\Carbon::now())
                                            {{ \Carbon\Carbon::parse($promotion->ending_at)->diffForHumans() }}
                                        @else
                                            Ended
                                        @endif
                                    </td>
                                    <td scope="col">{{ $promotion->amount }}</td>
                                    <td scope="col">{{ $promotion->method }}</td>
                                    <td scope="col">
                                        @if($promotion->ending_at != NULL && $promotion->ending_at < \Carbon\Carbon::now())
                                            <span class="text-danger">Archived</span>
                                        @else
                                            @if($promotion->is_active) 
                                                <span class="text-success">Active</span> 
                                            @else 
                                                <span class="text-info">Pending</span> 
                                            @endif
                                        @endif
                                    </td>
                                    <td scope="col">{{ $promotion->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $promotions->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection


@section('extra-js')
    <script>
        $(document).ready(function(){
            $('li').removeClass('active');
            $('#menu-promotions').parent().addClass('active');
            
            window.packages = {
            @foreach($packages as $package)
                @foreach($package->prices as $price)
                    {!! '"'.$package->name."-".$price->id."\":".$price->price."," !!}
                @endforeach
            @endforeach
            };

            $('#payable').val(window.packages[$('input[name=price]').attr('id')]);

            window.getPrice = function(nameId){
                return window.packages[nameId];
            };

            $('input[name=price]').on('click', function(e){
                var id = e.target.getAttribute('id');

                $('#payable').val(window.getPrice(id));
            });
        });
    </script>
@endsection