@extends('developer.layouts.main')
@section("content")
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">HomeScreen Holder Config</h1>
        </div>

    @include('developer.shared.message')

    <!-- Content Row -->
        <div class="row">

            <div class="col-xl-12 col-md-12 mb-12">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <form name="home_screen_holder_config" id="home_screen_holder_config" method="post" action="{{route("developer.homescreen.config.submit")}}">
                            @csrf
                            <div class="form-group">
                                <label for="add_row"></label>
                                <button type="button" class="btn btn-success float-right" id="add_row">Add Row</button>
                                <a href="{{route('developer.homescreen.config.reset')}}" class="btn btn-primary float-right" style="margin-right: 1%;">Reset Default</a>
                            </div>
                            @if(!empty($merchant_holders->toArray()))
                                <input type="hidden" id="total_holders" value="{{count($home_screen_holders)+1}}">
                                <input type="hidden" id="total_count" value="{{count($merchant_holders)}}">
                                <div id="holder_div">
                                    @foreach($merchant_holders as $key => $merchant_holder)
                                        <div class="row" @if($key > 0) id="row_{{$key}}" @endif>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="home_screen_holder">HomeScreen Holder</label>
                                                    <select class="form-control" name="home_screen_holder[]" id="home_screen_holder">
                                                        <option value="">--Select One--</option>
                                                        @foreach($home_screen_holders as $holder)
                                                            <option value="{{$holder->id}}" @if($merchant_holder->pivot->home_screen_holder_id == $holder->id) selected @endif>{{$holder->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="holder_position">Holder Position</label>
                                                    {{ Form::text("holder_position[]", old('holder_position',$merchant_holder->pivot->sequence), array("class" => "form-control", "id" => "holder_position")) }}
                                                </div>
                                            </div>
                                            @if($key > 0)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <button type="button" style="margin-top: 14%;" class="btn btn-danger float-right remove_button">Remove</button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <input type="hidden" id="total_holders" value="{{count($home_screen_holders)}}">
                                <input type="hidden" id="total_count" value="0">
                                <div id="holder_div">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="home_screen_holder">HomeScreen Holder</label>
                                                <select class="form-control" name="home_screen_holder[]" id="home_screen_holder">
                                                    <option value="">--Select One--</option>
                                                    @foreach($home_screen_holders as $holder)
                                                        <option value="{{$holder->id}}">{{$holder->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="holder_position">Holder Position</label>
                                                {{ Form::text("holder_position[]", old('holder_position'), array("class" => "form-control", "id" => "holder_position")) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <button type="submit" class="btn btn-primary displayTag">Submit</button>
                        </form>

                        <div id="holder_row" style="display: none">
                            <div class="row" id="row_1">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="home_screen_holder">HomeScreen Holder</label>
                                        <select class="form-control" name="home_screen_holder[]" id="new_home_screen_holder">
                                            <option value="">--Select One--</option>
                                            @foreach($home_screen_holders as $holder)
                                                <option value="{{$holder->id}}">{{$holder->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="holder_position">Holder Position</label>
                                        {{ Form::text("holder_position[]", old('holder_position'), array("class" => "form-control", "id" => "new_holder_position")) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <button type="button" style="margin-top: 14%;" class="btn btn-danger float-right remove_button">Remove</button>
                                    </div>
                                </div>
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
        $(document).ready(function () {
            var count = $('#total_count').val();
            console.log('count:'+count);
            $('#add_row').click(function () {
                var limit = $('#total_holders').val()-1;
                console.log('limit:'+limit);
                if (count == limit) {
                    return false;
                }
                count++;
                var html = $("#holder_row").html();
                $('#row_1').attr('id', 'row' + '_' + count);
                $("#holder_div").append(html);
            });

            $(document).on("click", ".remove_button", function (){
                var parent_id = $(this).parent().parent().parent().attr('id');
                $('#'+parent_id).remove();
                // $('#total_holders').val()+1;
                count--;
            });
        });
    </script>
@endsection
