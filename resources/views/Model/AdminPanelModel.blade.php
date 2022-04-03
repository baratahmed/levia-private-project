<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    @yield('extra-css')
    <title>@yield('title')</title>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>Levia</h3>
            </div>
            <ul class="list-unstyled components">
                <li>
                    <a class="sidebar-menu" id="menu-dashboard" href="{{route('admin.trends')}}">Trends</a>
                </li>
                <li>
                    <a class="sidebar-menu" id="menu-promotions" href="{{route('admin.promotions')}}">Promotions</a>
                </li>
                <li>
                    <a class="sidebar-menu" id="menu-payments" href="{{route('admin.payments')}}">Payments</a>
                </li>
                <li>
                    <a class="sidebar-menu" id="menu-users" href="{{route('admin.users')}}">Users</a>
                </li>
                <li>
                    <a class="sidebar-menu" id="menu-business" href="{{route('admin.business')}}">Business</a>
                </li>
                <li>
                    <a class="sidebar-menu" id="menu-orders" href="{{route('admin.orders')}}">Orders</a>
                </li>
                <li>
                    <a class="sidebar-menu" id="menu-offers" href="{{route('admin.offers')}}">Offers</a>
                </li>
                <li class="active">
                    <a class="sidebar-menu" id="menu-ratings" href="{{route('admin.ratings')}}">Rating and Reviews</a>
                </li>
                <li>
                    <a class="sidebar-menu" id="menu-settings" href="{{route('admin.settings')}}">Settings</a>
                </li>
                <li>
                    <a class="sidebar-menu" id="menu-reservations" href="{{route('admin.reservations')}}">Reservations</a>
                </li>
                <li>
                    <a class="sidebar-menu" id="menu-contact" href="{{route('admin.contacts')}}">Contact Requests</a>
                </li>
                <li>
                    <a class="sidebar-menu" id="menu-post" href="{{route('admin.posts')}}">All Posts</a>
                </li>
            </ul>
        </nav>
        <div id="content">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light sticky-top" style="background-color: #fff;">
                <button class="btn btn-info" type="button" id="sidebarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
            
                <h3>@yield('brand')</h3>
            
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            
                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <img src="{{ asset('img/man.ico') }}" class="avatar-img" style="height: 30px;">
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('adminLogout') }}">
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>

                
            </nav>
            <main>
                @yield('content')
            </main>
        </div>
    </div>
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