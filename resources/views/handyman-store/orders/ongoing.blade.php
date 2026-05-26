    @extends('handyman-store.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                        {{trans($string_file.'.ongoing_orders')}}
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    {!! $search_view !!}
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.booking_id")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.service_details")</th>
                            <th>@lang("$string_file.status")</th>
{{--                            <th>@lang("$string_file.service_area")</th>--}}
{{--                            <th>@lang("$string_file.current_status")</th>--}}
                            <th>@lang("$string_file.service_date")</th>
                            <th>@lang("$string_file.booking_date")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $arr_orders->firstItem();
                        $user_name = ''; $user_phone = ''; $user_email = '';
                        $driver_name = '';$driver_email = '';
                        $arr_price_type = get_price_card_type("web","BOTH",$string_file);
                        @endphp
                        @foreach($arr_orders as $order)
                            @php
                                $currency = $order->CountryArea->Country->isoCode;
                            @endphp
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $order->merchant_order_id }}</td>
                                <td>
                                    {{ is_demo_data($order->User->UserName, $order->Merchant) }} <br>
                                    {{ is_demo_data($order->User->UserPhone, $order->Merchant) }} <br>
                                    {{ is_demo_data($order->User->email, $order->Merchant) }} <br>
                                </td>
                                <td>
                                    @php $arr_services = []; $order_details = $order->HandymanOrderDetail;
                                        foreach($order_details as $details){
                                            $arr_services[] = $details->ServiceType->serviceName;
                                        }
                                    @endphp
                                    {{trans($string_file.".date").' : '.$order->booking_date}}
                                    <br>
                                    {{trans("$string_file.price_type").' : '}}
                                    {{isset($arr_price_type[$order->price_type]) ? $arr_price_type[$order->price_type] : ""}}
                                    <br>
                                    @if($order->price_type == 2)
                                        {{trans("$string_file.service_time").' : '}} {{ $order->total_service_hours}} @lang("$string_file.hour")
                                        <br>
                                    @endif
                                    {{trans("$string_file.service_type").' : '}}
                                    @foreach($order_details as $details)
                                        {{$details->ServiceType->serviceName}}, <br>
                                    @endforeach
                                    <br>
                                    @lang("$string_file.segment") : <strong>{{$order->Segment->name}}</strong>
                                    <br>
                                </td>
                                <td>
                                    @php

                                    $status = "";

                                    switch ($order->order_status){
                                        case "1":
                                            $status = "assigned";
                                            break;
                                        case "2":
                                            $status = "cancelled_by_user";
                                            break;
                                        case "3":
                                            $status = "rejected";
                                            break;
                                        case "4":
                                            $status = "accepted";
                                            break;
                                        case "5":
                                            $status = "cancelled_by_provider";
                                            break;
                                        case "6":
                                            $status = "started";
                                            break;
                                        case "7":
                                            $status = "finished";
                                            break;
                                        case "8":
                                            $status = "expired";
                                            break;
                                        case "10":
                                            $status = "disputed";
                                            break;
                                        default:
                                            $status= "unknown";
                                    }
                                    @endphp
                                    @if($order->order_status == 1 || $order->order_status == 4)
                                    <button class="btn btn-primary">@lang($string_file.".".$status)</button>
                                    @elseif($order->order_status == 2 || $order->order_status == 3 || $order->order_status == 5 || $order->order_status == 8)
                                        <button class="btn btn-danger">@lang($string_file.".".$status)</button>
                                    @elseif($order->order_status == 10 || $order->order_status == 6 || $order->order_status == 7)
                                        <button class="btn btn-warning">@lang($string_file.".".$status)</button>
                                    @endif

                                </td>
                                <td>{!! $order->booking_date !!}</td>
                                @php $created_at = convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone,null, $order->Merchant); @endphp
                                <td>{!! $created_at !!}</td>

                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
{{--                    @include('merchant.shared.table-footer', ['table_data' => $arr_orders, 'data' => $arr_search])--}}
                </div>
            </div>
        </div>
    </div>

    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
