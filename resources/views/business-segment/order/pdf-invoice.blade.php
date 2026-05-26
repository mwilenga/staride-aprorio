<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta content="width=device-width" name="viewport"/>
    <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
    <title></title>
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css"/> -->
    <style>
@page {
    size: A4;
    margin: 15mm 15mm 15mm 15mm;
}

body {
    font-family: sans-serif;
    direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
    unicode-bidi: embed;
}
td, th {
    word-wrap: break-word;
    white-space: normal;
    font-size: 12px;
}
</style>

</head>
<body style="background-color: #f6f6f6; padding:20px">
	<div class="panel-body container-fluid">
        <!-- <div class="row" style="width:100%;clear:both"> -->
        <table width="100%" cellspacing="0" cellpadding="6" style="border-collapse: collapse; margin-bottom:20px;">
            <tr>
                <!-- LEFT / RIGHT AUTO SWITCHES IN RTL -->
                <td width="50%" valign="top">
                    <img height="60" width="100"
                         src="{{ get_image($business_segment->business_logo,'business_logo',$business_segment->merchant_id,true) }}"
                         alt="logo">
        
                    <div style="margin-top:5px; font-weight:bold;">
                        {{ $business_segment->full_name }}
                    </div>
        
                    <div style="margin-top:5px;">
                        {{ $business_segment->address }}
                    </div>
        
                    <table width="100%" cellspacing="0" cellpadding="2"
                           style="border-collapse: collapse; margin-top:5px;">
                        <tr>
                            <td>@lang("$string_file.email")</td>
                            <td>
                                <span dir="ltr">{{ $business_segment->email }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>@lang("$string_file.phone")</td>
                            <td>
                                <span dir="ltr">{{ $business_segment->phone_number }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
        
                <td width="50%" valign="top" style="text-align:right;">
                    <div style="font-size:16px; font-weight:bold;">
                        @lang("$string_file.order_invoice")
                    </div>
        
                    <table width="100%" cellspacing="0" cellpadding="4"
                           style="border-collapse: collapse; margin-top:5px;">
                        <tr>
                            <td>@lang("$string_file.order_id")</td>
                            <td>
                                <span dir="ltr">#{{ $order->merchant_order_id }}</span>
                            </td>
                        </tr>
        
                        <tr>
                            <td>@lang("$string_file.f_cap_to")</td>
                            <td>
                                @if($hide_user_info_from_store == 1)
                                    ******
                                @else
                                    {{ is_demo_data($order->User->first_name.' '.$order->User->last_name, $order->Merchant) }}
                                @endif
                            </td>
                        </tr>
                    </table>
        
                    <div style="margin-top:5px;">
                        @if($order->drop_location)
                            {{ $order->drop_location }}
                        @else
                            {{ $order->UserAddress->house_name }},
                            {{ $order->UserAddress->floor }}
                            {{ $order->UserAddress->building }}<br>
                            {{ $order->UserAddress->address }}
                        @endif
                    </div>
        
                    <table width="100%" cellspacing="0" cellpadding="2"
                           style="border-collapse: collapse; margin-top:5px;">
                        <tr>
                            <td>@lang("$string_file.phone")</td>
                            <td>
                                @if($hide_user_info_from_store == 1)
                                    ******
                                @else
                                    <span dir="ltr">
                                        {{ is_demo_data($order->User->UserPhone, $order->Merchant) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>@lang("$string_file.invoice_date")</td>
                            <td>
                                <span dir="ltr">
                                    {{ date(getDateTimeFormat($order->Merchant->datetime_format,2)) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <h3 style="margin-top:20px">@lang("$string_file.product_details")</h3>
        <hr>
        <div class="page-invoice-table">
            <table width="100%" cellspacing="0" cellpadding="6" style="border-collapse: collapse; table-layout: fixed;">
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
                @php $sn = 1; $currency = $order->CountryArea->Country->isoCode; $arr_option_amount = []; $option_amount = []; @endphp
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
                        {{$product->ProductVariant->discount}}
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
        <table width="100%" cellspacing="0" cellpadding="6" style="border-collapse: collapse; margin-top:20px;">
            <tr>
                <td width="70%"></td>
                <td width="30%">
                    <table width="100%" cellspacing="0" cellpadding="6"
                           style="border-collapse: collapse;">
                        <tr>
                            <td style="text-align:right;">
                                @lang("$string_file.cart_amount")
                            </td>
                            <td style="text-align:left;">
                                <span dir="ltr">{{ $currency }} {{ $order->cart_amount }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">
                                @lang("$string_file.delivery_charge")
                            </td>
                            <td style="text-align:left;">
                                <span dir="ltr">{{ $currency }} {{ $order->delivery_amount }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">
                                @lang("$string_file.tax")
                            </td>
                            <td style="text-align:left;">
                                <span dir="ltr">{{ $currency }} {{ $tax }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">
                                @lang("$string_file.tip")
                            </td>
                            <td style="text-align:left;">
                                <span dir="ltr">{{ $currency }} {{ $tip }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:right; font-weight:bold;">
                                @lang("$string_file.grand_total")
                            </td>
                            <td style="text-align:left; font-weight:bold;">
                                <span dir="ltr">{{ $currency }} {{ $order->final_amount_paid }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </div>
</body>
</html>