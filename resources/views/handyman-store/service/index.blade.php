@extends('handyman-store.layouts.main')
@section('content')
    <style>
    </style>
    <div class="page">
        <div class="page-content">
            @include("handyman-store.shared.errors-and-messages")
            @if(!empty($handyman_segment_services) || true)
                <div class="panel panel-bordered">
                    <header class="panel-heading">

                        <h3 class="panel-title">
                            <i class="fa fa-cog fa-spin" aria-hidden="true"></i>
                            @lang("$string_file.handyman") @lang("$string_file.services")</h3>
                    </header>
                    <div id="exampleTransition" class="page-content container-fluid" data-plugin="animateList">
                        <ul class="blocks-sm-100 blocks-xxl-3">
                            @foreach($handyman_segment_services as $services)
                                <li>
                                    <div class="panel panel-bordered" style="border: 1px solid #e4eaec;">
                                        <div class="panel-heading">
                                            <a href="">
                                                <h3 class="panel-title segment_class">
                                                    {!! $services['slag'] !!}
                                                </h3>
                                            </a>
                                            <div class="panel-actions">
                                                <img class="img-responsive" height="50px"
                                                     src="{!! $services['segment_icon'] !!}">
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <span style="font-size:20px;">{!! $services['name'] !!}</span>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                {{--                                            @if(Auth::user('merchant')->can('edit_segment'))--}}
                                                {{--                                                <a href="{{ route('merchant.segment.edit',$services['segment_id']) }}"--}}
                                                {{--                                                   class="panel-action" data-toggle="panel-close" aria-hidden="true"--}}
                                                {{--                                                   title="@lang("$string_file.edit")"><i class="fa-pencil"></i> </a>--}}
                                                {{--                                            @endif--}}
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            @if($services['segment_group_id'] == 2)
                                                <a href="{{ route('handyman-store.services.add',$services['segment_id']) }}"
                                                   class="panel-action float-right" data-toggle="panel-close"
                                                   aria-hidden="true" title="@lang('admin.add_service')"><i
                                                            class="fa-plus"></i> </a>
                                            @endif
                                            <div class="example table-responsive">
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>@lang("$string_file.type")</th>
                                                        <th>@lang("$string_file.service_type")</th>
                                                        <th>@lang("$string_file.description")</th>
                                                        <th>@lang("$string_file.sequence")</th>
                                                        <th>@lang("$string_file.icon")</th>
                                                        @if(Auth::user('merchant')->can('edit_service_types'))
                                                            @if($appConfig->show_recommended_services == 1)
                                                                <th>@lang("$string_file.recommended")</th>
                                                            @endif
                                                            <th>@lang("$string_file.action")</th>
                                                        @endif
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @php $i = 1; @endphp
                                                    @foreach($services['arr_services'] as $service)
                                                        <tr>
                                                            <td>{{$i}}</td>
                                                            <td>{!! $service['serviceName'] !!}</td>
                                                            <td>{!! $service['locale_service_name'] !!}</td>
                                                            <td>{!! $service['locale_service_description'] !!}</td>
                                                            <td>{!! $service['service_sequence'] !!}</td>
                                                            <td>
                                                                @if( $appConfig->show_recommended_services == 1)
                                                                    <img class="img-responsive" height="50px"
                                                                         width="50px"
                                                                         src="{!! $service['service_icon'] !!}">
                                                                @else
                                                                    ------
                                                                @endif
                                                            </td>
                                                            {{--                                                            @if(Auth::user('merchant')->can('edit_service_types'))--}}
                                                            @if($appConfig->show_recommended_services == 1)
                                                                <td>
                                                                    <div class="example">
                                                                        <div class="float-left mr-20">
                                                                            @php $status = $service['service_is_recommended'] == 1 ? 0 : 1; @endphp
                                                                            <input type="checkbox" class="changeStatus"
                                                                                   service-type-id="{{$service['id']}}"
                                                                                   change-status="{{$status}}"
                                                                                   id="is_recommended_status_{{$service['id']}}"
                                                                                   name="inputiCheckBasicCheckboxes"
                                                                                   data-plugin="switchery"
                                                                                   @if(isset($service['service_is_recommended']) && $service['service_is_recommended'] == 1) checked @endif />
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            @endif
                                                            <td>
                                                                <a href="{{ route('handyman-store.services.add',[$service['segment_id'],$service['id']]) }}"
                                                                   class="panel-action" data-toggle="panel-close"
                                                                   aria-hidden="true"
                                                                   title="@lang("$string_file.edit")"><i
                                                                            class="fa-pencil"
                                                                            style="padding-left: 19%;"></i> </a>
                                                            </td>
                                                            {{--                                                            @endif--}}
                                                            @php $i++; @endphp
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
@section('js')
{{--    <script>--}}
{{--        $(".changeStatus").change(function(){--}}
{{--            var service_id = $(this).attr("service-type-id");--}}
{{--            var change_status = $(this).attr("change-status");--}}
{{--            var url = "{{route("merchant.serviceType.changestatus")}}"+"/"+service_id+"/"+change_status;--}}
{{--            $.ajax({--}}
{{--                type: "GET",--}}
{{--                url: url,--}}
{{--            }).done(function (data) {--}}
{{--                swal({--}}
{{--                    title: "Status Updated!",--}}
{{--                    text: data,--}}
{{--                    type: "success",--}}
{{--                });--}}
{{--                window.location.href = "{{ route('merchant.serviceType.index') }}";--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
@endsection
