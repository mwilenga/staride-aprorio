@php
    $business_segment = get_business_segment(false);
    //$order_request_receiver = $business_segment->order_request_receiver;
@endphp
<div class="site-menubar">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu">
                    {{--                    <li class="site-menu-category">General</li>--}}
                    <li class="site-menu-item">
                        <a href="{{ route('laundry-outlet.dashboard') }}">
                            <i class="site-menu-icon wb-dashboard" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.dashboard")</span>
                        </a>
                    </li>
                    <li class="site-menu-category" id="general-title">@lang("$string_file.service") @lang("$string_file.management")</li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('laundry-outlet.services.index')}}">
                            <i class="site-menu-icon fa fa-product-hunt" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.laundry_services")</span>
                        </a>
                    </li>
{{--                    <li class="site-menu-item">--}}
{{--                        <a class="animsition-link" href="{{route('business-segment.product.inventory.index')}}">--}}
{{--                            <i class="site-menu-icon fa fa-list-ol" aria-hidden="true"></i>--}}
{{--                            <span class="site-menu-title">@lang("$string_file.product_inventory")</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li class="site-menu-item">--}}
{{--                        <a class="animsition-link" href="{{route('business-segment.style-segment.index')}}">--}}
{{--                            <i class="site-menu-icon fa fa-sort-desc" aria-hidden="true"></i>--}}
{{--                            <span class="site-menu-title">@lang("$string_file.style_segment")</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    @if($business_segment->Segment->slag == "FOOD")--}}
{{--                        <li class="site-menu-item">--}}
{{--                            <a class="animsition-link" href="{{route('business-segment.option.index')}}">--}}
{{--                                <i class="site-menu-icon fa fa-optin-monster" aria-hidden="true"></i>--}}
{{--                                <span class="site-menu-title">@lang("$string_file.options")</span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endif--}}
                    <li class="site-menu-category" id="general-title">@lang("$string_file.order_management")</li>
                    <li class="site-menu-item has-sub">
                        <a href="javascript:void(0)">
                            <i class="site-menu-icon fa fa-map-marker" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.new_orders") </span>
                            <span class="site-menu-arrow"></span>
                        </a>
                        <ul class="site-menu-sub">
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{route('laundry-outlet.today-order')}}">
                                    <span class="site-menu-title">@lang("$string_file.today_order") </span>
                                </a>
                            </li>
                                <li class="site-menu-item">
                                    <a class="animsition-link" href="{{route('laundry-outlet.upcoming-order')}}">
                                        <span class="site-menu-title">@lang("$string_file.upcoming_order")</span>
                                    </a>
                                </li>
                        </ul>
                    </li>
                    <li class="site-menu-item has-sub">
                        <a href="javascript:void(0)">
{{--                                                        <i class="site-menu-icon fa-cog" aria-hidden="true"></i>--}}
                            <i class="site-menu-icon fa fa-map-marker" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.ongoing_orders") </span>
                            <span class="site-menu-arrow"></span>
                        </a>
                        <ul class="site-menu-sub">
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{route('laundry-outlet.arrived-order')}}">
                                    <span class="site-menu-title">@lang("$string_file.arrived_orders") </span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{route('laundry-outlet.pending-pick-order')}}">
                                    <span class="site-menu-title"> @lang("$string_file.pending_pickup_verification") </span>
                                </a>
                            </li>
{{--                            <li class="site-menu-item">--}}
{{--                                <a class="animsition-link"--}}
{{--                                   href="{{route('business-segment.pending-pick-order-verification')}}">--}}
{{--                                    <span class="site-menu-title">@lang("$string_file.pending_pickup_verification") </span>--}}
{{--                                </a>--}}
{{--                            </li>--}}
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="{{route('laundry-outlet.pending-order-delivery')}}">
                                    <span class="site-menu-title">@lang("$string_file.pending_order_delivery") </span>
                                </a>
                            </li>
{{--                            <li class="site-menu-item">--}}
{{--                                <a class="animsition-link" href="{{route('laundry-outlet.order-ontheway')}}">--}}
{{--                                    <span class="site-menu-title">@lang("$string_file.ontheway_orders") </span>--}}
{{--                                </a>--}}
{{--                            </li>--}}
                        </ul>
{{--                    </li>--}}
{{--                    <li class="site-menu-item">--}}
{{--                        <a class="animsition-link" href="{{route('business-segment.delivered-order')}}">--}}
{{--                            <i class="site-menu-icon fa fa-truck" aria-hidden="true"></i>--}}
{{--                            <span class="site-menu-title">@lang("$string_file.delivered_orders")</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}

                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('laundry-outlet.order-ontheway')}}">
                            <i class="site-menu-icon fa fa-paypal" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.ontheway_orders") </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('laundry-outlet.completed-order')}}">
                            <i class="site-menu-icon fa fa-paypal" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.completed_orders") </span>
                        </a>
                    </li>
{{--                    <li class="site-menu-item">--}}
{{--                        <a class="animsition-link" href="{{route('business-segment.cancelled-order')}}">--}}
{{--                            <i class="site-menu-icon fa fa-crosshairs" aria-hidden="true"></i>--}}
{{--                            <span class="site-menu-title">@lang("$string_file.cancelled_orders") </span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('laundry-outlet.rejected-order')}}">
                            <i class="site-menu-icon fa fa-recycle" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.rejected_orders") </span>
                        </a>
{{--                    </li>--}}
{{--                    <li class="site-menu-item">--}}
{{--                        <a class="animsition-link" href="{{route('business-segment.expired-order')}}">--}}
{{--                            <i class="site-menu-icon fa fa-recycle" aria-hidden="true"></i>--}}
{{--                            <span class="site-menu-title">@lang("$string_file.auto_expired_orders") </span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li class="site-menu-item">--}}
{{--                        <a class="animsition-link" href="{{route('business-segment.order')}}">--}}
{{--                            <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>--}}
{{--                            <span class="site-menu-title">@lang("$string_file.all_orders") </span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li class="site-menu-item">--}}
{{--                        <a class="animsition-link" href="{{route('business-segment.statistics',$business_segment->id)}}">--}}
{{--                            <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>--}}
{{--                            <span class="site-menu-title">@lang("$string_file.order_statistics")  </span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
                    <li class="site-menu-category" id="general-title">@lang("$string_file.transaction_management")  </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('laundry-outlet.wallet')}}">
                            <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.wallet_transaction")  </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('laundry-outlet.cashouts')}}">
                            <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.cashout_request")  </span>
                        </a>
                    </li>
                    <li class="site-menu-category" id="general-title">@lang("$string_file.reports")</li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('laundry-outlet.earning')}}">
                            <i class="site-menu-icon fa fa-money" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.earning")</span>
                        </a>
                    </li>
                    <li class="site-menu-category" id="general-title">@lang("$string_file.configuration")</li>
                        <li class="site-menu-item">
                            <a href="{{ route('laundry-outlet.configurations') }}">
                                <i class="site-menu-icon wb-settings" aria-hidden="true"></i>
                                <span class="site-menu-title">@lang("$string_file.configuration")</span>
                            </a>
                        </li>
                </ul>
            </div>
        </div>
    </div>
</div>
