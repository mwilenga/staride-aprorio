@extends('laundry-outlet.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                        @lang("$string_file.pending_pickup_verification   ")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    {!! $search_view !!}
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
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
                            @php $order_history = array_column(json_decode($order->order_status_history,true),'order_status');@endphp
{{--                            @if(!in_array(7,$order_history))--}}

                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $order->merchant_order_id }}</td>
                                <td>{{ $order->ServiceType->ServiceName($order->name) }}</td>
                                <td>
                                    @php $service_details = $order->LaundryOutletOrderDetail;@endphp
                                    @foreach($service_details as $service)
                                        {{$service->Service->Name($order->merchant_id)}} <br>
                                    @endforeach

                                </td>
                                <td>
                                    {{trans("$string_file.mode").": ". $order->PaymentMethod->payment_method}}<br>
                                    {{trans($string_file.".cart_amount").': '.$order->cart_amount}} <br>

                                    <br>
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

                                <td>{{ $order->order_type == 1 ? trans("$string_file.now") : trans("$string_file.later")}}</td>
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
                                    {!! \Carbon\Carbon::parse(convertTimeToUSERzone($drop_date_time_slot->date, $order->CountryArea->timezone, null, $order->Merchant, 2))->format('d-m-Y') !!}
                                    <br>
                                    {{ \Carbon\Carbon::parse($drop_date_time_slot->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($drop_date_time_slot->to_time)->format('h:i A') }}
                                    @endif
                                    <br>
                                    @if (!empty($order->delay_date_time))
                                    <strong>@lang("$string_file.new_drop")</strong><br>
                                    {{ \Carbon\Carbon::parse($order->delay_date_time)->format('d-m-Y h:i A') }}
                                    @endif
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone,null,$order->Merchant) !!}
                                </td>
                                <td>
                                    <a title="{{ $order->drop_location }}" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $order->drop_location }}"
                                       class="btn btn-icon btn-danger ml-20">
                                        <i class="icon fa-tint"></i>
                                    </a>
                                </td>
                                <td>

                                        <a data-original-title="" data-target="#pickup_otp_verify" data-toggle="modal" data-id="{{ $order->id }}" data-placement="top" data-backdrop="static" data-keyboard="false" class="btn btn-sm btn-success menu-icon btn_money action_btn pickup_otp_verify">
                                            <span class="fa fa-arrow-right" title="@lang(" $string_file.order_pickup_verification")"></span>
                                        </a>

                                    <a target="_blank" title="@lang("$string_file.order_details")"
                                       href="{{route('laundry-outlet.order.detail',$order->id)}}"
                                       class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                        <span class="fa fa-info-circle" title="@lang("$string_file.order_details")"></span>
                                    </a>
                                    <a target="_blank" title="@lang("$string_file.cancel")"
                                       href="{{route('laundry-outlet.order-cancel',$order->id)}}"
                                       class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span
                                                class="fa fa-eye-slash"></span>
                                    </a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
{{--                            @endif--}}
                        @endforeach
                        </tbody>
                    </table>

                    <div class="modal fade text-left" id="pickup_otp_verify" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b>@lang("$string_file.order_pickup_verification")</b></label>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('laundry-outlet.order.pickup.verify') }}" method="post"  id="verifyForm">
                                    @csrf
                                    <div class="modal-body">
                                        <label>@lang("$string_file.otp") </label>
                                        <div class="form-group">
                                            <input type="number" name="otp" id ="otp_val" placeholder="Enter Driver App  OTP" class="form-control" required>
                                            <input type="hidden" name="laundry_outlet_order_id" id="order_id">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="@lang("$string_file.close")">
                                        <input type="submit" id="sub" class="btn btn-primary" value="@lang("$string_file.save")">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @include('merchant.shared.table-footer', ['table_data' => $arr_orders, 'data' => $arr_search])
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script>
{{--        function autoSubmit(id, otp){--}}
{{--            let form = $('#verifyForm');--}}
{{--            $('#otp_val').val(otp);--}}
{{--            $('#order_id').val(id);--}}
{{--            form.submit();--}}
{{--        }--}}
        $(document).on("click", ".pickup_otp_verify", function() {
       var order_id = $(this).data('id');
       $(".modal-body #order_id").val(order_id);
   });
    </script>
@endsection