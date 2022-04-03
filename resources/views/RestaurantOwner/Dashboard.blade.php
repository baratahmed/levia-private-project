@extends('Model/restaurantOwnerModel')

@section('title', 'Dashboard')

@section('content')

	<div id="container">
		<div id="view-dashboard">
			<h1>Dashboard</h1>
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
							<img src="{{ $rest->imageUrl }}" style="width:100%;height:auto;">
						</div>
						<div class="col-md-9">
							<h3 class="card-title">{{ $rest->rest_name }}</h3>
							<p class="card-text">{{ $rest->rest_street }}, {{ $rest->district }}</p>
							<a href="#" class="btn btn-outline-success active">Open now</a>
							<a href="#" class="btn btn-outline-danger btn-rounded">Closed</a>
						</div>
					</div>
				</div>
			</div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <h3>Total Orders</h3>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="float-right">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-default">Today</button>
                                                <button type="button" class="btn btn-default">Monthly</button>
                                                <button type="button" class="btn btn-default">Yearly</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <i class="fas fa-shopping-cart" style="font-size: 5rem;"></i>
                                            <div class="float-right">
                                                <h3 style="margin-top: 20px;">1255</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <h3>Total Customers</h3>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="float-right">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-default">Today</button>
                                                <button type="button" class="btn btn-default">Monthly</button>
                                                <button type="button" class="btn btn-default">Yearly</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <i class="fas fa-walking" style="font-size: 5rem;"></i>
                                            <div class="float-right">
                                                <h3 style="margin-top: 20px;">2000</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <h3>Total Revenues</h3>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="float-right">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-default">Today</button>
                                                <button type="button" class="btn btn-default">Monthly</button>
                                                <button type="button" class="btn btn-default">Yearly</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <i class="fas fa-dollar-sign" style="font-size: 5rem;"></i>
                                            <div class="float-right">
                                                <h3 style="margin-top: 20px;">10000</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <h3>Total Reviews</h3>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="float-right">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-default">Today</button>
                                                <button type="button" class="btn btn-default">Monthly</button>
                                                <button type="button" class="btn btn-default">Yearly</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <i class="fas fa-pen" style="font-size: 5rem;"></i>
                                            <div class="float-right">
                                                <h3 style="margin-top: 20px;">120</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <canvas id="line-chart" style="width: 100%; height: 500px;"></canvas>
                    </div>
                    <div class="col-lg-6">
                        <canvas id="pie-chart" style="width: 100%; height: 500px;"></canvas>
                    </div>
                </div>
            </div>
		</div>
	</div>

@endsection


@section('extra-js')
    @parent
	<script type="text/javascript" src="{{asset('content/js/charts.js')}}"></script>    
@endsection