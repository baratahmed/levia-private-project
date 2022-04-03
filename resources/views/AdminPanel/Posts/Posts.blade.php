@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Posts')

@section('brand', 'Post Management')

@section('content')
    <div class="container">
        @if (session('success'))
        <div class="alert alert-primary" role="alert">
           {{session('success')}}
        </div>
        @endif
        <div class="row">
            @foreach ($posts as $post)
            
                <div class="col-md-3">
                    <div class="card px-3 py-2">
                        <h5><a href="{{route('admin.posts.details',$post->id)}}">{{$post->user->fb_profile_name}}</a></h5>
                        <p>{{$post->created_at->format('H:i A, D, M d, Y')}}</p>
                        <h6 class="font-weight-normal">{{$post->post}}</h6>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center">
            {{$posts->links()}}
        </div>
    </div>      
@endsection

@section('extra-js')
    <script>
        // $('li').removeClass('active');
        // $('#menu-ratings').parent().addClass('active');
    </script>
@endsection