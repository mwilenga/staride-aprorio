@extends('laundry-outlet.layouts.main')
@section('content')
    @php $images_required = true; $id = NULL;
    $sub_cat_optional =  true;
    @endphp
    @if(!empty($data['service']['id']))
        @php $lang_data = $data['service']->langData($data['service']['merchant_id']); @endphp
        @php $images_required = false; $id = $data['service']['id'];@endphp
    @endif
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('laundry-outlet.services.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" fa fa-building" aria-hidden="true"></i>
                        @lang("$string_file.service")

                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'service_form','id'=>'service-form','files'=>true,'url'=>$data['save_url'],'method'=>'POST'] ) !!}
                    {!! Form::hidden('id',$id) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sid">
                                    @lang("$string_file.sid")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::text('sid',old('sid',isset( $data ['service']['sid']) ? $data['service']['sid'] : $data['sid']),['id'=>'','class'=>'form-control','required'=>true,'autocomplete'=>'off', 'readonly'=>'true']) !!}
                                @if ($errors->has('sid'))
                                    <label class="text-danger">{{ $errors->first('sid') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="service_name">
                                    @lang("$string_file.service_name")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::text('service_name',old('service_name',isset($lang_data->name) ? $lang_data->name : NULL),['id'=>'','class'=>'form-control','required'=>true,'autocomplete'=>'off']) !!}
                                @if ($errors->has('service_name'))
                                    <label class="text-danger">{{ $errors->first('service_name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    @lang("$string_file.status")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('status',$data['service_status'],old('status',isset($data['service']['status']) ? $data['service']['status'] : NULL),['id'=>'','class'=>'form-control','required'=>true]) !!}
                                @if ($errors->has('status'))
                                    <label class="text-danger">{{ $errors->first('status') }}</label>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    @lang("$string_file.is_display_on_home_screen")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('display_type',get_status(true,$string_file),old('display_type',isset($data['service']['display_type']) ? $data['service']['display_type'] : NULL),['id'=>'','class'=>'form-control']) !!}
                                @if ($errors->has('display_type'))
                                    <label class="text-danger">{{ $errors->first('display_type') }}</label>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="service_description">
                                    @lang("$string_file.description")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::textarea('service_description',old('service_description',isset($lang_data->description) ? $lang_data->description : NULL),['id'=>'','class'=>'form-control','required'=>true ,'cols'=>3,'rows'=>2]) !!}
                                @if ($errors->has('service_description'))
                                    <label class="text-danger">{{ $errors->first('service_description') }}</label>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="price">
                                    @lang("$string_file.price")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::number('price',old('price',isset($data['service']['price']) ? $data['service']['price'] : NULL),['id'=>'','class'=>'form-control','required'=>true,'autocomplete'=>'off', 'step'=>"0.1"]) !!}
                                @if ($errors->has('price'))
                                    <label class="text-danger">{{ $errors->first('price') }}</label>
                                @endif
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="category_id">
                                    @lang("$string_file.category")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('category_id',add_blank_option($data['arr_category'],trans("$string_file.select")),old('category_id ',isset($data['service']['category_id']) ? $data['service']['category_id'] : NULL),['id'=>'category_id','class'=>'select2 form-control','required'=>true,'autocomplete'=>'off']) !!}
                                @if ($errors->has('category_id'))
                                    <label class="text-danger">{{ $errors->first('category_id') }}</label>
                                @endif
                            </div>
                        </div>

                        @php $sub_category_required = false; /*sub category id is optional in all cases*/
                        @endphp
                        {{--                        @if($data['segment']->Segment->slag != 'FOOD')--}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sub_category_id">
                                    @lang("$string_file.sub_category")
                                </label>
                                {!! Form::select('sub_category_id',add_blank_option($data['sub_category'],trans("$string_file.select")),old('sub_category_id ',isset($data['service']['sub_category_id']) ? $data['service']['sub_category_id'] : NULL),['id'=>'sub_category_id','class'=>'form-control','autocomplete'=>'off','required'=>$sub_cat_optional == 1 ? false : true]) !!}
                                @if ($errors->has('sub_category_id'))
                                    <label class="text-danger">{{ $errors->first('sub_category_id') }}</label>
                                @endif
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sequence">
                                    @lang("$string_file.sequence")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::number('sequence',old('sequence',isset( $data ['service']['sequence']) ? $data['service']['sequence'] : NULL),['id'=>'','class'=>'form-control','required'=>true,'autocomplete'=>'off']) !!}
                                @if ($errors->has('sequence'))
                                    <label class="text-danger">{{ $errors->first('sequence') }}</label>
                                @endif
                            </div>
                        </div>


                        <div class="col-md-4">
                            <label for="service_image">
                                @lang("$string_file.service") @lang("$string_file.list") @lang("$string_file.image")
                                (W:{{ $data['arr_size']['service_image']['width']  }} *
                                H:{{ $data['arr_size']['service_image']['height'] }})

                            </label>
                            @if(!empty($data['service']['id']))
                                <a href="{{ get_image($data['service']['service_image'],'laundry_service_image',$data['service']['merchant_id']) }}"
                                   target="_blank">@lang("$string_file.view")</a>
                            @endif
                            <div class="input-group form-group decrement">
                                {!! Form::file('service_image',['id'=>'service_image','class'=>'form-control']) !!}
                                @if ($errors->has('service_image'))
                                    <label class="text-danger">{{ $errors->first('service_image') }}</label>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="form-group  ">
                                <label for="service_cover_image">
                                    @lang("$string_file.service") @lang("$string_file.banner") @lang("$string_file.image")
                                    (W:{{  $data['arr_size']['service']['width']  }} *
                                    H:{{  $data['arr_size']['service']['height']  }})
                                </label>
                                @if(!empty($data['service']['id']))
                                    <a href="{{ get_image($data['service']['service_cover_image'],'laundry_service_cover_image',$data['service']['merchant_id']) }}"
                                       target="_blank">@lang("$string_file.view")</a>
                                @endif
                                {!! Form::file('service_cover_image',['id'=>'service_cover_image','class'=>'form-control']) !!} {{-- ,'required'=>$images_required --}}
                                @if ($errors->has('service_cover_image'))
                                    <label class="text-danger">{{ $errors->first('service_cover_image') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        @if($id == NULL || $edit_permission )
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i>
                                @if($data ['service'] == null)
                                    @lang("$string_file.save")
                                @else
                                    @lang("$string_file.save")
                                @endif
                            </button>
                        @else
                            <span style="color: red"
                                  class="float-right">@lang("$string_file.demo_warning_message")</span>
                        @endif
                    </div>
                    {!!  Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('#category_id').change(function () {
                $.ajax({
                    url: "{{ route('laundry-outlet.get.subcategory') }}",
                    type: "GET",
                    data: {id: $(this).val()},
                    dataType: "JSON",
                    success: function (result) {
                        $("#sub_category_id").empty();
                        $.each(result, function (key, value) {
                            $("#sub_category_id").append("<option value='" + key + "'>" + value + "</option>");
                        });
                    }
                });
            });
        });
    </script>
@endsection
