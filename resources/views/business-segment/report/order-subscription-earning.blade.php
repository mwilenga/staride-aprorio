@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    
                    <h3 class="panel-title"><i class="wb-copy" aria-hidden="true"></i>
                        @lang("$string_file.membership_order_earning")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                            <tr>
                                <th>@lang("$string_file.sn")</th>
                                <th>@lang("$string_file.order_id")</th>
                                <th>@lang("$string_file.user_detail")</th>
                                <th>@lang("$string_file.driver_detail")</th>
                                <th>@lang("$string_file.amount")</th>
                                <th>@lang("$string_file.created_at")</th>
                            </tr>
                        </thead>
                        <tbody>
                                @php $serial = 1; @endphp
                                @foreach($subscriptions as $start_date => $subscriptionGroup)
                                    @if(count($subscriptionGroup->skip(1)) > 0)
                                    @foreach($subscriptionGroup->skip(1) as $order)
                                        <tr>
                                            <td>{{ $order->order_id }}</td>
                                            <td>{{ $order->Order->User->first_name.' '.$order->Order->User->last_name}}</td>
                                            <td>{{ $order->Order->Driver->first_name.' '.$order->Order->Driver->last_name}}</td>
                                            <td>{{ $order->store_earning ?? '' }}</td>
                                            <td>{{ $order->Order->created_at }}</td>
                                        </tr>
                                    @endforeach
                                    @endif
                                    @php $serial++; @endphp
                                @endforeach
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>[],'page_name'=>'view_text'])
@endsection