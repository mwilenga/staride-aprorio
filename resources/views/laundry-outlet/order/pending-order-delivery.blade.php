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
                        @lang("$string_file.pending_order_delivery")
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
                            <th>@lang("$string_file.product_details")</th>
                            <th>@lang("$string_file.payment_details")</th>
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.user_details")</th>
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
                                <td>{{ $order->ServiceType->ServiceName($order->name)}}</td>
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
                                    {{$user_name}} <br>
                                    {{$user_phone}} <br>
                                    {{$user_email}} <br>
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
                                    @if($order->ServiceType->type == 1)
                                        {{-- @php
                                            $drop_date_time_slot =    json_decode($order->drop_date_time_slot);
                                        @endphp
                                            @if(!empty($drop_date_time_slot) )                                   
                                                @php
                                                    $drop_date =    \Carbon\Carbon::parse(convertTimeToUSERzone($drop_date_time_slot->date, $order->CountryArea->timezone, null, $order->Merchant, 2));
                                                    @endphp
                                                    @if( $drop_date < now()) --}}

                                                        <button target="_blank"  data-id="{{ $order->id }}" data-toggle="modal" data-target="#delayModal"
                                                            class="btn btn-sm btn-warning menu-icon btn_money action_btn">
                                                                <span class="fa fa-clock-o" title="@lang("$string_file.delay_drop")"></span>
                                                        </button>
                                                        <a target="_blank" title=""
                                                                href="{{route('laundry-outlet.order.assign',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                                            <span class="fa fa-check" title="@lang("$string_file.manual_assign")"></span>
                                                        </a>
                                                        <a data-original-title="" data-target="#auto_assign" data-toggle="modal"
                                                                data-id="{{ $order->id }}" data-placement="top" data-backdrop="static" data-keyboard="false"
                                                                class="btn btn-sm btn-success menu-icon btn_money action_btn auto_assign">
                                                            <span class="fa fa-send" title="@lang("$string_file.auto_assign")"></span>
                                                        </a>

                                                     
{{--                                                           
                                                    @endif
                                            @endif --}}
                                                        @else
                                                            <a target="_blank" title=""
                                                            href="{{route('laundry-outlet.deliver-order',$order->id)}}"
                                                            class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                                                <span class="fa fa-info-circle" title="">@lang("$string_file.deliver_order")</span>
                                                            </a>

                                                            <a target="_blank" title="@lang("$string_file.cancel")"
                                                            href="{{route('laundry-outlet.order-cancel',$order->id)}}"
                                                            class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span
                                                                        class="fa fa-eye-slash"></span>
                                                            </a>
                                                            @endif
                                                 
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
    

    <div class="modal fade text-left" id="delayModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b>@lang("$string_file.delay_drop")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('laundry-outlet.order-delay') }}" method="POST">
                        @csrf
                        <input type="hidden" value="{{ old('order_id') }}" name="order_id" id="laundry_outlet_order_id">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="modal-title text-text-bold-600 m-2" id="myModalLabel33"><b>@lang("$string_file.date_time")</b></label>
                                    <input type="datetime-local" name="delay_date_time" required class="form-control" id="futureDateTime"> 
                                    @error('delay_date_time')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 my-4">
                                    <button class="btn btn-primary float-right">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).on("click", ".action_btn", function () {
            var order_id = $(this).data('id');
            $(".modal-body #laundry_outlet_order_id").val( order_id );
        });

      
    </script>
    @error('delay_date_time')
     <script>
          $('#delayModal').modal().show();
     </script>
    @enderror
    <script>
        window.onload = function () {
            const input = document.getElementById('futureDateTime');
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); 
            input.min = now.toISOString().slice(0, 16); 
        };
    </script>
@endsection
