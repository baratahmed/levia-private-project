@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Post Details')

@section('brand', 'Post Details')

@section('content')
    <div class="container">
        <div class="row">
           <div class="col-md-6">
                <img src="{{asset('img/default_post.png')}}" alt="">
           </div>
           <div class="col-md-6">
                <div><span>ID: </span> {{$post->id}}</div>
                 <div>
                    {{$post->post}}
                 </div>
                <div>
                    <span>User: </span> {{$post->user->fb_profile_name}}
                </div>
                <div>
                    <span>Timestamp: </span> {{$post->created_at->format('H:i A, D, M d, Y')}}
                </div>
                <div>
                    <span>Likes: </span> {{$post->likes_count}}
                </div>
                <div>
                    <span>Comments: </span> {{$post->comments_count}}
                </div>
                <div>
                    <span>Share: </span> {{$post->shares_count}}
                </div>
                <div class="pt-2">
                    <a href="{{route('admin.posts.edit',$post->id)}}" class="btn btn-primary btn-block mr-3">EDIT</a>
                    <a href="{{route('admin.posts.delete',$post->id)}}" class="btn btn-danger btn-block" onclick="return confirm('Are you sure to delete?');">DELETE</a>
                </div>

           </div>
        </div>

    </div>      
@endsection

@section('extra-js')
    <script>
        // $('li').removeClass('active');
        // $('#menu-ratings').parent().addClass('active');
    </script>
@endsection