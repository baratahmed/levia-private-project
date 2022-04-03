<nav id="nav" class="navbar {{ isset($home) ? 'nav-transparent' : '' }}">
    <div class="container">

        <div class="navbar-header">
            <!-- Logo -->
            <div class="navbar-brand">
                <a href="index.html">
                    <img class="logo" src="{{asset('theme_landing/img/logo.png')}}" alt="logo">
                    <img class="logo-alt" src="{{asset('theme_landing/img/logo-alt.png')}}" alt="logo">
                </a>
            </div>
            <!-- /Logo -->

            <!-- Collapse nav button -->
            <div class="nav-collapse">
                <span></span>
            </div>
            <!-- /Collapse nav button -->
        </div>

        <!--  Main navigation  -->
        <ul class="main-nav nav navbar-nav navbar-right">
            <li><a href="{{ !isset($home) ? route('home').'/' : '' }}#home">Home</a></li>
            @if(auth('radmin')->check())
                <li class="font-bolder"><a href="{{ route('radmin.dashboard') }}">Dashboard</a></li>
            @endif
            {{--  <li><a href="#about">About</a></li>  --}}
            {{--  <li><a href="#service">Services</a></li>  --}}
            <li><a href="{{ !isset($home) ? route('home').'/' : '' }}#pricing">Pricing</a></li>
            {{--  <li class="has-dropdown"><a href="#blog">Blog</a>
                <ul class="dropdown">
                    <li><a href="blog-single.html">blog post</a></li>
                </ul>
            </li>  --}}
            <li><a href="{{ !isset($home) ? route('home').'/' : '' }}#contact">Help</a></li>
            @if(!auth('radmin')->check())
                <li><a href="{{ route('getLogin') }}">Login</a></li>
                <li class="font-bolder"><a href="{{ route('getRegister') }}" class="btn-primary">Start</a></li>
            @else
                <li class="font-bolder"><a href="{{ route('logout') }}" class="btn-primary">Logout</a></li>
            @endif
        </ul>
        <!-- /Main navigation -->

    </div>
</nav>
<!-- /Nav -->
