@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Payments')

@section('brand', 'Payments')

@section('content')
    <div id="container">
        <div id="view-payments">
            
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $('li').removeClass('active');
        $('#menu-payments').parent().addClass('active');
    </script>
@endsection