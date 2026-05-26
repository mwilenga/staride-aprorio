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
                        {{trans($string_file.'.ongoing_order')}}
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
                            <th>@lang("$string_file.payment_details")</th>
{{--                            <th>@lang("$string_file.service_area")</th>--}}
{{--                            <th>@lang("$string_file.current_status")</th>--}}
                            <th>@lang("$string_file.service_date")</th>
                            <th>@lang("$string_file.booking_date")</th>
                            <th>@lang("$string_file.pickup")</th>
                            <th>@lang("$string_file.action")</th>
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
                                    {{trans("$string_file.mode").': '.$order->PaymentMethod->payment_method}} <br>
                                    {{trans("$string_file.cart_amount").': '.$order->total_booking_amount}} <br>
                                </td>

{{--                                <td> {{ $order->CountryArea->CountryAreaName }}</td>--}}
{{--                                <td style="text-align: center">--}}
{{--                                    {{ $arr_status[$order->order_status] }}--}}
{{--                                </td>--}}
                                <td>{!! $order->booking_date !!}</td>
                                @php $created_at = convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone,null, $order->Merchant); @endphp
                                <td>{!! $created_at !!}</td>
                                <td>
                                    <a title="{{ $order->drop_location }}" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $order->drop_location }}"
                                       class="btn btn-icon btn-danger ml-20">
                                        <i class="icon fa-tint"></i>
                                    </a>
                                </td>
                                <td>
                                    <a target="_blank" title=""
                                       href="{{route('handyman-store.order.assign',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                        <span class="fa fa-check" title="@lang("$string_file.manual_assign")"></span>
                                    </a>
                                </td>
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
