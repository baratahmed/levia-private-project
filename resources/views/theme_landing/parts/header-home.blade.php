<!-- Header -->
<header id="home">
    <!-- Background Image -->
    <div class="bg-img" style="background-image: url('{{asset("theme_landing/img/background4.jpg")}}');">
        <div class="overlay"></div>
    </div>
    <!-- /Background Image -->

    <!-- Nav -->
    @include('theme_landing.parts.header')

    <!-- home wrapper -->
    <div class="home-wrapper">
        <div class="container">
            <div class="row">

                <!-- home content -->
                <div class="col-md-10 col-md-offset-1">
                    <div class="home-content">
                        {{-- <h1 class="white-text">Bring your business to online</h1> --}}
                        <h1 class="white-text">Code is Updated</h1>
                        <p class="white-text lead">You've made your decision, we've arranged the platform</p>
                        @if(!auth('radmin')->check())
                            <a href="{{ route('getRegister') }}"><button class="btn-primary">Start your journey</button></a>
                        @else
                            <a href="{{ route('radmin.dashboard') }}"><button class="btn-primary">Go to Dashboard</button></a>
                        @endif
                    </div>
                </div>
                <!-- /home content -->

            </div>
        </div>
    </div>
    <!-- /home wrapper -->

</header>
<!-- /Header -->
