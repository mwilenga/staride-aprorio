@extends('developer.layouts.main')
@section("content")
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">User Token Generation</h1>
        </div>

        <!-- Content Row -->
        <div class="row">

            <div class="col-xl-6 col-md-6 mb-6">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <form name="user-token-geneate" id="user-token-geneate">
                                @csrf
                                <div class="form-group">
                                    <label for="user_id">User</label>
                                    {{ Form::select("user_id", $users_arr, old('user_id'), array("class" => "form-control", "id" => "user_id", "required")) }}
                                </div>
                                <button type="submit" class="btn btn-primary">Generate</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-12 mb-12">
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
    <!-- /.container-fluid -->
@endsection
@section("js")
    <script>
        $("#user-token-geneate").on("submit", function(event){
            event.preventDefault();
            var formValues= $(this).serialize();
            $.post("{{route("developer.user.token-generation")}}", formValues, function(data){
                $("#response").html(data);
            });
        });
    </script>
@endsection
