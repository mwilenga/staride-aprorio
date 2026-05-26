@extends('laundry-outlet.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
{{--                       <a href="{{route("laundry-outlet.start-order-process",$order->id)}}"> <button type="submit" class="btn btn-primary">--}}
{{--                            <i class="fa fa-spinner"></i>&nbsp;@lang("$string_file.start_processing")--}}
                         <a class="btn btn-primary" href="{{route("laundry-outlet.start-order-process", ["id"=>$order->id])}}">
                                <i class="fa fa-spinner"></i>&nbsp;@lang("$string_file.start_processing")
                         </a>

                    </div>
                    <h3 class="panel-title"><i class="fa fa-product-hunt" aria-hidden="true"></i>
                        @lang("$string_file.order_details") #{{$order->merchant_order_id}}
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <h5>@lang("$string_file.service") @lang("$string_file.details"): - </h5>
                    <div class="page-invoice-table table-responsive">
                        <table class="table table-hover text-right">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th  class="text-center">@lang("$string_file.service")</th>
                                <th  class="text-center">@lang("$string_file.description")</th>
                                <th class="text-right">@lang("$string_file.quantity")</th>
                                <th class="text-right">@lang("$string_file.price")</th>

                                <th class="text-right">@lang("$string_file.discount")</th>
                                <th class="text-right">@lang("$string_file.total_amount")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sn = 1 @endphp
                            @foreach($order->LaundryOutletOrderDetail as $service)
                                @php $lang = $service->Service->langData($order->merchant_id); $option_amount = []; @endphp
                                <tr>
                                    <td class="text-center">
                                        {{$sn}}
                                    </td>
                                    <td class="text-center">
                                        {{$lang->name}}

                                    </td>

                                    <td class="text-center">
                                        {{$lang->description}}
                                    </td>
                                    <td>
                                        {{$service->quantity}}
                                    </td>
                                    <td>
                                        {{$service->price}}
                                    </td>
                                    <td>
                                        {{$service->discount}}
                                    </td>
                                    <td>
                                        {{$service->total_amount}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <h5>@lang("$string_file.additional_notes") : - </h5>
                    <div class="row">
                        <div class="col-md-12">
                            <p>
                              {{$order->additional_notes}}
                            </p>
                        </div>
                    </div>
                    <h5>@lang("$string_file.images") : - </h5>
                    <div class="row">

                        @if(!empty($order->order_item_images))
                            @php
                                $arr_image = json_decode($order->order_item_images);
                            @endphp
                            @foreach($arr_image as $image)
                                @if(!empty($image))
                                <div class="col-md-6">
                                    <img src="{{get_image($image,"laundry_order_items",$order->merchant_id)}}"  name="images" width="250" height="250">
                                </div>
                                
                                @endif
                            @endforeach
                        @endif
                        
                        
                        
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-6 col-xs-12">
                            <h5>@lang("$string_file.user_details") : - </h5>
                            <div class="card my-2 shadow  bg-white h-240">
                                <div class="row p-3 mt-30 ml-30">
                                        <div class="col-md-4 col-xs-6">
                                            <img height="100" width="100" class="rounded-circle"
                                                 src="@if($order->User->UserProfileImage) {{ get_image($order->User->UserProfileImage,'user',$order->merchant_id) }}@else{{get_image()}}@endif">
                                        </div>
                                        <div class="col-md-8 col-xs-6">
                                            <p>
                                                <span class="font-size-20">{{is_demo_data($order->User->first_name,$order->Merchant)}}</span>
                                                <br>
                                                <span title="Phone">@lang("$string_file.phone"):</span>&nbsp;&nbsp;{{is_demo_data($order->User->UserPhone,$order->Merchant)}}
                                                <span title="Phone">@lang("$string_file.email"):</span>&nbsp;&nbsp;{{is_demo_data($order->User->email,$order->Merchant)}}
                                                <br>
                                            </p>
                                        </div>
                                </div>
                                <div class="row mt-10 ml-20">
                                    <div class="col-md-4 col-xs-4 pt-20 pl-30">
                                        @lang("$string_file.address") :
                                    </div>
                                    <div class="col-md-8 col-xs-8">
                                            <address>
                                                @if(!empty($order->drop_location))
                                                    {{$order->drop_location}}
                                                @elseif(!empty($order->user_address_id))
                                                    {{$order->UserAddress->house_name}},
                                                    {{$order->UserAddress->floor}}
                                                    {{$order->UserAddress->building}}
                                                    <br>
                                                    {{$order->UserAddress->address}}
                                                @endif
                                            </address>
                                            <br>
                                        </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <h5>@lang("$string_file.other_details") : - </h5>
                            <div class="card my-2 shadow  bg-white h-240">
                                <div class="row p-3 mt-30 ml-30">
                                    <div class="col-md-4 col-xs-6">
                                      @lang("$string_file.payment")
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            <span class="font-size-20">{{$order->PaymentMethod->payment_method}}</span>
                                            <br>
                                            <span title="">@lang("$string_file.grand_total"):</span>&nbsp;&nbsp;{{$order->CountryArea->Country->isoCode.$order->final_amount_paid}}
                                        </p>
                                    </div>
                                </div>
                                <div class="row p-3 mt-30 ml-30">
                                    <div class="col-md-4 col-xs-6">
                                        @lang("$string_file.payment_status")
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                           {{$order->payment_status == 1 ? trans("$string_file.paid") : trans("$string_file.pending") }}
                                            <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="row p-3 mt-30 ml-30">
                                    <div class="col-md-4 col-xs-6">
                                        @lang("$string_file.current_status")
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            {{$arr_status[$order->order_status]}}
                                            <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="row mt-10 ml-20">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- 
    <div class="modal fade" id="estimateDelivery" tabindex="-1" role="dialog" aria-labelledby="estimateDeliveryLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{route("laundry-outlet.start-order-process", ["id"=>$order->id])}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="estimateDeliveryLabel">@lang("$string_file.estimate") @lang("$string_file.deliver") @lang("$string_file.time")</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="datetime-local" name="estimate_delivery_time" required
                                   placeholder="<?php echo app('translator')->get("common.start"); ?> <?php echo app('translator')->get("common.date"); ?>"
                                   class="form-control col-md-12 col-xs-12">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
@endsection