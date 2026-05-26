@extends('developer.layouts.main')
@section("content")
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Preview App Config</h1>
        </div>

        @include('developer.shared.message')

        <!-- Content Row -->
        <div class="row">

            <div class="col-xl-12 col-md-12 mb-12">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <form name="preview_app_config" id="preview_app_config" method="post" action="{{route("developer.preview.config")}}">
                            @csrf
                            <h4>Default Preview Notification Details</h4>
                            <div class="form-group">
                                <label for="web_application_key">Web Onesignal App Key</label>
                                {{ Form::text("web_application_key", old('web_application_key', isset($onesignal['web_application_key']) ? $onesignal['web_application_key'] : ""), array("class" => "form-control", "id" => "web_application_key")) }}
                            </div>

                            <div class="form-group">
                                <label for="web_rest_key">Web Onesignal Rest Key</label>
                                {{ Form::text("web_rest_key", old('web_rest_key', isset($onesignal['web_rest_key']) ? $onesignal['web_rest_key'] : ""), array("class" => "form-control", "id" => "web_rest_key")) }}
                            </div>

                            <div class="form-group">
                                <label for="user_application_key">User Application Key</label>
                                {{ Form::text("user_application_key", old('user_application_key', isset($onesignal['user_application_key']) ? $onesignal['user_application_key'] : ""), array("class" => "form-control", "id" => "user_application_key")) }}
                            </div>

                            <div class="form-group">
                                <label for="user_rest_key">User Rest Key</label>
                                {{ Form::text("user_rest_key", old('user_rest_key', isset($onesignal['user_rest_key']) ? $onesignal['user_rest_key'] : ""), array("class" => "form-control", "id" => "user_rest_key")) }}
                            </div>

                            <div class="form-group">
                                <label for="user_channel_id">User Channel ID</label>
                                {{ Form::text("user_channel_id", old('user_channel_id', isset($onesignal['user_channel_id']) ? $onesignal['user_channel_id'] : ""), array("class" => "form-control", "id" => "user_channel_id")) }}
                            </div>

                            <div class="form-group">
                                <label for="driver_application_key">Driver Application Key</label>
                                {{ Form::text("driver_application_key", old('driver_application_key', isset($onesignal['driver_application_key']) ? $onesignal['driver_application_key'] : ""), array("class" => "form-control", "id" => "driver_application_key")) }}
                            </div>

                            <div class="form-group">
                                <label for="driver_rest_key">Driver Rest Key</label>
                                {{ Form::text("driver_rest_key", old('driver_rest_key', isset($onesignal['driver_rest_key']) ? $onesignal['driver_rest_key'] : ""), array("class" => "form-control", "id" => "driver_rest_key")) }}
                            </div>

                            <div class="form-group">
                                <label for="driver_channel_id">Driver Channel ID</label>
                                {{ Form::text("driver_channel_id", old('driver_channel_id', isset($onesignal['driver_channel_id']) ? $onesignal['driver_channel_id'] : ""), array("class" => "form-control", "id" => "driver_channel_id")) }}
                            </div>

                            <div class="form-group">
                                <label for="business_segment_application_key">Business Segment Application Key</label>
                                {{ Form::text("business_segment_application_key", old('business_segment_application_key', isset($onesignal['business_segment_application_key']) ? $onesignal['business_segment_application_key'] : ""), array("class" => "form-control", "id" => "business_segment_application_key")) }}
                            </div>

                            <div class="form-group">
                                <label for="business_segment_rest_key">Business Segment Rest Key</label>
                                {{ Form::text("business_segment_rest_key", old('business_segment_rest_key', isset($onesignal['business_segment_rest_key']) ? $onesignal['business_segment_rest_key'] : ""), array("class" => "form-control", "id" => "business_segment_rest_key")) }}
                            </div>

                            <div class="form-group">
                                <label for="business_segment_channel_id">Business Segment Channel ID</label>
                                {{ Form::text("business_segment_channel_id", old('business_segment_channel_id', isset($onesignal['business_segment_channel_id']) ? $onesignal['business_segment_channel_id'] : ""), array("class" => "form-control", "id" => "business_segment_channel_id")) }}
                            </div>

                            <h4>Merchant Config</h4>
                            <div class="form-group">
                                <label for="send_notification_to_preview">Send Notification To Preview</label>
                                {{ Form::select('send_notification_to_preview', array("" => "--Select", 1 => "Enable", 2 => "Disable"), old('send_notification_to_preview', isset($merchant->send_notification_to_preview) ? $merchant->send_notification_to_preview : null), array("class" => "form-control", "id" => "send_notification_to_preview", "required")) }}
                            </div>
                            <button type="submit" class="btn btn-primary displayTag">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
@section("js")
@endsection
