@extends('handyman-store.layouts.main')
@section('content')
<div class="page">
    <div class="page-content">
        @include('handyman-store.shared.errors-and-messages')
        <div class="panel panel-bordered">
            <header class="panel-heading">
                <div class="panel-actions">
                    @if(!empty($info_setting) && $info_setting->add_text != "")
                    <button class="btn btn-icon btn-primary float-right" style="margin:10px" data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                        <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                    </button>
                    @endif
                    <div class="btn-group float-right" style="margin:10px">
                        <a href="{{ route('promocode.index') }}">
                            <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                </div>
                <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                    @lang("$string_file.promo_code")
                </h3>
            </header>
            @php $id = isset($promocode->id) ? $promocode->id : NULL;@endphp
            <div class="panel-body container-fluid">
                <section id="validation">
                    <form method="POST" class="steps-validation wizard-notification" id="promocode-form" name="promocode-form" enctype="multipart/form-data" action="{{ route('handyman-store.promocode.store',$id) }}">
                        @csrf
                        {!! Form::hidden('id',$id) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.segment")
                                        <span class="text-danger">*</span>
                                    </label>
                                    @if(!empty($id))
                                    {!! Form::text('seg_id',($promocode->segment_id != NULL) ? $segment_list[$promocode->segment_id] : "---",['class'=>"form-control",'disabled'=>true]) !!}
                                    {!! Form::hidden('segment_id',$promocode->segment_id,[]) !!}
                                    @else
                                    {!! Form::select('segment_id',add_blank_option($segment_list,trans("$string_file.select")),old('segment_id'),array('class' => 'form-control','required'=>true,'id'=>'segment_id')) !!}
                                    @if ($errors->has('segment_id'))
                                    <label class="text-danger">{{ $errors->first('segment_id') }}</label>
                                    @endif
                                    @endif
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.promo_code")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="promocode" name="promocode" placeholder="FIRSTDELFREE" value="{{ old('promocode',isset($promocode->promoCode) ? $promocode->promoCode : NULL) }}" required>
                                    @if ($errors->has('promocode'))
                                    <label class="text-danger">{{ $errors->first('promocode') }}</label>
                                    @endif
                                </div>
                            </div>
                            <!-- </div>
                        <div class="row"> -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.type")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="promo_code_value_type" id="promo_code_value_type" onchange="changeText(this.value)" required>
                                        <option id="1" value="1" @if(!empty($id) && $promocode->promo_code_value_type == 1) selected @endif> @lang("$string_file.flat")</option>
                                        <option id="2" value="2" @if(!empty($id) && $promocode->promo_code_value_type == 2) selected @endif> @lang("$string_file.percentage")</option>
                                    </select>
                                    @if ($errors->has('promo_code_value_type'))
                                    <label class="text-danger">{{ $errors->first('promo_code_value_type') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.discount")<span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step=0.01 min=0 class="form-control" id="promo_code_value" name="promo_code_value" placeholder="" value="{{ old('promo_code_value',isset($promocode->promo_code_value) ? $promocode->promo_code_value : NULL) }}" required>
                                    @if ($errors->has('promo_code_value'))
                                    <label class="text-danger">{{ $errors->first('promo_code_value') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.description")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="promo_code_description" name="promo_code_description" placeholder="" required>{{ old('promo_code_description',isset($promocode->promo_code_description) ? $promocode->promo_code_description : "") }}</textarea>
                                    @if ($errors->has('promo_code_description'))
                                    <label class="text-danger">{{ $errors->first('promo_code_description') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.validity")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="icheckbox_minimal checked hover active" style="position: relative;">
                                        <input type="radio" id="promo_code_validity_permanent" value="1" name="promo_code_validity" onclick="javascript:yesnoCheck()" checked @if(!empty($id) && $promocode->promo_code_validity == 1) checked @endif>
                                        <label for="promo_code_validity_permanent" class="">@lang("$string_file.permanent")</label>
                                        <input type="radio" id="promo_code_validity_custom" value="2" name="promo_code_validity" onclick="javascript:yesnoCheck()" style="margin-left: 20px;" @if(!empty($id) && $promocode->promo_code_validity == 2) checked @endif>
                                        <label for="promo_code_validity_custom" class="">@lang("$string_file.custom")</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group custom-hidden" id="start-div">
                                    <label for="emailAddress5">
                                        @lang("$string_file.start_date")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control customDatePicker1" name="start_date" placeholder="" value="{{ old('start_date', isset($promocode->start_date) ? $promocode->start_date : NULL) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group @if(empty($id) || (!empty($id) && $promocode->promo_code_validity == 1)) custom-hidden @endif" id="end-div">
                                    <label for="emailAddress5">
                                        @lang("$string_file.end_date")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control customDatePicker1" name="end_date" placeholder="" value="{{ old('end_date', isset($promocode->end_date) ? $promocode->end_date : NULL) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.limit")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="promo_code_limit" name="promo_code_limit" placeholder="" value="{{ old('promo_code_limit',isset($promocode->promo_code_limit) ? $promocode->promo_code_limit : NULL) }}" required>
                                    @if ($errors->has('promo_code_limit'))
                                    <label class="text-danger">{{ $errors->first('promo_code_limit') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.limit_per_user")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="promo_code_limit_per_user" name="promo_code_limit_per_user" placeholder="" value="{{ old('promo_code_limit_per_user',isset($promocode->promo_code_limit_per_user) ? $promocode->promo_code_limit_per_user : NULL) }}" required>
                                    @if ($errors->has('promo_code_limit_per_user'))
                                    <label class="text-danger">{{ $errors->first('promo_code_limit_per_user') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.minimum_bill_amount")
                                        {{-- <span class="text-danger">*</span>--}}
                                    </label>
                                    <input type="number" class="form-control" id="order_minimum_amount" name="order_minimum_amount" placeholder="" value="{{ old('order_minimum_amount',isset($promocode->order_minimum_amount) ? $promocode->order_minimum_amount : NULL) }}">
                                    @if ($errors->has('order_minimum_amount'))
                                    <label class="text-danger">{{ $errors->first('order_minimum_amount') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.promo_percentage_maximum_discount")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="promo_percentage_maximum_discount" name="promo_percentage_maximum_discount" placeholder="" value="{{old('promo_percentage_maximum_discount',isset($promocode->promo_percentage_maximum_discount) ? $promocode->promo_percentage_maximum_discount:NULL) }}" disabled>
                                    @if ($errors->has('promo_percentage_maximum_discount'))
                                    <label class="text-danger">{{ $errors->first('promo_percentage_maximum_discount') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.promo_code_parameter")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="promo_code_name" name="promo_code_name" placeholder="" value="{{ old('promo_code_name',!empty($id) ? $promocode->PromoName : NULL) }}" required>
                                    @if ($errors->has('promo_code_name'))
                                    <label class="text-danger">{{ $errors->first('promo_code_name') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            @if($id == NULL || $edit_permission)
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-check-circle" onclick="return Validate()"></i>
                                @lang("$string_file.save")
                            </button>
                            @endif
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</div>
@include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
<script>
    function Validate() {
        var promo_code_value_type = document.getElementById('promo_code_value_type').value;
        var promo_code_value = document.getElementById('promo_code_value').value;
        if (promo_code_value_type == 2 && promo_code_value > 100) {
            alert('Enter Value Less Then 100');
            return false;
        }
    }

    function changeText(val) {
        let firstmsg = "";
        let firstmsg2 = "";
        if (val == "2") {
            $('#promo_percentage_maximum_discount').prop("disabled", false);
            $('#promo_code_value').attr("placeholder", firstmsg2);
        } else {
            $('#promo_percentage_maximum_discount').prop("disabled", true);
            $('#promo_code_value').attr("placeholder", firstmsg);
        }
    }

    function UserType(val) {
        if (val == "3") {
            document.getElementById('corporate_div').style.display = 'block';
        } else {
            document.getElementById('corporate_div').style.display = 'none';
        }
    }

    function yesnoCheck() {
        if (document.getElementById('promo_code_validity_permanent').checked) {
            document.getElementById('start-div').style.display = 'none';
            document.getElementById('end-div').style.display = 'none';
        } else {
            document.getElementById('start-div').style.display = 'block';
            document.getElementById('end-div').style.display = 'block';
        }
    }



</script>
@endsection
