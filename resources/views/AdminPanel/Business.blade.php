@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Businesses')

@section('brand', 'Businesses')

@section('content')
    <div id="container">
        <div id="view-business">
            @if(session()->has('success'))
                <div class="col-md-12 alert alert-success">{{ session()->get('success') }}</div>
            @endif
            @if(session()->has('error'))
                <div class="col-md-12 alert alert-danger">{{ session()->get('error') }}</div>
            @endif
            <div class="col-md-12">
                <form action="{{ route('admin.business') }}" method="get">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="name">Business Name</label>
                            <input type="text" id="name" value="{{ request()->get('name') }}" name="name" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="email">Owners Email</label>
                            <input type="email" id="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="date">Date</label>
                            <input type="text" id="date" name="date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="All" {{ request()->input('status') == 'All' ? 'selected' : '' }}>All</option>
                                <option value="Published" {{ request()->input('status') == 'Published' ? 'selected' : '' }}>Published</option>
                                <option value="Unpublished" {{ request()->input('status') == 'Unpublished' ? 'selected' : '' }}>Unpublished</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary" style="margin-top: 35px;">Search</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="container">
                <div class="float-right"><a href="{{ route('admin.create_business') }}" class="btn btn-primary">Add a business</a></div>
            </div>
            <div style="margin-top: 40px;">
                
                <div id="pagination">Showing {{$restaurants->count()}} records out of {{\App\Models\RestaurantInfo::count()}} promotions</div>
                <div class="container text-center">
                    <table class="table table-striped">
                        <thead style="text-align: center;">
                            <tr>
                                <th scope="col">SL.</th>
                                <th scope="col">Date</th>
                                <th scope="col">Business Name</th>
                                <th scope="col">Owner Name</th>
                                <th scope="col">Status</th>
                                <th scope="col">Current Plan</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($restaurants as $restaurant)
                            <tr>
                                <th scope="col">{{ $restaurant->id }}</th>
                                <td>{{ $restaurant->created_at->toFormattedDateString() }}</td>
                                <td>{{ $restaurant->rest_name }}</td>
                                <td>{{ $restaurant->admin->name }}</td>
                                <td>{{ ($restaurant->is_published ? "Published" : "Unpublished") }}</td>
                                <td>{{ $restaurant->plan }}</td>
                                <td class="row d-flex justify-content-around align-items-center">
                                    <form action="{{ route('admin.restaurantPublisher', $restaurant->id) }}" method="POST">
                                        {{ csrf_field() }}
                                        @if($restaurant->is_published)
                                            <button class="btn btn-danger" type="submit" name="action" value="Unpublish">Unpublish</button>
                                        @else
                                            <button class="btn btn-success" type="submit" name="action" value="Publish">Publish</button>
                                        @endif
                                    </form>
                                    <div><a href="{{ route('admin.view_business', $restaurant->id) }}" class="btn btn-link">View</a></div>
                                    <div><a href="{{ route('admin.edit_business', $restaurant->id) }}" class="btn btn-link">Edit</a></div>
                                    <div>
                                        <form onsubmit="return confirm('Are you sure you want to delete this restaurant?');" action="{{ route('admin.deleteRestaurant', $restaurant->id) }}" method="POST">
                                            {{ csrf_field() }}
                                            <button class="btn btn-danger" type="submit" name="action" value="Delete">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $restaurants->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $('li').removeClass('active');
        $('#menu-business').parent().addClass('active');
    </script>
@endsection