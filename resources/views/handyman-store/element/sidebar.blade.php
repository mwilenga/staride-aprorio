<div class="site-menubar">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu">
                    <li class="site-menu-category">@lang("$string_file.general")</li>
                    <li class="site-menu-item">
                        <a href="{{ route('handyman-store.dashboard') }}">
                            <i class="site-menu-icon wb-dashboard" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.dashboard")</span>
                        </a>
                    </li>
                    <li class="site-menu-category">@lang("$string_file.service_management")</li>
                    <li class="site-menu-item">
                        <a href="{{ route('services.index') }}">
                            <i class="site-menu-icon fa fa-th-list" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.services")</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('handyman-store.segment.price_card') }}">
                            <i class="site-menu-icon fa fa-money" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.price_card")</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('handyman-store.segment.commission') }}">
                            <i class="site-menu-icon fa fa-money" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.commission")</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('handyman-store.advertisement.index') }}">
                            <i class="site-menu-icon fa fa-buysellads" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.banner")  @lang("$string_file.management")</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('handyman-store.segment.handyman-category') }}">
                            <i class="site-menu-icon fa fa-caret-square-o-left" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.categories") </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('handyman-store.segment.service-time-slot') }}">
                            <i class="site-menu-icon fa fa-caret-square-o-left" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.service_time_slots") </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a href="{{ route('handyman-store.promocode.index') }}">
                            <i class="site-menu-icon fa fa-caret-square-o-left" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.promo_code") </span>
                        </a>
                    </li>
                    <li class="site-menu-category">@lang("$string_file.booking") @lang("$string_file.management")</li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('handyman-store.new.orders') }}">
                            <i class="site-menu-icon fa fa-id-card" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.new_orders")</span>
                        </a>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('handyman-store.ongoing.orders') }}">
                            <i class="site-menu-icon fa fa-id-card" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.ongoing_orders")</span>
                        </a>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('handyman-store.completed.orders') }}">
                            <i class="site-menu-icon fa fa-id-card" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.completed_orders")</span>
                        </a>
                    </li>
                    <li class="site-menu-category">@lang("$string_file.driver") @lang("$string_file.management")</li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('handyman-store.driver.index') }}">
                            <i class="site-menu-icon fa fa-id-card" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.all_driver")</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('handyman-store.driver.pending.show') }}">
                            <i class="site-menu-icon fa fa-id-card" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.pending_approval")</span>
                        </a>
                    </li>
                    <li class="site-menu-category">@lang("$string_file.transactions")</li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('handyman-store.wallet') }}">
                            <i class="site-menu-icon fa fa-id-card" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.wallet")</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('handyman-store.cashouts') }}">
                            <i class="site-menu-icon fa fa-id-card" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.cashout")</span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="{{ route('handyman-store-services-report') }}">
                            <i class="site-menu-icon fa fa-id-card" aria-hidden="true"></i>
                            <span class="site-menu-title">@lang("$string_file.earning")</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="site-menubar-footer">
        <a href="{{ route('corporate.profile') }}" style="width:50%" data-placement="top" data-toggle="tooltip" data-original-title="Update Profile">
            <span class="icon wb-user" aria-hidden="true"></span>
        </a>
        <a href="{{ route('corporate.logout') }}" style="width:50%" data-placement="top" data-toggle="tooltip" data-original-title="Logout">
            <span class="icon wb-power" aria-hidden="true"></span>
        </a>
    </div>
</div>
<div class="site-gridmenu">
    <div>
        <div>
            <ul><!--[if lt IE 8]> -->
                <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
            </ul>
        </div>
    </div>
</div>