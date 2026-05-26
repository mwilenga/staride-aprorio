@extends('laundry-outlet.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('laundry-outlet.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                        @lang("$string_file.completed_orders")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    {!! $search_view !!}
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.order_id")</th>
                            <th>@lang("$string_file.service_type")</th>
                            <th>@lang("$string_file.laundry_service_details")</th>
                            <th>@lang("$string_file.payment_details")</th>
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.order_type")</th>
                            <th>@lang("$string_file.date")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.pickup_drop")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $arr_orders->firstItem();$user_name = ''; $user_phone = ''; $user_email = '';
                        $driver_name = '';$driver_email = '';
                        @endphp
                        @foreach($arr_orders as $order)
                            @php
                            $user_name = is_demo_data($order->User->first_name.' '.$order->User->last_name,$order->Merchant);
                            $user_phone = is_demo_data($order->User->UserPhone,$order->Merchant);
                            $user_email = is_demo_data($order->User->email,$order->Merchant);
                            @endphp

                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $order->merchant_order_id }}</td>
                                <td>{{ $order->ServiceType->ServiceName($order->name) }}</td>
                                <td>
                                    @php $service_details = $order->LaundryOutletOrderDetail; $products = "";@endphp
                                    @foreach($service_details as $service)
                                        {{$service->Service->Name($order->merchant_id)}} <br>
                                    @endforeach
                                </td>
                                <td>
                                    {{trans("$string_file.mode").": ". $order->PaymentMethod->payment_method}}<br>
                                    {{trans($string_file.".cart_amount").': '.$order->cart_amount}} <br>


                                </td>
                                <td style="text-align: center">
                                    {{ $arr_status[$order->order_status] }}
                                </td>
                                <td> {{ $order->CountryArea->CountryAreaName }}</td>
                                <td>
                                    {{$user_name}} <br>
                                    {{$user_phone}} <br>
                                    {{$user_email}} <br>
                                </td>

                                <td> {{ $order->order_type == 1 ? trans("$string_file.now") : trans("$string_file.later")}} </td>
                                <td>
                                    <strong>@lang("$string_file.pickup")</strong><br>
                                    {!! \Carbon\Carbon::parse(convertTimeToUSERzone($order->order_date, $order->CountryArea->timezone, null, $order->Merchant, 2))->format('d-m-Y') !!}

                                    <br>
                                    @php $slot_time = ""; $current_time_stamp = strtotime(date('Y-m-d H:i:s')); @endphp
                                    @if(!empty($order->service_time_slot_detail_id) && empty($order->estimate_delivery_time))
                                        @php
                                            $slot_time = $order->ServiceTimeSlotDetail->to_time;
                                            $start = $order->ServiceTimeSlotDetail->from_time;

                                           $start = strtotime($start);
                                            $start = $time_format == 2  ? date("H:i",$start) : date("h:i A",$start);
                                             $end = strtotime($slot_time);
                                            $end =  $time_format == 2  ? date("H:i",$end) : date("h:i A",$end);
                                            $time = $start." - ".$end;
                                            echo $time.'<br>';
                                        @endphp
                                   
                                    @endif


                                    @php
                                     $drop_date_time_slot =    json_decode($order->drop_date_time_slot);
                                    @endphp
                                    @if(!empty($drop_date_time_slot) )
                                    <strong>@lang("$string_file.drop")</strong><br>
                                    @php
                                     $drop_date =    \Carbon\Carbon::parse(convertTimeToUSERzone($drop_date_time_slot->date, $order->CountryArea->timezone, null, $order->Merchant, 2));
                                    @endphp
                                    {!!  $drop_date->format('d-m-Y') !!}
                                    <br>
                                    {{ \Carbon\Carbon::parse($drop_date_time_slot->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($drop_date_time_slot->to_time)->format('h:i A') }}
                                    @endif

                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone,null,$order->Merchant) !!}
                                </td>
                                <td>
                                    <a title="{{ $order->drop_location }}" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $order->drop_location }}" class="btn btn-icon btn-danger ml-20">
                                        <i class="icon fa-tint"></i>
                                    </a>
                                </td>
                                <td>

                                    <a target="_blank" title="@lang("$string_file.order_details")"
                                       href="{{route('laundry-outlet.order.detail',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                        <span class="fa fa-info-circle" title="@lang("$string_file.order_details")"></span>
                                    </a>

                                    <a target="_blank" title="@lang('"$string_file.invoice"')"
                                       href="{{route('laundry-outlet.order.invoice',$order->id)}}" class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span class="fa fa-print"></span>
                                    </a>

                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('laundry-outlet.shared.table-footer', ['table_data' => $arr_orders, 'data' => $arr_search])
                </div>
            </div>
        </div>
    </div>
@endsection
