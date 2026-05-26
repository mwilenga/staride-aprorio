@extends('laundry-outlet.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">

            </div>
        </div>
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('laundry-outlet.service.add')}}">
                            <button type="button" title="@lang(" $string_file.add") @lang(" $string_file.service") "
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-plus"></i>
                            </button>
                        </a>

                    </div>
                    <h3 class="panel-title">
                        <i class=" icon wb-shopping-cart" aria-hidden="true"></i>@lang("$string_file.laundry_services")
                    </h3>
                </header>
                <div class="panel-body">
                    {!! $search_view !!}
                    <table class="display nowrap table table-hover table-striped w-full" id=""
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.sid")</th>
                            <th>@lang("$string_file.service") @lang("$string_file.name")</th>
                            <th>@lang("$string_file.description")</th>
                            <th>@lang("$string_file.category")</th>
                            <th>@lang("$string_file.sequence")</th>
                            <th>@lang("$string_file.service") @lang("$string_file.image")</th>
                            <th>@lang("$string_file.service") @lang("$string_file.cover")  @lang("$string_file.image")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1; @endphp
                        @foreach($data as $laundry)

                            <tr>
                                @php $lang_data = $laundry->langData($laundry->merchant_id); @endphp
                                <td>{{ $sr }}</td>
                                <td>{{ $laundry->sid }}</td>
                                <td>{{ $lang_data->name}}  </td>
                                <td>{{ $lang_data->description }}</td>
                                <td>{{$laundry->Category->Name($laundry->merchant_id)}}</td>
                                <td>{{$laundry->sequence}}</td>
                                <td>
                                    <img src="{{ get_image($laundry->service_image,'laundry_service_image',$laundry->merchant_id)}}"
                                         width="80px" height="80px">
                                </td>
                                <td>
                                    <img src="{{ get_image($laundry->service_cover_image,'laundry_service_cover_image',$laundry->merchant_id)}}"
                                         width="80px" height="80px">
                                </td>
                                <td>@if($laundry->status == 1)
                                        <span class="badge badge-success">{{isset($service_status[$laundry->status]) ? $service_status[$laundry->status] : ""}}</span>
                                    @else
                                        <span class="badge badge-danger">{{isset($service_status[$laundry->status]) ? $service_status[$laundry->status] : ""}}</span>
                                    @endif
                                </td>


                                <td>
                                    <a href="{!! route('laundry-outlet.service.add',$laundry->id) !!}"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn"
                                       title="@lang(" $string_file.edit")"><i class="wb-edit"></i></a>

                                    <button onclick="DeleteEvent({{ $laundry->id }})" type="button"
                                            data-original-title="@lang(" $string_file.delete")" data-toggle="tooltip"
                                            data-placement="top"
                                            class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                        <i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                            @php $sr++ @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $data, 'data' => $arr_search])
                </div>
            </div>
        </div>
    </div>

{{--    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])--}}
@endsection
@section('js')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function DeleteEvent(id = null) {
            var token = $('[name="_token"]').val();
            swal({
                title: '{{trans("$string_file.are_you_sure")}}',
                text: '{{trans("$string_file.delete_warning")}}',
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': "{{csrf_token()}}"
                        },
                        type: "POST",
                        data: {
                            id: id,
                        },
                        url: "{{route('laundry-outlet.service.destroy')}}",
                    })
                        .done(function (data) {
                            swal({
                                title: "DELETED!",
                                text: data,
                                type: "success",
                            });
                            window.location.href = "{{ route('laundry-outlet.services.index') }}";
                        });
                } else {
                    swal('{{trans("$string_file.data_is_safe")}}');
                }
            });
        };
    </script>
@endsection
