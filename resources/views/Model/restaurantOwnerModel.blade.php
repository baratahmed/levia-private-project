<!DOCTYPE html>
<html>
<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- CSS Files -->
	{{-- <link rel="stylesheet" href="{{asset('content/vendor/bootstrap/css/bootstrap.min.css')}}"> --}}
	{{-- <link rel="stylesheet" type="text/css" href="{{asset('content/css/style.css')}}"> --}}
	{{-- Style will be compiled and loaded from SCSS --}}
	<link rel="stylesheet" href="{{ asset('css/app.css') }}">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
	@yield('extra-css')

	{{--  <link rel="stylesheet" type="text/css" href="{{asset('content/css/style.css')}}">  --}}
	{{--  Zisad's commit, perhaps we don't need it anymore, since everything is merged into app.scss  --}}

	<!-- Title -->
	<title>@yield('title')</title>
</head>
<body>

	<div class="wrapper">

		<!-- Sidebar -->
		<nav id="sidebar">
		    <div class="sidebar-header">
		        <h3><a href="{{ route('home') }}">Levia</a></h3>
		    </div>
		    <ul class="list-unstyled components">
				<li>
		            <a class="sidebar-menu" id="menu-trends" href="{{asset('trends')}}">Trends</a>
		        </li>
		        {{-- <li>
		            <a class="sidebar-menu" id="menu-dashboard" href="{{asset('dashboard')}}">Dashboard</a>
		        </li> --}}
		        {{-- <li>
		            <a class="sidebar-menu" id="menu-orders" href="{{asset('myorders')}}">My Orders</a>
		        </li>
		        <li>
					<a class="sidebar-menu" id="menu-payments" href="{{asset('payments')}}">Payments</a>
		        </li> --}}
				<li>
					<a class="sidebar-menu" id="menu-promotions" href="{{asset('promotion')}}">Promotion</a>
				</li>
		        <li>
		            <a class="sidebar-menu" id="menu-settings" href="{{asset('settings')}}">Settings</a>
		        </li>
		        </li>
		        <li>
		            <a class="sidebar-menu" id="menu-offers" href="{{asset('offer')}}">Offers</a>
		        </li>
		        <li>
		            <a class="sidebar-menu" id="menu-rating" href="{{asset('ratings')}}">Rating and reviews</a>
		        </li>
		        <li>
		            <a class="sidebar-menu" id="menu-gallery" href="{{asset('gallery')}}">Gallery</a>
		        </li>
		        <li>
		            <a class="sidebar-menu" id="menu-reservations" href="{{asset('reservations')}}">Reservations</a>
		        </li>
		        <li>
		            <a class="sidebar-menu" id="menu-posts" href="{{asset('posts')}}">Posts</a>
		        </li>
		    </ul>
		</nav>

	    <div id="content">

			<!-- Navbar -->
			<nav class="navbar navbar-expand-lg navbar-light sticky-top" style="background-color: #fff;">
			    <button class="btn btn-info" type="button" id="sidebarCollapse">
			        <span class="navbar-toggler-icon"></span>
			    </button>

			    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
			        <span class="navbar-toggler-icon"></span>
			    </button>

			    <div class="collapse navbar-collapse" id="navbarContent">
			        <ul class="navbar-nav ml-auto">
			            <li class="nav-item">
			                <a class="nav-link" href="https://www.facebook.com/levia.bd" target="_blank">Visit Page</a>
			            </li>
			            {{--  <li class="nav-item">
			                <a class="nav-link menu" href="{{asset('notification')}}" id="notification">
			                    <div id="notification-icon">
			                        <i class="fas fa-bell"></i>
			                    </div>
			                </a>
			            </li>  --}}
			            <li class="nav-item">
			                <a class="nav-link" href="#">
			                    <img src="{{asset('content/img/man.ico')}}" class="avatar-img" style="height: 30px;">
			                </a>
			            </li>
			            <li class="nav-item">
			                <a class="nav-link" href="{{ route('logout') }}">
			                    Logout
			                </a>
			            </li>
			        </ul>
			    </div>
			</nav>

			<main>
				<!-- Main Content -->
				@yield('content')

			</main>
	    </div>
	</div>

	<!-- Javascripts Files -->
	{{-- Bootstrap, jQuery, Lodash, Axios, Vue, Popper -> These libraries will be pre-compiled and included with public/js/app.js file  --}}
	{{-- <script src="{{asset('content/vendor/bootstrap/js/jquery.min.js')}}"></script> --}}
	{{-- <script src="{{asset('content/vendor/bootstrap/js/bootstrap.min.js')}}"></script> --}}
	@if(!isset($VuePage))
		<script src="{{ asset('js/app.js') }}"></script>
	@else
		@yield('vue-js')
	@endif
	<script defer src="https://use.fontawesome.com/releases/v5.2.0/js/all.js" integrity="sha384-4oV5EgaV02iISL2ban6c/RmotsABqE4yZxZLcYMAdG7FAPsyHYAPpywE9PJo+Khy" crossorigin="anonymous"></script>
	<script src="{{asset('content/vendor/bootstrap/js/chart.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('content/js/script.js')}}"></script>
	@yield('extra-js')

	
</body>
</html>
