@extends('Model/restaurantOwnerModel')

@section('title', 'My Orders')

@section('content')

    <div id="container">
        <div id="view-orders">
            <h1>My Orders</h1>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date-from">Date From</label>
                                <div class="col-md-8">
                                    <input type="text" id="date-from" name="date-from" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date-to">To</label>
                                <div class="col-md-8">
                                    <input type="text" id="date-to" name="date-to" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date</label>
                                <div class="col-md-8">
                                    <input type="text" id="date" name="date" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary" style="margin-top: 20px; margin-left: 20vh;">Search</button>
                        </div>
                    </div>
                    <div class="container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Order No</th>
                                    <th scope="col">Table No</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr data-toggle="modal" data-target="#pending-request">
                                    <th scope="col">1</th>
                                    <td scope="col">01-01-2001</td>
                                    <td scope="col">05:15 pm</td>
                                    <td scope="col">Syed Mohammad Yasir</td>
                                    <td scope="col">550</td>
                                    <td scope="col">OR_532_125_7532</td>
                                    <td scope="col">06</td>
                                    <td scope="col"><span style="color: red;">Pending</span></td>
                                </tr>
                                <tr data-toggle="modal" data-target="#accepted-request">
                                    <th scope="col">2</th>
                                    <td scope="col">01-01-2001</td>
                                    <td scope="col">05:15 pm</td>
                                    <td scope="col">Syed Mohammad Yasir</td>
                                    <td scope="col">550</td>
                                    <td scope="col">OR_532_125_7532</td>
                                    <td scope="col">06</td>
                                    <td scope="col">Accepted</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="modal fade" id="pending-request" tabindex="-1" role="dialog" aria-labelledby="request-label" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="request-label">Confirmation</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div id="date">
                                                    Date: Sun, May 28, 2018
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="time">
                                                    Time: 05:18pm
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="consumer">
                                                    Consumer: Syed Mohammad Yasir
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div id="table-no">
                                                    Table-no: 06
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 30px;">
                                            <div class="col-md-12">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">Items</th>
                                                            <th scope="col">Quantity</th>
                                                            <th scope="col">Price</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th scope="col">1</th>
                                                            <td scope="col">Chicken Spicy</td>
                                                            <td scope="col">2 pcs</td>
                                                            <td scope="col">200.00</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="col">2</th>
                                                            <td scope="col">Prawn Masala</td>
                                                            <td scope="col">2 pcs</td>
                                                            <td scope="col">500.00</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="col">3</th>
                                                            <td scope="col">Soft Drinks</td>
                                                            <td scope="col">2 pcs</td>
                                                            <td scope="col">30.00</td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="3" class="text-center">Total</th>
                                                            <th scope="col">730.00</th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Reject</button>
                                        <button type="button" class="btn btn-success">Accept</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="accepted-request" tabindex="-1" role="dialog" aria-labelledby="request-label" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="request-label">
                                        Barcode Cafe
                                        <br>
                                        <small>2 no gate circle, Chittagong</small>
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="date">
                                                Date: Sun, May 28, 2018
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div id="time">
                                                Time: 05:18pm
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="order-no">
                                                Order no: OR_532_125_7532
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div id="consumer">
                                                Consumer: Syed Mohammad Yasir
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div id="table-no">
                                                Table-no: 06
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div id="patment-type">
                                                Payment type: Cash
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 30px;">
                                        <div class="col-md-12">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Items</th>
                                                        <th scope="col">Unit Price</th>
                                                        <th scope="col">Quantity</th>
                                                        <th scope="col">Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th scope="col">1</th>
                                                        <td scope="col">Chicken Spicy</td>
                                                        <td scope="col">100.00</td>
                                                        <td scope="col">2 pcs</td>
                                                        <td scope="col">200.00</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col">2</th>
                                                        <td scope="col">Prawn Masala</td>
                                                        <td scope="col">250.00</td>
                                                        <td scope="col">2 pcs</td>
                                                        <td scope="col">500.00</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col">3</th>
                                                        <td scope="col">Soft Drinks</td>
                                                        <td scope="col">15.00</td>
                                                        <td scope="col">2 pcs</td>
                                                        <td scope="col">30.00</td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="4" class="text-center">Sub Total</th>
                                                        <th scope="col">730.00</th>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="text-center">Vat (15%)</td>
                                                        <td scope="col">109.50</td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="4" class="text-center">Total</th>
                                                        <th scope="col">839.50</th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                                    <button type="button" class="btn btn-primary">Print</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection