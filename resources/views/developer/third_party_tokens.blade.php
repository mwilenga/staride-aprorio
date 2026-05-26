@extends('developer.layouts.main')
@section("content")
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Third Party Client Token Generation</h1>
        </div>

        <!-- Content Row -->
        <div class="row">
            <div class="col-xl-12 col-md-12 mb-12">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row" style="margin-bottom:20px;">
                            <form name="user-token-geneate" id="user-token-geneate">
                                @csrf
                                <button type="submit" class="btn btn-primary">Generate Token</button>
                            </form>
                        </div>
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
            $.post("{{route("developer.user.saveClient")}}", formValues, function(data){
                $("#response").html(data);
            });
        });
    </script>
@endsection
