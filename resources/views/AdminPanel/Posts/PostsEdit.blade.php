@extends('Model.AdminPanelModel')

@section('title', 'Admin Panel | Post Edit')

@section('brand', 'Post Edit')

@section('content')
    <div class="container">
        <div class="row pl-5">
            <form action="{{route('admin.posts.update',$post->id)}}" method="post">
                @csrf
                <div class="form-group">
                  <label for="post_text">Post Text</label>
                  <textarea type="text" class="form-control" name="post" cols="100" rows="4" id="post_text" placeholder="Post Text Here">{{$post->post}}</textarea>
                </div>
                <div class="form-group">
                  <label for="created_at">Created At</label>
                  @php
                    $datetime = $post->created_at;
                    $d =  explode(' ', $datetime);
                    $created_at_date = $d[0] ."T". $d[1];
                  @endphp
                  <input type="datetime-local" name="created_at" value="{{$created_at_date}}" class="form-control" id="created_at" placeholder="Created At Date">
                </div>
    
                <button type="submit" class="btn btn-primary btn-block">Update</button>
              </form>
        </div>
    </div>      
@endsection

@section('extra-js')
    <script>
        // $('li').removeClass('active');
        // $('#menu-ratings').parent().addClass('active');
    </script>
@endsection