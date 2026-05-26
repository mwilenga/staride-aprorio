@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        {!! Form::open(["name"=>"deliver-order","url"=>route("business-segment.deliver-order-request",$order->id)]) !!}
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="btn-group float-md-right">
                        @if($order->is_order_completed != 1)
                            <button type="submit" class="btn btn-icon btn-info mr-1" style="margin:10px  ">@lang("$string_file.deliver_order")</button>
                        @endif
                        <a href="{{route("business-segment.pending-order-delivery")}}">
                            <button type="button" class="btn btn-icon btn-success mr-1" style="margin:10px  "><i class="wb-reply"></i></button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                      @lang("$string_file.deliver_order")
                    </h3>
                </header>
                <div class="panel-body container-fluid printableArea">
                    @php $currency = $order->CountryArea->Country->isoCode; @endphp
                    {{--{!! Form::select('payment_method_id',$arr_payment_method,$order->payment_method_id,['class'=>"form-control",'required_true']) !!}--}}
                    @if($order->payment_status != 1)
                        <div class="row">
                            <div class="col-lg-12">
                                <h5> @lang("$string_file.collect_payment") : {{trans_choice("$string_file.collecting_order_payment",3,["AMOUNT"=>$currency.$order->final_amount_paid])}}</h5>
                            </div>
                        </div>
                        <br>
                    @endif
                    <div class="row">

                        {{--<div class="col-lg-3">--}}
                            {{--<h4>@lang("$string_file.store_details")</h4>--}}
                            {{--<span>--}}
                                {{--<img height="60" width="100" src="{{ get_image($business_segment->business_logo,'business_logo',$business_segment->merchant_id,true) }}" alt="...">--}}
                               {{--<br> --}}
                                {{--@lang("$string_file.name"): {{$business_segment->full_name}},--}}
                            {{--</span>--}}
                            {{--<address>--}}
                                {{--{{$business_segment->address}}--}}
                                {{--<br>--}}
                                {{--<abbr title="Mail">@lang("$string_file.email"):</abbr>&nbsp;&nbsp;{{$business_segment->email}}--}}
                                {{--<br>--}}
                                {{--<abbr title="Phone">@lang("$string_file.phone"):</abbr>&nbsp;&nbsp;{{$business_segment->phone_number}}--}}
                                {{--<br>--}}
                            {{--</address>--}}
                        {{--</div>--}}

                        <div class="col-lg-3">
                            <h4>@lang("$string_file.user_details")</h4>
                            <span>
                                {{--<img height="60" width="100" src="{{ get_image($business_segment->business_logo,'business_logo',$business_segment->merchant_id,true) }}" alt="...">--}}
                               @lang("$string_file.name") : {{$order->User->first_name.' '.$order->User->last_name}},
                            </span>
                            <address>
                                @lang("$string_file.address") :
                                @if($order->drop_location)
                                    {{$order->drop_location}}
                                @else
                                    {{$order->UserAddress->house_name}},
                                    {{$order->UserAddress->floor}}
                                    {{$order->UserAddress->building}}
                                    <br>
                                    {{$order->UserAddress->address}}
                                @endif
                                <br>
                                <abbr title="Phone">@lang("$string_file.phone"):</abbr>&nbsp;&nbsp;
                                @if($hide_user_info_from_store == 1)
                                    ******
                                @else
                                    {{is_demo_data($order->User->UserPhone, $order->Merchant)}}
                                @endif
                                <br>
                                <abbr title="Email">@lang("$string_file.email"):</abbr>&nbsp;&nbsp;
                                @if($hide_user_info_from_store == 1)
                                    ******
                                @else
                                    {{is_demo_data($order->User->email, $order->Merchant)}}
                                @endif
                                <br>
                            </address>
                            {{--<address>--}}
                                {{--@lang("$string_file.address") : {{$order->drop_location}}--}}
                                {{--<br>--}}
                                {{--<abbr title="Mail">@lang("$string_file.email"):</abbr>&nbsp;&nbsp;{{$order->User->email}}--}}
                                {{--<br>--}}
                                {{--<abbr title="Phone">@lang("$string_file.phone"):</abbr>&nbsp;&nbsp;{{$order->User->UserPhone}}--}}
                                {{--<br>--}}
                            {{--</address>--}}
                        </div>

                        <div class="col-lg-3 offset-lg-6 text-right">
                            <h4>@lang("$string_file.order_details")</h4>
                            <p>
                                <a class="font-size-15" href="javascript:void(0)">@lang("$string_file.order_id") : #{{$order->merchant_order_id}}</a>
                            </p>
                            <span>@lang("$string_file.date") : {{date(getDateTimeFormat($order->Merchant->datetime_format,2))}}</span>
                            <br>

                            <span>@lang("$string_file.grand_total") : {{$currency.$order->final_amount_paid}}</span>
                            <br>

                            <span>@lang("$string_file.payment_method") : {{$order->PaymentMethod->MethodName($order->merchant_id)}}</span>
                            <br>

                            <span>@lang("$string_file.payment_status") :
                                @if($order->payment_status == 1)
                                    <i class="btn btn-sm btn-success">@lang("$string_file.done")</i>
                                @else
                                    <i class="btn btn-sm btn-danger">@lang("$string_file.pending")</i>
                                @endif
                            </span>
                            <br>

                        </div>
                    </div>
                    <h3>@lang("$string_file.product_details")</h3>
                    <hr>
                    <div class="page-invoice-table table-responsive">
                        <table class="table table-hover text-right">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">@lang("$string_file.product_name")</th>
                                <th class="text-center">@lang("$string_file.product_variant")</th>
                                <th class="text-center">@lang("$string_file.description")</th>
                                <th class="text-right">@lang("$string_file.quantity")</th>
                                <th class="text-right">@lang("$string_file.price")</th>
                                @if($order->Segment->slag =="FOOD")
                                    <th class="text-right">@lang("$string_file.option_amount")</th>
                                @endif
                                <th class="text-right">@lang("$string_file.discount")</th>
                                <th class="text-right">@lang("$string_file.amount")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sn = 1;$tax =0; $tip=0;$arr_option_amount = []; $option_amount = []; @endphp
                            @foreach($order->OrderDetail as $product)
                                @php $lang = $product->Product->langData($order->merchant_id);
                                 $tax =  !empty($order->tax) ? $order->tax : 0;
                                 $tip =  !empty($order->tip_amount) ? $order->tip_amount : 0;
                                @endphp
                            <tr>
                                <td class="text-center">
                                    {{$sn}}
                                </td>
                                <td class="text-center">
                                    {{$lang->name}}
                                    @if(!empty($product->options))
                                        {{'('}}
                                        @php  $arr_cart_option = !empty($product->options) ? json_decode($product->options,true) : []; @endphp
                                        @foreach($arr_cart_option as $option)
{{--                                                @if($option['amount'])--}}
{{--                                                    {{$currency.$option['amount']}}--}}
{{--                                                @endif--}}
                                            @php $arr_option_amount[] = $option['amount'];
                                                $option_amount[] = $option['amount'];
                                            @endphp
                                            {{$option['option_name']}},

                                        @endforeach
                                        {{')'}}
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{$product->ProductVariant->Name($order->merchant_id)}}
                                </td>
                                <td class="text-center">
                                    {{$lang->description}}
                                </td>
                                <td>
                                    {{$product->quantity}}
                                </td>
                                <td>
                                    {{$product->price}}
                                </td>
                                @if($order->Segment->slag =="FOOD")
                                    <td>
                                        {{array_sum($option_amount)}}
                                    </td>
                                @endif
                                <td>
                                    {{$product->discount}}
                                </td>
                                <td>
                                    {{$product->total_amount}}
                                </td>
                            </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="text-right clearfix">
                        <div class="float-right">
                            <p>@lang("$string_file.cart_amount") :
                                <span>{{$currency.$order->cart_amount}}</span>
                            </p>
                            <p>@lang("$string_file.delivery_charge") :
                                <span>{{$currency.$order->delivery_amount}}</span>
                            </p>
                            <p>@lang("$string_file.tax") :
                                <span>{{$currency.$tax}}</span>
                            </p>
                            <p>@lang("$string_file.tip") :
                                <span>{{$currency.$tip}}</span>
                            </p>
                            <p class="page-invoice-amount">@lang("$string_file.grand_total"):
                                <span>{{$currency.$order->final_amount_paid}}</span>
                            </p>
                        </div>
                    </div>

{{--                    <div class="text-right">--}}
{{--                        <button type="submit" class="btn btn-animate btn-animate-side btn-primary">--}}
{{--                <span><i class="icon wb-shopping-cart" aria-hidden="true"></i> Proceed--}}
{{--                  to payment</span>--}}
{{--                        </button>--}}
{{--                        <button type="button" class="btn btn-animate btn-animate-side btn-default btn-outline"--}}
{{--                                onclick="javascript:window.print();">--}}
{{--                            <span><i class="icon wb-print" aria-hidden="true"></i> Print</span>--}}
{{--                        </button>--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/jquery.PrintArea.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $(".print_invoice").click(function(){
                var mode = 'popup'; //popup
                var close = mode == "popup";
                var options = { mode : mode, popClose : true, popHt : 900, popWd: 900, };
                $(".printableArea").printArea( options );
            });
        });
    </script>
@endsection
