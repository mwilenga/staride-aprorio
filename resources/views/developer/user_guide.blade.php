@extends('developer.layouts.main')
@section("content")
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">User Guide Files</h1>
        </div>

        <!-- Content Row -->
        <div class="row">

            <div class="col-xl-12 col-md-12 mb-12">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <form style="width: 100%;" method="POST" name="user-guide" id="user-guide" enctype="multipart/form-data" action="{{ route('developer.user-guide.submit') }}">
                                @csrf
                                @foreach($slugs as $key => $slug)
                                    <div class="form-group">
                                        <label for="file_{{$key}}">{{$slug}} File</label> : @if(isset($user_guides[$slug]) && !empty($user_guides[$slug])) <span style="color: green">Uploaded</span> <a href="{{get_image($user_guides[$slug],"user_guide",NULL, false)}}" target="_blank">View</a> @else <span style="color: red">Not Uploaded</span> @endif
                                        <input type="hidden" name="file[{{$key}}][slug]" value="{{$slug}}" />
                                        <input type="file" class="form-control" id="file_{{$key}}" name="file[{{$key}}][file]" />
                                    </div>
                                @endforeach
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
