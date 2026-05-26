@php
    $agentObj = Auth::user('agent');
@endphp
<div class="site-menubar">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu">
                    <li class="site-menu-item">
                        <a href="{{ route('agent.dashboard') }}">
                            <i class="site-menu-icon wb-dashboard" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.dashboard")</span>
                        </a>
                    </li>
                    {{--<li class="site-menu-category">@lang("$string_file.ride")</li>--}}
                    {{--<li class="site-menu-item">--}}
                        {{--<a href="{{ route('agent.test.manualdispatch') }}">--}}
                            {{--<i class="site-menu-icon fa fa-truck" aria-hidden="true"></i>--}}
                            {{--<span class="site-menu-title">@lang("$string_file.manual_dispatch")</span>--}}
                        {{--</a>--}}
                    {{--</li>--}}
                    {{--<li class="site-menu-item has-sub">--}}
                        {{--<a href="javascript:void(0)">--}}
                            {{--<i class="site-menu-icon fa-cab" aria-hidden="true"></i>--}}
                            {{--<span class="site-menu-title">@lang("$string_file.ride_management")</span>--}}
                            {{--<span class="site-menu-arrow"></span>--}}
                        {{--</a>--}}
                        {{--<ul class="site-menu-sub open">--}}
                            {{--<li class="site-menu-item">--}}
                                {{--<a class="animsition-link" href="{{ route('agent.activeride') }}">--}}
                                    {{--<span class="site-menu-title">@lang("$string_file.on_going_rides")</span>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li class="site-menu-item">--}}
                                {{--<a class="animsition-link" href="{{ route('agent.all.ride') }}">--}}
                                    {{--<span class="site-menu-title">@lang("$string_file.all_rides")</span>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li class="site-menu-item">--}}
                                {{--<a class="animsition-link" href="{{ route('agent.ratings') }}">--}}
                                    {{--<span class="site-menu-title">@lang("$string_file.reviews_and_symbol_ratings")</span>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                    <li class="site-menu-category">@lang("$string_file.driver")</li>
                    <li class="site-menu-item has-sub">
                        <a href="javascript:void(0)">
                            <i class="site-menu-icon fa-drivers-license" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.driver_management") </span>
                            <span class="site-menu-arrow"></span>
                        </a>
                        <ul class="site-menu-sub">
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{ route('agent.driver.index') }}">
                                    <span class="site-menu-title">@lang("$string_file.all_drivers")</span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="{{ route('agent.driver.allvehicles') }}">
                                    <span class="site-menu-title">@lang("$string_file.all_vehicles") </span>
                                </a>
                            </li>
                            {{--<li class="site-menu-item">--}}
                                {{--<a class="animsition-link" href="{{ route('account.index') }}">--}}
                                    {{--<span class="site-menu-title">@lang("$string_file.driver_account")</span>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                        </ul>
                    </li>
                    {{--<li class="site-menu-item has-sub">--}}
                        {{--<a href="javascript:void(0)">--}}
                            {{--<i class="site-menu-icon wb-user-circle" aria-hidden="true"></i>--}}
                            {{--<span class="site-menu-title">@lang("$string_file.driver_vehicles") </span>--}}
                            {{--<span class="site-menu-arrow"></span>--}}
                        {{--</a>--}}
                        {{--<ul class="site-menu-sub">--}}
                            {{--<li class="site-menu-item">--}}
                                {{--<a class="animsition-link" href="{{ route('agent.driver.allvehicles') }}">--}}
                                    {{--<span class="site-menu-title">@lang("$string_file.all_vehicles") </span>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                    {{--<li class="site-menu-category">@lang("$string_file.user") </li>--}}
                    {{--<li class="site-menu-item">--}}
                        {{--<a href="{{ route('agent.users.index') }}">--}}
                            {{--<i class="site-menu-icon wb-users" aria-hidden="true"></i>--}}
                            {{--<span class="site-menu-title">@lang("$string_file.user_management")</span>--}}
                        {{--</a>--}}
                    {{--</li>--}}
                    {{--<li class="site-menu-category">@lang("$string_file.others")</li>--}}
                    {{--<li class="site-menu-item">--}}
                        {{--<a href="{{ route('agent.transaction') }}">--}}
                            {{--<i class="site-menu-icon fa fa-exchange" aria-hidden="true"></i>--}}
                            {{--<span class="site-menu-title">@lang("$string_file.transaction_management")</span>--}}
                        {{--</a>--}}
                    {{--</li>--}}
                    {{--<li class="site-menu-item">--}}
                        {{--<a href="{{ route('agent.wallet') }}">--}}
                            {{--<i class="site-menu-icon fa fa-google-wallet" aria-hidden="true"></i>--}}
                            {{--<span class="site-menu-title">@lang("$string_file.wallet_transaction")</span>--}}
                        {{--</a>--}}
                    {{--</li>--}}
                </ul>
            </div>
        </div>
    </div>
    <div class="site-menubar-footer">
        <a href="{{ route('agent.profile') }}" style="width:50%" data-placement="top" data-toggle="tooltip"
           data-original-title="Update Profile">
            <span class="icon wb-user" aria-hidden="true"></span>
        </a>
        <a href="{{ route('agent.logout') }}" style="width:50%" data-placement="top" data-toggle="tooltip"
           data-original-title="Logout">
            <span class="icon wb-power" aria-hidden="true"></span>
        </a>

    </div>
</div>
