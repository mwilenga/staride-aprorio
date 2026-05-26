@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title">
                        <i class=" icon wb-shopping-cart" aria-hidden="true"></i>
                        @lang("$string_file.product_details") </h3>
                </header>
                <div class="panel-body">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable2"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th> @lang("$string_file.variant_id")</th>
                            <th>@lang("$string_file.sku_no")</th>
                            <th>@lang("$string_file.business_segment")</th>
                            <th>@lang("$string_file.title")</th>
                            <th>@lang("$string_file.price")</th>
                            <th>@lang("$string_file.discount")</th>
                            <th data-name="weight_unit">@lang("$string_file.weight_unit") </th>
                            <th data-name="weight_unit_value" data-visible="false">@lang("$string_file.weight_unit") </th>
                            <th data-name="weight">@lang("$string_file.weight")</th>
                            <th data-name="is_title_show">@lang("$string_file.is_title_show")</th>
                            <th data-name="is_title_show_value" data-visible="false">@lang("$string_file.is_title_show")</th>
                            <th data-name="status">@lang("$string_file.status")</th>
                            <th data-name="status_value" data-visible="false">@lang("$string_file.status")</th>
                            <th data-name="current_stock">@lang("$string_file.current_stock")</th> 
                            <th>@lang("$string_file.product_cost")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($product_variant_list as $product_variant)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $product_variant->id}}</td>
                                <td>{{ $product_variant->sku_id}}</td>
                                <td>{{ $product_variant->Product->BusinessSegment->full_name}}</td>
                                <td>{{  $product_variant->is_title_show == 1 ? $product_variant->Name($merchant_id) : $product_variant->Product->Name($merchant_id)}}</td>
                                <td>{{ custom_number_format($product_variant->product_price,$trip_calculation_method) }}</td>
                                <td>{{ custom_number_format($product_variant->discount,$trip_calculation_method) }}</td>
                                <td>@if(!empty($product_variant->weight_unit_id)){{$product_variant->WeightUnit->WeightUnitName}}@endif</td>
                                <td>{{$product_variant->weight_unit_id}}</td>
                                <td>{{ $product_variant->weight }}</td>
                                <td>@if($product_variant->is_title_show == 1)
                                        <span class="badge badge-success">@lang("$string_file.yes")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.no")</span>
                                    @endif
                                </td>
                                <td>{{$product_variant->is_title_show}}</td>
                                <td>@if($product_variant->status == 1)
                                        <span class="badge badge-success">{{$product_status[$product_variant->status]}}</span>
                                    @else
                                        <span class="badge badge-danger">{{$product_status[$product_variant->status]}}</span>
                                    @endif
                                </td>
                                <td>{{$product_variant->status}}</td>
                                <td>{{$product_variant->ProductInventory->current_stock}}</td>
                                <td>{{$product_variant->ProductInventory->product_cost}}</td>
                                <td>
                                    <a href="{!! route('business-segment.warehouse.add',$product_variant->id) !!}"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn"
                                       title="@lang(" $string_file.edit")"><i class="wb-edit"></i></a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection