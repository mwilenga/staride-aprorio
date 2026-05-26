@extends('developer.layouts.main')
@section("content")
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">SMS Gateway Testing</h1>
        </div>

        <!-- Content Row -->
        <div class="row">

            <div class="col-xl-4 col-md-6 mb-6">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <form name="sms-gateway-testing" id="sms-gateway-testing">
                                @csrf
                                <div class="form-group">
                                    <label for="sms_gateway">SMS Gateway</label>
                                    <select class="form-control" id="sms_gateway_config_id" name="sms_gateway_config_id" required>
                                        <option value="">--- Select ---</option>
                                        @foreach($sms_config as $sms)
                                            <option value="{{$sms->id}}">{{$sms->SmsGateways->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number (With Country Code)</label>
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone Number" required>
                                </div>
                                <div class="form-group">
                                    <label for="sms">Test Message</label>
                                    <textarea class="form-control" rows="3" name="sms" id="sms" placeholder="Hello World!" required>Hello World!</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-6">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <label>SMS Gateway Params</label><br>
                            <div id="sms_gateway_params">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-12 mb-12">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <label>Response from SMS Gateway</label><br>
                            <div id="sms_gateway_response" style="width: 100%">

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
        $("#sms_gateway_config_id").change(function () {
            var value = $(this).val();
            var token = $('[name="_token"]').val();
            $.ajax({
                type: "POST",
                url: "{{route("developer.get-sms-gateway-details")}}",
                headers: {
                    'X-CSRF-TOKEN': token
                },
                data: {
                    sms_gateway_config_id: value
                },
                success: function (response) {
                    $("#sms_gateway_params").html(response);
                }
            });
        });

        $("#sms-gateway-testing").on("submit", function(event){
            event.preventDefault();
            var formValues= $(this).serialize();
            $.post("{{route("developer.submit-sms-gateway-testing")}}", formValues, function(data){
                if (data.data) {
                   $("#sms_gateway_response").html(JSON.stringify(data.data));
                }else{
                    $("#sms_gateway_response").html(data);
                }
            });
        });
    </script>
@endsection
