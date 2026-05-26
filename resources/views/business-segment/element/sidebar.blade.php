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
                        <a href="{{ route('business-segment.dashboard') }}">
                            <i class="site-menu-icon wb-dashboard" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.dashboard")</span>
                        </a>
                    </li>
                    @if($business_segment->is_warehouse == 1)
                        <li class="site-menu-category" id="general-title">@lang("$string_file.product_management")</li>
                        <li class="site-menu-item">
                            <a class="animsition-link" href="{{route('business-segment.warehouse.product.index')}}">
                                <i class="site-menu-icon wb-shopping-cart" aria-hidden="true"></i>
                                <span class="site-menu-title">@lang("$string_file.business_segment")</span>
                            </a>
                        </li>
                        <li class="site-menu-item">
                            <a class="animsition-link" href="{{route('business-segment.product.index')}}">
                                <i class="site-menu-icon fa fa-product-hunt" aria-hidden="true"></i>
                                <span class="site-menu-title">@lang("$string_file.product")</span>
                            </a>
                        </li>
                    @else
                        <li class="site-menu-category" id="general-title">@lang("$string_file.product_management")</li>
                        <li class="site-menu-item">
                            <a class="animsition-link" href="{{route('business-segment.product.index')}}">
                                <i class="site-menu-icon fa fa-product-hunt" aria-hidden="true"></i>
                                <span class="site-menu-title">@lang("$string_file.product")</span>
                            </a>
                        </li>
                        <li class="site-menu-item">
                            <a class="animsition-link" href="{{route('business-segment.product.inventory.index')}}">
                                <i class="site-menu-icon fa fa-list-ol" aria-hidden="true"></i>
                                <span class="site-menu-title">@lang("$string_file.product_inventory")</span>
                            </a>
                        </li>
                        <li class="site-menu-item">
                            <a class="animsition-link" href="{{route('business-segment.style-segment.index')}}">
                                <i class="site-menu-icon fa fa-sort-desc" aria-hidden="true"></i>
                                <span class="site-menu-title">@lang("$string_file.style_segment")</span>
                            </a>
                        </li>
                        @if($business_segment->packaging_preference_enable == 1)
                        <li class="site-menu-item">
                            <a class="animsition-link" href="{{route('business-segment.packaging-preferences')}}">
                                <i class="site-menu-icon fa fa-get-pocket" aria-hidden="true"></i>
                                <span class="site-menu-title">@lang("$string_file.packaging_preferences")</span>
                            </a>
                        </li>
                        @endif
                        @if($business_segment->Segment->slag == "FOOD")
                        <li class="site-menu-item">
                            <a class="animsition-link" href="{{route('business-segment.option.index')}}">
                                <i class="site-menu-icon fa fa-optin-monster" aria-hidden="true"></i>
                                <span class="site-menu-title">@lang("$string_file.options")</span>
                            </a>
                        </li>
                        @endif
                        @if($business_segment->Segment->slag == "FOOD" && isset($business_segment->product_availability_time_module_enable) && $business_segment->product_availability_time_module_enable == 1)
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{route('product-availability-time-slabs')}}">
                                    <i class="site-menu-icon fa-clock-o" aria-hidden="true"></i>
                                    <span class="site-menu-title">@lang("$string_file.product_availability")</span>
                                </a>
                            </li>
                        @endif
                    @endif
                    <li class="site-menu-category" id="general-title">@lang("$string_file.order_management")</li>
{{--                        <li class="site-menu-item">--}}
{{--                            <a class="animsition-link" href="{{route('business-segment.new-order')}}">--}}
{{--                                <i class="site-menu-icon fa-cutlery" aria-hidden="true"></i>--}}
{{--                                <span class="site-menu-title">@lang("$string_file.new_orders") </span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
                    <li class="site-menu-item has-sub">
                        <a href="javascript:void(0)">
                            <i class="site-menu-icon fa fa-map-marker" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.new_orders") </span>
                            <span class="site-menu-arrow"></span>
                        </a>
                        <ul class="site-menu-sub">
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{route('business-segment.today-order')}}">
                                    <span class="site-menu-title">@lang("$string_file.today_order") </span>
                                </a>
                            </li>
                            @if($business_segment->Segment->slag != "FOOD")
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{route('business-segment.upcoming-order')}}">
                                    <span class="site-menu-title">@lang("$string_file.upcoming_order")</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    <li class="site-menu-item has-sub">
                        <a href="javascript:void(0)">
                            {{--                            <i class="site-menu-icon fa-cog" aria-hidden="true"></i>--}}
                            <i class="site-menu-icon fa fa-map-marker" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.ongoing_orders") </span>
                            <span class="site-menu-arrow"></span>
                        </a>
                        <ul class="site-menu-sub">
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{route('business-segment.pending-process-order')}}">
                                    <span class="site-menu-title">@lang("$string_file.pending_process") </span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="{{route('business-segment.pending-pick-order-verification')}}">
                                    <span class="site-menu-title">@lang("$string_file.pending_pickup_verification") </span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="{{route('business-segment.pending-order-delivery')}}">
                                    <span class="site-menu-title">@lang("$string_file.pending_order_delivery") </span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{route('business-segment.order-ontheway')}}">
                                    <span class="site-menu-title">@lang("$string_file.ontheway_orders") </span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('business-segment.delivered-order')}}">
                            <i class="site-menu-icon fa fa-truck" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.delivered_orders")</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('business-segment.completed-order')}}">
                            <i class="site-menu-icon fa fa-paypal" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.completed_orders") </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('business-segment.cancelled-order')}}">
                            <i class="site-menu-icon fa fa-crosshairs" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.cancelled_orders") </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('business-segment.rejected-order')}}">
                            <i class="site-menu-icon fa fa-recycle" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.rejected_orders") </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('business-segment.expired-order')}}">
                            <i class="site-menu-icon fa fa-recycle" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.auto_expired_orders") </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('business-segment.order')}}">
                            <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.all_orders") </span>
                        </a>
                    </li>
                    @if($business_segment->is_warehouse != 1)
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('business-segment.statistics',$business_segment->id)}}">
                            <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.order_statistics")  </span>
                        </a>
                    </li>
                    <li class="site-menu-category" id="general-title">@lang("$string_file.transaction_management")  </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('business-segment.wallet')}}">
                            <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.wallet_transaction")  </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('business-segment.cashouts')}}">
                            <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.cashout_request")  </span>
                        </a>
                    </li>
                    <li class="site-menu-category" id="general-title">@lang("$string_file.reports")</li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('business-segment.earning')}}">
                            <i class="site-menu-icon fa fa-money" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.earning")</span>
                        </a>
                    </li>
                    @if($business_segment->order_based_on == 2)
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{route('business-segment.subscription.earning')}}">
                            <i class="site-menu-icon fa fa-money" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.membership_earning")</span>
                        </a>
                    </li>
                    @endif
                    @if($business_segment->Segment->slag == "FOOD" || $business_segment->Segment->slag == "GROCERY")
                    <li class="site-menu-category" id="general-title">@lang("$string_file.configuration")</li>
                    <li class="site-menu-item">
                        <a href="{{ route('business-segment.configurations') }}">
                            <i class="site-menu-icon wb-settings" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.configuration")</span>
                        </a>
                    </li>
                    @endif

                    @if(($business_segment->Segment->slag == "FOOD" || $business_segment->Segment->slag == "GROCERY") && $business_segment->order_based_on == 2)
                    <li class="site-menu-category" id="general-title">@lang("$string_file.purchase_membership_plan")</li>
                    <li class="site-menu-item">
                        <a href="{{ route('business-segment.purchase-membership') }}">
                            <i class="site-menu-icon wb-settings" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.purchase_membership_plan")</span>
                        </a>
                    </li>
                    @endif
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
