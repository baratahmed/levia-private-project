@extends('Model/restaurantOwnerModel')

@section('title', 'Posts')

@section('content')

    <div id="container">
        
        <div class="view">
            <div class="d-flex">
                <div class="mr-auto"><h2>Posts</h2></div>
            </div>
            
            <div class="create-post">
              <form action="#" id="CreatePost">
                <div class="post-form pt-2">
                  <div class="post-body">
                    <div class="content-container p-2">
                      <textarea class="form-control post-content" cols="1" rows="4" placeholder="Make a Post" style="resize: none; overflow-y: hidden; position: absolute; top: 0px; left: -9999px; height: 97.2px; width: 780px; line-height: 21px; text-decoration: none solid rgb(73, 80, 87); letter-spacing: 0px;" tabindex="-1"></textarea><textarea class="form-control post-content" name="post_content" id="post_content" cols="1" rows="4" placeholder="Make a Post" style="resize: none; overflow-y: hidden;"></textarea>
                    </div>
                  </div>
                  <div class="attached-images">
                    <div class="images-container">
                      <div class="single-image">
                        <div class="the-image"></div>
                        <div class="image-overlay"></div>
                        <div class="cancel-image" id="CancelUploadButton"><i class="fa fa-times"></i></div>
                      </div>
                    </div>
                  </div>
                  <div class="submit-options">
                    <a class="btn btn-default btn-sm btn-upload" id="AddImageButton" href="#" title="Click here to Post an Image"><i class="fa fa-image"></i></a>
                    <input type="submit" value="Create Post" href="#" class="btn btn-default btn-sm btn-submit" />
                  </div>
                </div>
                <input type="file"  name="attached_images[]" id="attached_images">
              </form>

              <div class="uploading-overlay">
                <i class="fas fa-spinner fa-3x fa-spin " style="color:white"></i>
              </div>
            </div>


            <div class="old-posts">
              @foreach ($posts as $post)
              <div class="single-post bg-white">
                <div class="post-header">
                  <div class="post-information flex-grow-1">
                    <div class="name-and-badges">
                      <div class="name-box">
                        <span>Posted by </span>
                        <a href="#">
                          {{ auth()->user()->restaurant->rest_name }}
                        </a>
                        <span class="time">
                          <a href="#">{{ $post->created_at->diffForHumans() }}</a>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                @if ($post->post)
                  <div class="post-body">
                    {{ $post->post }}
                  </div>
                @endif
                @if ($post->post_image)
                  @foreach (json_decode($post->post_image) as $image)
                      <div class="post-images">
                        <div class="single-image">
                          <img src="{{ $image }}" alt="Post Image">
                          <div class="overlay"></div>
                        </div>
                      </div>
                  @endforeach
                @endif
                
                <div class="post-options d-flex bordered-bottom bordered-top">
                  <button href="#" class="btn btn-default btn-option">
                    <i class="far fa-heart"></i>
                    {{ $post->likes_count }}
                  </button>
                  <button href="#" class="btn btn-default btn-option">
                    <i class="far fa-comment-alt"></i>
                    {{ $post->comments_count }}
                  </button>
                </div>
              </div>
              @endforeach
            </div>
            
        </div>
    </div>

@endsection

@section('extra-js')
    <!-- Inline JS -->
    <link rel="stylesheet" href="{{ asset('css/posts.css') }}">
    <script src="{{ asset('js/textarea-autoresizer.js') }}"></script>
    <script>
        $(function(){
            $('li').removeClass('active');
            $('#menu-posts').parent().addClass('active');
            $('#post_content').autoResize();

            $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
          
            // Programmmatically click the hidden file input by clicking the link
            $('#AddImageButton').click(function(){
              $('#attached_images').click();
            });
          
            // Displays image directly from user's filesystem before uploading that to server.
            function DisplayImageBeforeUploading(input) {
              var acceptedTypes = ["image/png", "image/jpeg", "image/gif", "image/webp"]
              if (input.files && input.files[0]) {
                var reader = new FileReader();
          
                // console.log(input.files[0].type);
                if (acceptedTypes.indexOf(input.files[0].type) < 0){
                  alert("The file must be an image file (jpeg/png/gif/webp).");
                  return;
                }
          
                reader.onload = function (e) {
                  // We use background image rather than "img" to cover the area
                  $('.single-image .the-image').css({
                    'background-image' : `url(${e.target.result})`
                  });
                  // $('#post_content').hide();
                }
          
                reader.readAsDataURL(input.files[0]);
                
                // console.log('adding-class');
                $('.attached-images').addClass('has-images');
              }
            }
          
            // Clear the form when user removes image from post
            function CancelImageSelection(){
              $('.attached-images').removeClass('has-images'); // Hide the Image Preview
              $('.single-image .the-image').css({ // Image Preview Source to Empty
                'background-image' : `none`
              });
              $('#attached_images').val('') // Clear file input
              // $('#post_content').show();
            }
            
            // Display the image on selecting from filesystem
            $('#attached_images').change(function(){
              DisplayImageBeforeUploading(this);
            });
          
            // Cancel the image
            $('#CancelUploadButton').on('click', function(){
              CancelImageSelection();
            });


            $('#CreatePost').on('submit', function(e){
              e.preventDefault();
              $formData = new FormData($(this)[0]);
              //$formData.append('attached_images', $('#attached_images')[0].files[0]);

              //var formData = new FormData();
              $('.uploading-overlay')[0].style.display = 'flex';

              $.ajax({
                url : '{{ route("radmin.posts.store") }}',
                type : 'POST',
                data : $formData,
                processData: false,  // tell jQuery not to process the data
                contentType: false,  // tell jQuery not to set contentType
                success : function(data) {
                    console.log(data);
                    $('.uploading-overlay')[0].style.display = 'none';
                    document.location.reload();
                },
                error: function(data){
                  alert(JSON.stringify(data.responseJSON.errors.post_content));
                  $('.uploading-overlay')[0].style.display = 'none';
                }
              });
            })
          
        });
    </script>
@endsection
