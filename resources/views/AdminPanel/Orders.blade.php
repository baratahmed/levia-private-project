@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Orders')

@section('brand', 'Orders')

@section('content')
    <!-- Modal -->
    <div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="delete_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="delete_modal_label">Delete Promotion</h5>
            </div>
            <div class="modal-body">
                <p class="text-dark">Are you sure you want to delete this order?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Delete</button>
                <button type="button" class="btn btn-dark" data-dismiss="modal">Cancel</button>
            </div>
            </div>
        </div>
    </div>

    <div id="container">
        <div id="view-orders">
            <div>
                <div class="row">
                    <div class="col-md-2">
                        <label for="user">User</label>
                        <input type="text" id="user" name="user" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="date">Date</label>
                        <input type="text" id="date" name="date" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="name">Business name</label>
                        <input type="text" id="name" name="business_name" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary" style="margin-top: 35px;">Search</button>
                    </div>
                </div>
            </div>
            <div style="margin-top: 20px;">
                <div id="pagination">Showing 50 records out of 100 promotions</div>
                <div class="container">
                    <table class="table table-striped">
                        <thead style="text-align: center;">
                            <tr>
                                <th scope="col">SL.</th>
                                <th scope="col">Date</th>
                                <th scope="col">Business Name</th>
                                <th scope="col">User</th>
                                <th scope="col">Order no.</th>
                                <th scope="col">Price</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="col">1</th>
                                <td>01-01-2001</td>
                                <td>Hashtag Lounge</td>
                                <td>Md. Mahmudul Hasan</td>
                                <td>OR_532_139_08</td>
                                <td>1200</td>
                                <td>Accepted</td>
                                <td>
                                    <div class="d-flex justify-content-around">
                                        <a href="{{ route('admin.view_orders', 1) }}" class="btn btn-link">View</a>
                                        <a href="#" class="btn btn-link" data-toggle="modal" data-target="#delete_modal">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $('li').removeClass('active');
        $('#menu-orders').parent().addClass('active');
    </script>
@endsection