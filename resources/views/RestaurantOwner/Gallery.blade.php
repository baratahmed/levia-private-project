@extends('Model/restaurantOwnerModel')

@section('title', 'Gallery')

@section('content')

    <div id="container">
        <div class="gallery">
            <div class="header">
                <h3>Gallery</h3>
            </div>
            <div class="body">
                <div class="flex-container">
                    @if($rest->rest_image_url != null && $rest->rest_image_url != "default.jpg")
                        
                            <div class="gallery-image">
                                <img src="{{ $rest->imageUrl }}" alt="{{ $rest->rest_name }}">
                                <p>{{ $rest->rest_image_url }}</p>
                            </div>
                        
                    @endif
                    @foreach ($foods as $food)
                        @if($food->food_image_url != null && $food->food_image_url != "default.jpg")
                            
                                <div class="gallery-image">
                                    <img src="{{ $food->foodImage }}" alt="{{ $food->food_name }}">
                                    <p>{{ $food->food_image_url }}</p>
                                </div>
                            
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection

@section('extra-js')
    <!-- Inline JS -->
    <script>
        $('li').removeClass('active');
        $('#menu-gallery').parent().addClass('active');
    </script>
@endsection

@section('extra-css')
    <style>
        .flex-container {
            display:flex;
            flex-direction:row;
            flex-wrap:wrap;
        }
        .gallery .gallery-image {
            height:300px;
            
            margin-right:10px;
            margin-bottom:10px;
            text-align:center;
        }
        .gallery .gallery-image img {
            height:250px;
        }
        .gallery .gallery-image p {
            text-align:center;
            margin-top:10px;
            color:black;
        }
    </style>
@endsection