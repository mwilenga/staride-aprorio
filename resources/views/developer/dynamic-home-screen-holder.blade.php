@extends('developer.layouts.main')

@section("content")
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">@if(!empty($id)) Update @else Create @endif Dynamic Holders</h1>
            <div class="py-2">
                @include('developer.shared.message')
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">

            <div class="col-xl-12 col-md-12 mb-12">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row ml-lg-5">
                            <div class="col-lg-5">
                                <form action="@if(!empty($id)) {{route('dynamic.save.home-screen-holder', ['id'=>$id])}} @else {{route('dynamic.save.home-screen-holder')}} @endif" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group col-md-12">
                                        <label for="user_id">Holder Name</label>
                                        <input type="text" name="holder_name" class="form-control" id="holder_name" @if(!empty($id)) value="{{$data['name']}}" @endif>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="notification_content">Holder Image</label>
                                        <input type="file" name="holder_image" class="form-control" id="holder_image">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="notification_content">Select Holder Segments</label><br>
                                        <select class="form-control" name="segments[]" multiple="multiple" id="segments">
                                            @foreach($merchant_segment as $segment)
                                                <option value="{{$segment->id}}">{{$segment->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary ">@if(!empty($id)) Update @else Save @endif</button>
                                </form>
                            </div>
                            <div class="col-lg-1">

                            </div>
                            <div class="col-lg-6">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Segments</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $sno = 1; @endphp
                                    @foreach($dynamic_holders as $holder)
                                        <tr>
                                            <td>{{$sno}}</td>
                                            <td>{{$holder->holder_name}}</td>
                                            <td><img src="{{get_image($holder->holder_image, "merchant", $holder->merchant_id)}}" width="70" height="50"alt="holder_icon"></td>
                                            <td>{{$holder->segment_names}}</td>
                                            <td><a href="{{route('dynamic.home-screen-holder', ['id'=>$holder->id])}}"><button class="btn btn-primary">Edit</button></a></td>
                                        </tr>
                                        @php $sno++; @endphp
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
@section("js")
    <script>
        $(document).ready(function() {
            $('#segments').select2();
        });
        @if(!empty($id))
            $('#segments').val({{ json_encode($data['selected_segments']) }}).trigger('change');

        @endif
    </script>
@endsection
