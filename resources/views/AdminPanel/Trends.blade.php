@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Trends')

@section('brand', 'Trends')

@section('content')
    <div id="container">
        <div id="view-dashboard">
            <div class="container">
                <div id="search-content">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><span class="fas fa-search"></span></div>
                        </div>
                        <input type="text" class="form-control form-control-lg" id="search" placeholder="Search">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $('li').removeClass('active');
        $('#menu-dashboard').parent().addClass('active');
    </script>
@endsection