@extends('developer.layouts.main')
@section("styles")
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section("content")
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dev Settings</h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Content Row -->
        <div class="row">

            <div class="col-xl-6 col-md-6 mb-6">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <form name="dev_settings" id="dev_settings" action="{{route("developer.driver.settings.save")}}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="driver_id">Driver Location Logs</label>
                                        {{ Form::select("driver_id[]", $drivers_arr, old('driver_id[]'), array("class" => "form-control", "id" => "driver_id", "multiple"=>"multiple", "required")) }}
                                    </div>
                                    <div class="col-md-4">
                                        <label>Enable or Disable</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="enable" value="1" checked>
                                            <label class="form-check-label" for="enable">Enable</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="disable" value="2">
                                            <label class="form-check-label" for="disable">Disable</label>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-12 mb-12">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <label>Saved Data</label><br>
                            <div id="location_api_logs_enabled_drivers" style="width: 100%">
                                @forelse($enabled_location_logs as $driver)
                                    <div class="mb-2 p-2 border-bottom">
                                        <strong>{{ $driver->first_name }} {{ $driver->last_name }} {{ $driver->phoneNumber }}  </strong> @if($driver->DriverDetail && $driver->DriverDetail->location_logs_enable == 1) <span class="badge badge-success" data-toggle="modal" data-target="#exampleModal" onclick="viewData(
        '{{ $driver->first_name }}',
        '{{ $driver->last_name }}',
        '{{ $driver->phoneNumber }}',
        {!! htmlspecialchars(json_encode($driver->location_logs), ENT_QUOTES, 'UTF-8') !!}
   )">view location api request logs</span> @endif <br>
                                    </div>
                                @empty
                                    <p>No drivers found with enabled location logs.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /.container-fluid -->




    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Location Update Logs</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalBodyContent">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


@endsection
@section("js")
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $("#driver_id").select2({
    width: 'resolve' // need to override the changed default
});

function viewData(fname, lname, number, data) {
    // Header info
    let html = `
        <h6><strong>Driver:</strong> ${fname} ${lname}</h6>
        <p><strong>Phone:</strong> ${number}</p>
        <hr>
    `;

    // Build table for logs
    html += `
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Time</th>
                    <th>IP</th>
                    <th>update via http/socket</th>
                    <th>User Agent</th>
                    <th>Parameter</th>
                    <th>client Timestamp</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach((log, index) => {
        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${log.time}</td>
                <td>${log.ip}</td>
                <th>${log.endpoint}</th>
                <td>${log.user_agent}</td>
                <td>${log.parameter ?? '-'}</td>
                <td>${log.client_timestamp ?? '-'}</td>
            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
    `;

    // Insert into modal body
    document.getElementById("modalBodyContent").innerHTML = html;

}

    </script>
@endsection
