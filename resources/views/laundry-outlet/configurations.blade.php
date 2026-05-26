@extends('laundry-outlet.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
{{--                        <div class="btn-group float-right" >--}}
{{--                            <a href="{{ route('business-segment.option.index') }}">--}}
{{--                                <button type="button" class="btn btn-icon btn-success"style="margin:10px"><i--}}
{{--                                            class="wb-reply"></i>--}}
{{--                                </button>--}}
{{--                            </a>--}}
{{--                        </div>--}}
                    </div>
                    <h3 class="panel-title">
                        @php $heading = trans("$string_file.add"); @endphp
                       {{$heading}} @lang("$string_file.configuration")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'configurations','id'=>'option-form','files'=>true,'url'=>route('laundry-outlet.save-configurations'),'method'=>'POST'] ) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="optionname">
                                    @lang("$string_file.order_expire_time_minutes")
                                </label>
                                {!! Form::text('order_expire_time',old('name',!empty($config) ? $config->order_expire_time : NULL),['id'=>'','class'=>'form-control','placeholder'=>""]) !!}
                                @if ($errors->has('order_expire_time'))
                                    <label class="text-danger">{{ $errors->first('order_expire_time') }}</label>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="is_open">
                                    @lang("$string_file.is_open")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('is_open',$is_open,old('is_open',!empty($config) ? $config->is_open : NULL),['id'=>'is_open','class'=>'form-control','required'=>true]) !!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="estimate_process_days">
                                    @lang("$string_file.estimate") @lang("$string_file.processing") @lang("$string_file.days")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::number('estimate_process_days',old('estimate_process_days',!empty($config) ? $config->estimate_process_days : NULL),['id'=>'estimate_process_days','class'=>'form-control','required'=>true]) !!}
                            </div>
                        </div>


                    </div>   
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                        </button>
                    </div>
                    {!!  Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection