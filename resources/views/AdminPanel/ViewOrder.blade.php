@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Orders > View')

@section('brand', 'Orders')

@section('content')
    <div id="container">
        <div class="text-center" id="view_orders">
            <h2>Barcode Cafe</h2>
            <p>2 no gate circle, Chittagong</p>
        </div>
        <div style="margin-top: 60px; font-size: 14px;" class="col-md-4 offset-md-4">
            <div>
                <p>Date: <strong class="text-dark">Sun, May 28, 2019</strong>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                Time:<strong class="text-dark">05:18 PM</strong></p>
                <p>Order NO: <strong class="text-dark">Lorem</strong></p>
                <p>Consumer: <strong class="text-dark">Sun, May 28, 2019</strong></p>
                <p>Table NO: <strong class="text-dark">1</strong></p>
                <p>Payment Type: <strong class="text-dark">Cash</strong></p>
                <p>Status: <strong class="text-dark">Accepted</strong></p>
            </div>
            <div style="margin-top: 20px;" class="text-center">
                <table class="table table-striped">
                    <thead style="text-align: center;">
                        <tr>
                            <th scope="col">NO.</th>
                            <th scope="col">Items</th>
                            <th scope="col">Unit Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="col">1</th>
                            <td>Chicken</td>
                            <td>100.00</td>
                            <td>2 pcs</td>
                            <td>200</td>
                        </tr>
                        <tr>
                            <th scope="col">2</th>
                            <td>Chicken</td>
                            <td>100.00</td>
                            <td>2 pcs</td>
                            <td>200</td>
                        </tr>
                        <tr>
                            <th scope="col">3</th>
                            <td>Chicken</td>
                            <td>100.00</td>
                            <td>2 pcs</td>
                            <td>200</td>
                        </tr>
                        <tr>
                            <th scope="col" colspan="4">Sub Total</th>
                            <td>730.00</td>
                        </tr>
                        <tr>
                            <th scope="col" colspan="4">Vat (15%)</th>
                            <td>109.50</td>
                        </tr>
                        <tr>
                            <th scope="col" colspan="4">Total</th>
                            <td>839.50</td>
                        </tr>
                    </tbody>
                </table>
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