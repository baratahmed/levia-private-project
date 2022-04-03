@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Users')

@section('brand', 'Users')

@section('content')
    <!-- Modal -->
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
                    <form action="{{ route('admin.deleteUser') }}" method="POST">
                        @csrf
                        @method('delete')
                        <input type="hidden" name="user_id" id="user_id" value="" />
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <a class="btn btn-default" data-dismiss="modal">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="container">
        <div id="view-users">
            <div class="col-md-12">
                <form action="{{route('admin.users')}}" method="GET">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="name">Username</label>
                            <input type="text" id="name" value="{{ request()->get('username') }}" name="username" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="email">User Email</label>
                            <input type="email" id="email" value="{{ request()->get('email') }}" name="email" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="date">Joining Date</label>
                            <input type="date" id="date" value="{{ request()->get('date') }}" name="date" class="form-control">
                        </div>
                        {{-- <div class="col-md-2">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                
                            </select>
                        </div> --}}
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary" style="margin-top: 35px;">Search</button>
                        </div>
                    </div>
                </form>
            </div>
            <div style="margin-top: 20px;">
                <div id="pagination">Showing {{$users->count()}} records out of {{ $users->total() }} users</div>
                <div class="container text-center">
                    <table class="table table-striped">
                        <thead style="text-align: center;">
                            <tr>
                                <th scope="col">SL. (User ID)</th>
                                <th scope="col">Joined Date</th>
                                <th scope="col">Username</th>
                                <th scope="col">Email</th>
                                <th scope="col">Contact no.</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <th scope="col">{{$user->id}}</th>
                                    <td>{{ $user->created_at->toFormattedDateString() }}</td>
                                    <td>{{ $user->fb_profile_name }}</td>
                                    <td>{{ $user->user_email }}</td>
                                    <td>{{ $user->contact_no }}</td>
                                    <td>Active</td>
                                    <td class="row">
                                        <div class="col-md-6"><a href="{{ route('admin.view_users', $user->id) }}" class="btn btn-link">View</a></div>
                                        <div class="col-md-6"><a href="#" class="btn btn-link delete-user-button" data-userid="{{ $user->id }}" data-toggle="modal" data-target="#delete_modal">Delete</a></div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $users->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $(document).ready(function(){
            $('li').removeClass('active');
            $('#menu-users').parent().addClass('active');

            // Delete User
            $(document).on('click', '.delete-user-button', function(e){
                e.preventDefault();
                console.log("clicked");
                // console.log(e.target.getAttribute('data-userid'));
                $('#user_id').val(e.target.getAttribute('data-userid'));
            })
        });
    </script>
@endsection