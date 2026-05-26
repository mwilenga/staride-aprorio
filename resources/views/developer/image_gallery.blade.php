@extends('developer.layouts.main')
@section("content")
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Image Gallery</h1>
        </div>

        <!-- Content Row -->
        <div class="row">
            <div class="col-xl-12 col-md-12 mb-12">
                <div class="row">
                    <form action="{{route("developer.image-gallery-test-submit")}}" enctype="multipart/form-data"
                          method="POST">
                        @csrf
                        <div class="col-xl-12 col-md-12 mb-12">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-header">
                                    <h5>Upload Image Prototype</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-4 col-md-4 mb-4">
                                            <label>Upload</label><br>
                                            <input type="file" class="form-control" id="image" name="image" src=""/>
                                        </div>
                                        OR
                                        <div class="col-xl-4 col-md-4 mb-4">
                                            <label>Gallery</label><br>
                                            <input type="text" class="form-control" id="gallery_image"
                                                   name="gallery_image" style="display: none;"/>
                                            <button type="button" class="form-control" id="gallery_cancel"
                                                    name="gallery_cancel" style="display: none;">X
                                            </button>
                                            <button type="button" class="btn btn-primary mt-3" id="gallery_choose"
                                                    data-bs-toggle="modal" data-bs-target="#imageModal">Choose
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 mb-12">
                                            <button type="submit" class="btn btn-primary mt-3">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form action="{{route("developer.image-gallery-submit")}}" enctype="multipart/form-data"
                          method="POST">
                        @csrf
                        <div class="col-xl-12 col-md-12 mb-12">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-header">
                                    <h5>Upload Gallery Images</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-4 col-md-4 mb-4">
                                            <label>Images</label><br>
                                            <input type="file" class="form-control" id="image[]" name="image[]"
                                                   multiple/>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 mb-12">
                                            <button type="submit" class="btn btn-primary mt-3">Upload</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form>
                        <div class="col-xl-12 col-md-12 mb-12">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-header">
                                    <h5>Uploaded Images</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 mb-12">
                                            <table class="table border">
                                                <thead>
                                                <tr>
                                                    <th>Sr.</th>
                                                    <th>Path</th>
                                                    <th>Image</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php $i=1; @endphp
                                                @foreach($gallery_images as $image)
                                                    <tr>
                                                        <td>{{$i++}}</td>
                                                        <td>{{$image}}</td>
                                                        <td><img src="{{ view_config_image($image) }}" class="img-fluid" height="80px" width="80px" alt="{{$image}}" /></td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Select Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="imageGallery">
                        <!-- Images will be dynamically added here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="chooseImageBtn">Choose Image</button>
                </div>
            </div>
        </div>
    </div>

    <!-- /.container-fluid -->
@endsection
@section("styles")
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styling for selected image */
        .selected {
            border: 2px solid blue;
        }

        .gallery_image {
            width: 50px;
            height: 50px;
        }
    </style>
@endsection
@section("js")
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Images data (replace with your image URLs)
        const images = JSON.parse('{!! $images_arr !!}');

        // Function to load images into gallery
        function loadImages() {
            const gallery = document.getElementById('imageGallery');
            gallery.innerHTML = '';
            images.forEach((image, index) => {
                const imgElement = document.createElement('img');
                imgElement.src = image;
                imgElement.classList.add('img-thumbnail', 'm-2', 'cursor-pointer', 'gallery_image');
                imgElement.setAttribute('data-index', index);
                imgElement.addEventListener('click', selectImage);
                gallery.appendChild(imgElement);
            });
        }

        // Function to handle image selection
        function selectImage(event) {
            const selectedImage = document.querySelector('.selected');
            if (selectedImage) {
                selectedImage.classList.remove('selected');
            }
            event.target.classList.add('selected');
        }

        // Function to handle choose image button click
        document.getElementById('chooseImageBtn').addEventListener('click', () => {
            const selectedImage = document.querySelector('.selected');
            if (selectedImage) {
                document.getElementById('gallery_image').value = selectedImage.src;
                // Close the modal
                const modal = document.getElementById('imageModal');
                const modalInstance = bootstrap.Modal.getInstance(modal);
                modalInstance.hide();

                document.getElementById('gallery_image').style.display = 'block';
                document.getElementById('gallery_cancel').style.display = 'block';
                document.getElementById('gallery_choose').style.display = 'none';
            } else {
                alert('Please select an image.');
            }
        });

        document.getElementById('gallery_cancel').addEventListener('click', () => {
            document.getElementById('gallery_image').style.display = 'none';
            document.getElementById('gallery_cancel').style.display = 'none';
            document.getElementById('gallery_choose').style.display = 'block';
            document.getElementById('gallery_image').value = null;
        });

        // Initial loading of images
        window.addEventListener('load', loadImages);
    </script>
@endsection
