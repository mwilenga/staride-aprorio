@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('business-segment.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                            <a href="{{route('product-availability-time-slabs.edit')}}">
                                <button type="button" class="btn btn-icon btn-warning float-right" style="margin:10px">
                                    <i class="wb-edit"
                                       title="@lang("$string_file.edit")"></i>
                                </button>
                            </a>
                        <a href="{{route('product-availability-time-slabs.add')}}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-plus"
                                   title=" @lang("$string_file.new") @lang("$string_file.product_availability_time_slabs")"></i>
                            </button>
                        </a>

                    </div>
                    <h3 class="panel-title"><i class="icon fa-money" aria-hidden="true"></i>
                        @lang("$string_file.product_availability_time_slabs")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
{{--                    @include('merchant.segment-pricecard.search')--}}
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.image")</th>
                            <th>@lang("$string_file.start_time")</th>
                            <th>@lang("$string_file.end_time")</th>
                            <th>@lang("$string_file.priority")</th>
                            <th>@lang("$string_file.custom")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $slabs->firstItem();
                        @endphp
                        @foreach($slabs as $slab)
                            @php
                                $start = isset($slab->start_time) ? strtotime($slab->start_time) : null;
                                $end = isset($slab->end_time) ? strtotime($slab->end_time) : null;
                            @endphp
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{$slab->name}}</td>
                                <td>
                                    <image src="{{ get_image($slab->image, "product_availability_slab_image", $slab->merchant_id) }}" width=75 height=75>
                                </td>
                                <td>
                                    @if(isset($start))   
                                        {{ $time_format == 2 ? date("H:i", $start) : date("h:i a", $start) }}
                                    @endif
                                </td>
                                <td>
                                    @if(isset($end))   
                                        {{ $time_format == 2 ? date("H:i", $end) : date("h:i a", $end) }}</td>
                                    @endif
                                <td>{{$slab->priority}}</td>
                                <td>
                                    @if($slab->is_custom)
                                        @lang("$string_file.yes")
                                    @else
                                        @lang("$string_file.no")
                                    @endif
                                </td>
                                <td>
                                    @if($slab->status == 0)
                                        <span class="badge badge-warning">@lang("$string_file.price_not_set")</span>
                                    @elseif($slab->status == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>


                                    <a href="{{ route('product-availability-time-slabs.delete',$slab->id) }}"
                                       data-original-title="@lang("$string_file.delete")"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-danger menu-icon btn_edit action_btn">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
{{--                    @include('merchant.shared.table-footer', ['table_data' => $arr_service_time_slot, 'data' => []])--}}
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection

