@extends('developer.layouts.main')
@section("content")
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">API Testing</h1>
        </div>

        <!-- Content Row -->
        <div class="row">

            <div class="col-xl-12 col-md-12 mb-12">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <form name="api_testing" id="api_testing" class="form-inline">
                                @csrf
                                <div class="form-group">
                                    <input type="text" readonly class="form-control" id="method_type" name="method_type" value="">
                                </div>
                                &nbsp;
                                <div class="form-group">
                                    <input type="text" style="width: 300px;" readonly class="form-control" id="base_url" name="base_url" value="{{URL::to('/')}}">
                                </div>
                                &nbsp;
                                <div class="form-group">
                                    <select class="form-control select2" style="width: 400px;"  name="api" id="api" required>
                                        <option>--Select--</option>
                                        @foreach($api_list as $api)
                                            <option value="{{$api['uri']}}" method="{{$api['method']}}" calling-function="{{$api['calling_function']}}">{{$api['uri']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                &nbsp;
                                <input type="hidden" name="public_key" id="public_key" value="">
                                <input type="hidden" name="secret_key" id="secret_key" value="">
                                <input type="hidden" name="token" id="token" value="">
                                <input type="hidden" name="locale_type" id="locale_type" value="">
                                <input type="hidden" name="request_data" id="request_data" value="">
                                <button type="submit" class="btn btn-primary mb-2">TEST Api</button>
                            </form>
                            <div class="col-md-12">
                                <small class="form-text text-muted"><b>Calling Function : </b></small><small id="calling-fucntion" class="form-text text-muted">--</small>
                            </div>
                            <div class="col-md-12">
                                <small class="form-text text-muted"><b>Access Pin : </b></small><small id="access-pin" class="form-text text-muted">{{$merchant->access_pin}}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-md-12 mb-12">
                <div class="row">
                    <div class="col-xl-12 col-md-12 mb-12">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row">
                                    <label>Header Params</label><br>
                                    <div id="header_params" style="width: 100%">
                                        <div class="form-group">
                                            <label for="token">Bearer Token</label>
                                            <input type="text" class="form-control" id="token_data" name="token_data" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="public_key">Public Key</label>
                                            <input type="text" class="form-control" id="public_key_data" name="public_key_data" value="{{$merchant->merchantPublicKey}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="secret_key">Secret Key</label>
                                            <input type="text" class="form-control" id="secret_key_data" name="secret_key_data" value="{{$merchant->merchantPublicKey}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="locale">Locale</label>
                                            <input type="text" class="form-control" id="locale_data" name="locale_data" value="en">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12 col-md-12 mb-12">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row">
                                    <label>Request Params</label><br>
                                    <div id="request_params" style="width: 100%">
                                        <div class="form-group">
                                            <label for="locale">Request Data</label>
                                            <textarea type="text" class="form-control" id="request_data_data" name="request_data_data" rows="5"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-xl-12 col-md-12 mb-12">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row">
                                    <label>Response</label><br>
                                    <div id="response" style="width: 100%">

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                closeOnSelect: false
            });
        });

        $("#api").change(function () {
            var method = $('option:selected', this).attr('method');
            $("#method_type").val(method);
            var calling_function = $('option:selected', this).attr('calling-function');
            $("#calling-fucntion").html(calling_function);
        });

        $("#api_testing").on("submit", function (event) {
            event.preventDefault();

            $("#public_key").val($("#public_key_data").val());
            $("#secret_key").val($("#secret_key_data").val());
            $("#locale_type").val($("#locale_data").val());
            $("#token").val($("#token_data").val());
            $("#request_data").val($("#request_data_data").val());

            var formValues = $(this).serialize();
            $.post("{{route("developer.api.testing")}}", formValues, function (data) {
                $("#response").html(data);
            });
        });
    </script>
@endsection
