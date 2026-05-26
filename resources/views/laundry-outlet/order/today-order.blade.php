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
                        @lang("$string_file.today_order")
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
                            <th>@lang("$string_file.laundry_service_details") </th>
                            <th>@lang("$string_file.payment_details")</th>
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.details")</th>
                            <th>@lang("$string_file.date")</th>
                            <th>@lang("$string_file.images")</th>
                            <th>@lang("$string_file.expire_at")</th>
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
                                    <br>
                                </td>
                                <td style="text-align: center">
                                    {{ $arr_status[$order->order_status] }}
                                </td>
                                <td> {{ $order->CountryArea->CountryAreaName }}</td>
                                <td>
                                    <strong>@lang("$string_file.user_details")</strong><br>
                                    {{$user_name}} <br>
                                    {{$user_phone}} <br>
                                    {{$user_email}} <br> <br>
                                    <strong>@lang("$string_file.additional_notes")</strong><br>
                                    {{$order->additional_notes}} <br>
                                </td>


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
                                    @else
                                        echo $order->estimate_delivery_time.'<br>';
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

                                </td>
                                <td>
                                    @if(!empty($order->order_item_images))
                                        @php
                                            $arr_image = json_decode($order->order_item_images);
                                        @endphp
                                        @foreach($arr_image as $image)
                                            @if(!empty($image))
                                            <a href="{{get_image($image,"laundry_order_items",$order->merchant_id)}}"
                                               target="_blank">
                                                <button class="btn btn-icon btn-warning"
                                                        style="margin:10px" type="button">
                                                    <i class="icon fa fa-picture-o ml-1 mr-1" title="Info" style=""></i>
                                                </button>
                                            </a>
                                            @endif
                                    @endforeach
                                    @endif
                                </td>
                                <td>
                                    @php
                                        if(!empty($config)){
                                            $expire_minute=$config->order_expire_time;
                                        }
                                        else{
                                            $expire_minute=10; // minutes
                                        }
                                     if($order->order_type == 1) //now or later
                                     {
                                            $order_time_stamp = $order->order_timestamp;
                                            $expire_time = strtotime("+$expire_minute minutes", $order_time_stamp);
                                     }
                                     elseif(!empty($order->service_time_slot_detail_id))
                                     {
                                         $order_time_stamp = $order->order_date;
                                         if(!empty($slot_time))
                                         {
                                             $expire_time = strtotime($order->order_date.' '.$slot_time);
                                             $order_time_stamp = $expire_time;
                                         }
                                     }
                                     else{
                                            $order_time_stamp = $order->order_timestamp;
                                            $expire_time = strtotime("+$expire_minute minutes", $order_time_stamp);
                                     }
                                     $expire_time = date("Y-m-d H:i:s",$expire_time);
                                    @endphp
                                    {{convertTimeToUSERzone($expire_time, $order->CountryArea->timezone,null,$order->Merchant)}}
                                </td>
                                <td>
                                    <a title="{{ $order->drop_location }}" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $order->drop_location }}" class="btn btn-icon btn-danger ml-20">
                                        <i class="icon fa-tint"></i>
                                    </a>
                                </td>
                                <td>
                                    @if($order->ServiceType->type == 1)

                                             @if(($order->order_date == date('Y-m-d',strtotime($order->created_at))) || (($order->order_date == date('Y-m-d')) && $current_time_stamp <= $order_time_stamp))
                                                <a target="_blank" title=""
                                                   href="{{route('laundry-outlet.order.assign',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                                    <span class="fa fa-check" title="@lang("$string_file.manual_assign")"></span>
                                                </a>
                                                <a data-original-title="" data-target="#auto_assign" data-toggle="modal"
                                                   data-id="{{ $order->id }}" data-placement="top" data-backdrop="static" data-keyboard="false"
                                                   class="btn btn-sm btn-success menu-icon btn_money action_btn auto_assign">
                                                    <span class="fa fa-send" title="@lang("$string_file.auto_assign")"></span>
                                                </a>
                                            @endif
{{--                                            @php--}}
{{--                                                $diff = strtotime(date('Y-m-d H:i:s')) - $order->order_timestamp;--}}
{{--                                            @endphp--}}
{{--                                            @if($diff > $booking_config->driver_request_timeout)--}}
{{--                                                <a target="_blank" title=""--}}
{{--                                                   href="{{route('business-segment.order.assign',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">--}}
{{--                                                    <span class="fa fa-check" title="@lang("$string_file.manual_assign")"></span>--}}
{{--                                                </a>--}}
{{--                                            @endif--}}


                                        <a target="_blank" title="@lang("$string_file.reject")"
                                           href="{{route('laundry-outlet.order-reject',$order->id)}}" class="btn btn-sm btn-danger menu-icon btn-edit action_btn"><span class="fa fa-edit"></span>
                                        </a>
                                    @else
                                        <a target="_blank" title=""
                                           href="{{route('laundry-outlet.order.accept',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                            <span class="fa fa-check" title="@lang("$string_file.accept")"></span>
                                        </a>
                                        <a target="_blank" title="@lang("$string_file.reject")"
                                           href="{{route('laundry-outlet.order-reject',$order->id)}}" class="btn btn-sm btn-danger menu-icon btn-edit action_btn"><span class="fa fa-edit"></span>
                                        </a>
                                    @endif
                                        <a target="_blank" title="@lang("$string_file.order_details")"
                                           href="{{route('laundry-outlet.order.detail',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                            <span class="fa fa-info-circle" title="@lang("$string_file.order_details")"></span>
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


    <div class="modal fade text-left" id="auto_assign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b>@lang("$string_file.assign_order_to_delivery_candidate")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['name'=>"","url"=>route('laundry-outlet.order.auto-assign')]) !!}
                <div class="modal-body">
                    <label>@lang("$string_file.are_you_sure_assign")</label>
                    <div class="form-group">
                        {{--                            <input type="number" name="otp" placeholder="Enter Driver App OTP"--}}
                        {{--                                   class="form-control" required>--}}
                        <input type="hidden" name="laundry_outlet_order_id" id="laundry_outlet_order_id">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="@lang("$string_file.no")">
                    <input type="submit" id="sub" class="btn btn-primary" value="@lang("$string_file.yes")">
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        $(document).on("click", ".auto_assign", function () {
            var order_id = $(this).data('id');
            $(".modal-body #laundry_outlet_order_id").val( order_id );
        });
    </script>
@endsection
