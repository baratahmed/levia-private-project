<!DOCTYPE html>
<html>
<head>
    <!-- CSS Files -->
    <link rel="stylesheet" href="{{asset('content/vendor/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{asset('content/css/style.css')}}">

@yield('css')

    <!-- Title -->
    <title>@yield('title')</title>
</head>
<body>

<div class="wrapper">
    <div id="content">
        <main>
            @yield('content')
        </main>
    </div>
</div>


<script src="{{asset('content/vendor/bootstrap/js/jquery.min.js')}}"></script>
<script defer src="https://use.fontawesome.com/releases/v5.2.0/js/all.js" integrity="sha384-4oV5EgaV02iISL2ban6c/RmotsABqE4yZxZLcYMAdG7FAPsyHYAPpywE9PJo+Khy" crossorigin="anonymous"></script>
<script src="{{asset('content/vendor/bootstrap/js/bootstrap.min.js')}}"></script>

</body>
</html>