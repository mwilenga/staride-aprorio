@extends('developer.layouts.main')
@section("content")
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">User Notification</h1>
        </div>

        <!-- Content Row -->
        <div class="row">

            <div class="col-xl-6 col-md-6 mb-6">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <form name="user-notification-generate" id="user-notification-generate">
                                @csrf
                                <div class="form-group">
                                    <label for="user_id">User</label>
                                    {{ Form::select("user_id", $users_arr, old('user_id'), array("class" => "form-control", "id" => "user_id", "required")) }}
                                </div>
                                <div class="form-group displayTag">
                                    <label for="notification_content">Notification Content</label>
                                    {{ Form::textarea('notification_content', $notification_content, array("class" => "form-control", "id" => "notification_content", "required", "rows" => "6")) }}
                                </div>
                                <input type="hidden" name="player_ids" id="player_ids" value="">
                                <button type="submit" class="btn btn-primary displayTag">Send Notification</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-12 mb-12">
                <div class="row">
                    <div class="col-xl-12 col-md-12 mb-12">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row">
                                    <label>Player Ids</label><br>
                                    <div id="player_ids_data" style="width: 100%">

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
    <script>
        reset();

        function reset(){
            $(".displayTag").css("display","none");
            $("#player_ids_data").html("");
            $("#response").html("");
        }

        $("#user_id").change(function () {
            reset();
            var value = $(this).val();
            var token = $('[name="_token"]').val();
            $.ajax({
                type: "POST",
                url: "{{route("developer.get.user.playerids")}}",
                headers: {
                    'X-CSRF-TOKEN': token
                },
                data: {
                    user_id: value
                },
                success: function (response) {
                    $("#player_ids_data").html(response.data);
                    if(response.result == 1){
                        $(".displayTag").css("display","block");
                    }
                }
            });
        });

        $("#user-notification-generate").on("submit", function (event) {
            event.preventDefault();

            var player_ids = "";
            $('.player_id_check').each(function () {
                var id = $(this).attr('id');
                if ($('#' + id).prop('checked')) {
                    player_ids += $('#' + id).val() + "%%";
                    // alert($('#' + id).val() + ' is checked');
                }
            });

            if(player_ids != ""){
                $("#player_ids").val(player_ids);
            }

            var formValues = $(this).serialize();
            $.post("{{route("developer.user.notification")}}", formValues, function (data) {
                $("#response").html(data);
            });
        });
    </script>
@endsection
