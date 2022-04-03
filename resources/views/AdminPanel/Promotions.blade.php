@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Promotions')

@section('brand', 'Promotions')

@section('content')
    <!-- Modal -->
    {{-- <div class="modal fade" id="approve_modal" tabindex="-1" role="dialog" aria-labelledby="approve_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approve_modal_label">Approve Promotion</h5>
                </div>
                <div class="modal-body">
                    <p class="text-success">Think again! Deleting this record will Approve all associated promotional data.</p>
                    <p class="text-dark">Are you sure you want to Approve this record?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Approve</button>
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="delete_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="delete_modal_label">Delete Promotion</h5>
                </div>
                <div class="modal-body">
                    <p class="text-danger">Warning! Deleting this record will delete all associated promotional data.</p>
                    <p class="text-dark">Are you sure you want to delete this record?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Delete</button>
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div> --}}

    <div id="container">
        <div id="view-promotions">
            <div class="col-md-12">
                <form>
                    <div class="row">
                        <div class="col-md-2">
                            <label for="name">Business name</label>
                            <input type="text" id="name" value="{{ request()->business_name }}" name="business_name" class="form-control">
                        </div>
                        {{--  <div class="col-md-2">
                            <label for="date">Date</label>
                            <input type="text" id="date" name="date" class="form-control">
                        </div>  --}}
                        <div class="col-md-2">
                            <label for="package">Package</label>
                            <select name="package" id="package" class="form-control">
                                <option value="all">All</option>
                                @foreach ($packages as $package)
                                    @foreach ($package->prices as $price)
                                        <option value="{{ $price->id }}" {{ request()->package == $price->id ? 'selected' : '' }}>{{ $package->name }}-{{ $price->duration }} days</option>
                                    @endforeach    
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="only">From</label>
                            <select name="only" id="only" class="form-control">
                                <option value="all">All</option>
                                <option value="active" {{ request()->only == 'active' ? 'selected' : '' }}>Active</option>
                            </select>
                        </div>
                        {{--  <div class="col-md-2">
                            <label for="package_id">Package ID</label>
                            <input type="text" id="package_id" name="package_id" class="form-control">
                        </div>  --}}
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary" style="margin-top: 35px;">Search</button>
                        </div>
                        @if ($rest == null)
                            <div class="col-md-12 alert-danger">
                                No business with this name. Showing all businesses.
                            </div>
                        @endif
                    </div>
                </form>
            </div>
            <div style="margin-top: 20px;">
                <div id="pagination">Showing {{ $promotions->count() }} records out of {{ $total }} promotions</div>
                <div class="">
                    <table class="table table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">ID.</th>
                                <th scope="col">Starting at</th>
                                <th scope="col">Ending at</th>
                                <th scope="col">Remaining Time</th>
                                <th scope="col">Business Name</th>
                                <th scope="col">Duration</th>
                                <th scope="col">Price</th>
                                <th scope="col">Package</th>
                                <th scope="col">Created at</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($promotions as $promotion)
                                <tr>
                                    <th scope="col">1</th>
                                    <td scope="col">{{ $promotion->starting_at != null ? \Carbon\Carbon::parse($promotion->starting_at)->toDateTimeString() : 'NULL' }}</td>
                                    <td scope="col">{{ $promotion->ending_at != null ? \Carbon\Carbon::parse($promotion->ending_at)->toDateTimeString() : 'NULL' }}</td>
                                    <td scope="col">
                                        @if ($promotion->is_active && $promotion->ending_at != NULL && $promotion->ending_at > \Carbon\Carbon::now())
                                            {{ \Carbon\Carbon::parse($promotion->ending_at)->diffForHumans() }}
                                        @else
                                            Ended
                                        @endif
                                    </td>
                                    <td scope="col">{{ $promotion->restaurant->rest_name }}</td>
                                    <td scope="col">{{ $promotion->price->duration }} days</td>
                                    <td scope="col">{{ $promotion->price->price }}</td>
                                    <td scope="col">{{ $promotion->price->pack->name }}</td>
                                    <td scope="col">{{ $promotion->created_at->diffForHumans() }}</td>
                                    <td scope="col" class="juxtaposed-child">
                                        @if($promotion->ending_at != NULL && $promotion->ending_at < \Carbon\Carbon::now())
                                            <span class="text-danger">Archived</span>
                                        @else
                                            @if($promotion->is_active)
                                                <span class="text-success">Active</span>
                                            @else
                                                <form action="{{ route('admin.managePromotion') }}?promo_id={{ $promotion->id }}" onsubmit="return confirm('Are you sure about approving this promotion?')" method="POST">
                                                    {{ csrf_field() }}
                                                    <button name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                            @endif
                                                <form action="{{ route('admin.managePromotion') }}?promo_id={{ $promotion->id }}" onsubmit="return confirm('Are you sure you want to delete this promotion?')" method="POST">
                                                    {{ csrf_field() }}
                                                    <button name="action" value="delete" class="btn btn-sm btn-danger ml-1">Delete</button>
                                                </form>
                                        @endif
                                    </td>
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
        $('li').removeClass('active');
        $('#menu-promotions').parent().addClass('active');
    </script>
@endsection

@section('extra-css')
    <style>
        .juxtaposed-child > *{
            float:left;
            display:inline;
        }
    </style>
@endsection