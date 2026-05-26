@extends('laundry-outlet.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                      <button class="btn btn-icon btn-warning float-right print_invoice" style="margin:10px;width:115px;" ><i class="icon wb-print" aria-hidden="true"></i>
                          @lang("$string_file.print")
                      </button>
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                      @lang("$string_file.invoice")
                    </h3>
                </header>
                <div class="panel-body container-fluid printableArea">
                    <div class="row">
                        <div class="col-lg-3">
                            <span><img height="60" width="100" src="{{ get_image($outlet->business_logo,'laundry_outlet_logo',$outlet->merchant_id,true) }}" alt="...">
                               <br> {{$outlet->full_name}},
                            </span>
                            <address>
                                {{$outlet->address}}
                                <br>
                                <abbr title="Mail">@lang("$string_file.email"):</abbr>&nbsp;&nbsp;{{$outlet->email}}
                                <br>
                                <abbr title="Phone">@lang("$string_file.phone"):</abbr>&nbsp;&nbsp;{{$outlet->phone_number}}
                                <br>
                            </address>
                        </div>
                        <div class="col-lg-3 offset-lg-6 text-right">
                            <h4>@lang("$string_file.order_invoice")</h4>
                            <p>
                                <a class="font-size-20" href="javascript:void(0)">#{{$order->merchant_order_id}}</a>
                                <br> @lang("$string_file.f_cap_to"):
                                <br>

                                <span class="font-size-20">{{is_demo_data($order->User->first_name.' '.$order->User->last_name, $order->Merchant)}}</span>

                            </p>
                            <address>
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

                                   {{is_demo_data($order->User->UserPhone, $order->Merchant)}}

                                <br>
                            </address>
                            <span>@lang("$string_file.date") : {{date(getDateTimeFormat($order->Merchant->datetime_format,2))}}</span>
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
                                <th class="text-center">@lang("$string_file.service")</th>
                                <th class="text-center">@lang("$string_file.description")</th>
                                <th class="text-right">@lang("$string_file.quantity")</th>
                                <th class="text-right">@lang("$string_file.price")</th>

                                <th class="text-right">@lang("$string_file.discount")</th>
                                <th class="text-right">@lang("$string_file.amount")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sn = 1;$tax =0; $tip=0; $currency = $order->CountryArea->Country->isoCode;
                            $arr_option_amount
                            = []; $option_amount = []; @endphp
                            @foreach($order->LaundryOutletOrderDetail as $product)
                                @php $lang = $product->Service->langData($order->merchant_id);
                                 $tax =  !empty($order->tax) ? $order->tax : 0;
                                 $tip =  !empty($order->tip_amount) ? $order->tip_amount : 0;
                                @endphp
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
                                    {{$product->quantity}}
                                </td>
                                <td>
                                    {{$product->price}}
                                </td>

                                <td>

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

                </div>
            </div>
        </div>
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
