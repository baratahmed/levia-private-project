@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Settings')

@section('brand', 'Settings')

@section('content')
    <!-- Modal -->
    <div id="container">
        <div id="view-users">
            <div class="col-md-12">
                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif

                <form action="" method="POST">
                    {{ csrf_field() }}
                    <div class="row">
                        @php
                            $settings = \App\Models\Setting::all();
                            // dd($settings);
                        @endphp

                        @foreach ($settings as $setting)
                            <div class="col-md-12">
                                <div class="form-group">
                                  <label for="{{ $setting->key }}">{{ ucfirst(str_replace("_", " ", $setting->key)) }}</label>
                                  <input type="text"
                                    class="form-control" name="{{ $setting->key }}" id="{{ $setting->key }}" aria-describedby="help_{{ $setting->key}}" value="{{ $setting->value }}" placeholder="Set {{ $setting->key }}">
                                  <small id="help_{{ $setting->key}}" class="form-text text-muted">In {{ strtoupper($setting->value_type) }}</small>
                                </div>
                            </div>
                        @endforeach


                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary" style="margin-top: 35px;">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $(document).ready(function(){
            $('li').removeClass('active');
            $('#menu-settings').parent().addClass('active');

            // Delete User
            $(document).on('click', '.delete-user-button', function(e){
                e.preventDefault();
                console.log("clicked");
                // console.log(e.target.getAttribute('data-userid'));
                $('#user_id').val(e.target.getAttribute('data-userid'));
            })
        });
    </script>
@endsection