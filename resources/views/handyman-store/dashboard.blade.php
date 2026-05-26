@extends('handyman-store.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('success'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert dark alert-icon alert-error alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon fa-warning" aria-hidden="true"></i>{{ session('error') }}
                </div>
            @endif
            <!-- First Row -->
            <!-- Example Panel With Heading -->
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title">@lang("$string_file.service_statistics")</h3>
                </header>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                            <a href="#">
                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                    <div class="card-block bg-white p-20">
                                        <button type="button" class="btn btn-floating btn-sm btn-success"
                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                            <i class="icon fa-cab"></i>
                                        </button>
                                        <span class="ml-10 font-weight-400">@lang("$string_file.new_orders")</span>
                                        <div class="content-text text-center mb-0">
                                            <span class="font-size-18 font-weight-100">{{$new_orders}}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                            <a href="#">
                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                    <div class="card-block bg-white p-20">
                                        <button type="button" class="btn btn-floating btn-sm btn-success"
                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                            <i class="icon fa-cab"></i>
                                        </button>
                                        <span class="ml-10 font-weight-400">@lang("$string_file.assigned")</span>
                                        <div class="content-text text-center mb-0">
                                            <span class="font-size-18 font-weight-100">{{$assigned_orders}}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                            <a href="#">
                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                    <div class="card-block bg-white p-20">
                                        <button type="button" class="btn btn-floating btn-sm btn-success"
                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                            <i class="icon fa-cab"></i>
                                        </button>
                                        <span class="ml-10 font-weight-400">@lang("$string_file.started")</span>
                                        <div class="content-text text-center mb-0">
                                            <span class="font-size-18 font-weight-100">{{$started}}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                            <a href="#">
                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                    <div class="card-block bg-white p-20">
                                        <button type="button" class="btn btn-floating btn-sm btn-warning"
                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                            <i class="icon fa-flag"></i>
                                        </button>
                                        <span class="ml-10 font-weight-400">@lang("$string_file.total")</span>
                                        <div class="content-text text-center mb-0">
                                            <span class="font-size-18 font-weight-100">{{$total_booking}}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                            <a href="#">
                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                    <div class="card-block bg-white p-20">
                                        <button type="button" class="btn btn-floating btn-sm btn-primary"
                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                            <i class="icon wb-users"></i>
                                        </button>
                                        <span class="ml-10 font-weight-400">@lang("$string_file.on_going")</span>
                                        <div class="content-text text-center mb-0">
                                            <span class="font-size-18 font-weight-100">{{$ongoing_booking}}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                    <div class="card-block bg-white p-20">
                                        <button type="button" class="btn btn-floating btn-sm btn-danger"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                            <i class="icon wb-file"></i>
                                        </button>
                                        <span class="ml-10 font-weight-400">@lang("$string_file.cancelled")</span>
                                        <div class="content-text text-center mb-0">
                                            <span class="font-size-18 font-weight-100">{{$cancelled}}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                            <div class="card card-shadow" style="margin-bottom:0.243rem">
                                <div class="card-block bg-white p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-danger"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                        <i class="icon wb-file"></i>
                                    </button>
                                    <span class="ml-10 font-weight-400">@lang("$string_file.expired")</span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-18 font-weight-100">{{$expired_booking}}</span>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-3 col-sm-6 info-panel">
                            <a href="#">
                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                    <div class="card-block bg-white p-20">
                                        <button type="button" class="btn btn-floating btn-sm btn-info"
                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                            <i class="icon fa-calculator"></i>
                                        </button>
                                        <span class="ml-10 font-weight-400">@lang("$string_file.completed")</span>
                                        <div class="content-text text-center mb-0">
                                            <span class="font-size-18 font-weight-100">{{$complete_booking}}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    {{--    <script>--}}
    {{--        $('.toast').toast('show');--}}
    {{--    </script>--}}
@endsection
