@extends('business-segment.layouts.main')
@section('content')
    @php $images_required = true; $id = NULL; $sub_cat_optional =  $data['segment']->Merchant->configuration->bussiness_seg_sub_cat_optional;
    $enable_auto_generate_sku =  $data['segment']->Merchant->configuration->enable_sku_auto_generate;
    $disabled_sku = $enable_auto_generate_sku == 1 ? true : false;
    @endphp
    @if(!empty($data['product']['id']))
        @php $lang_data = $data['product']->langData($data['product']['merchant_id']); @endphp
        @php $images_required = false; $id = $data['product']['id'];@endphp
    @endif
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('business-segment.product.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" fa fa-building" aria-hidden="true"></i>
                        @lang("$string_file.product")

                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'product','id'=>'product-form','files'=>true,'url'=>$data['save_url'],'method'=>'POST'] ) !!}
                    {!! Form::hidden('id',$id) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sku_id">
                                    @lang("$string_file.sku_no")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::text('sku_id',old('sku_id',isset( $data ['product']['sku_id']) ? $data['product']['sku_id'] : $data['sku_id']),['id'=>'','class'=>'form-control','required'=>true,'autocomplete'=>'off', 'readonly'=>$disabled_sku]) !!}
                                @if ($errors->has('sku_id'))
                                    <label class="text-danger">{{ $errors->first('sku_id') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="product_name">
                                    @lang("$string_file.product_name")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::text('product_name',old('product_name',isset($lang_data->name) ? $lang_data->name : NULL),['id'=>'','class'=>'form-control','required'=>true,'autocomplete'=>'off']) !!}
                                @if ($errors->has('product_name'))
                                    <label class="text-danger">{{ $errors->first('product_name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    @lang("$string_file.status")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('status',$data['product_status'],old('status',isset($data['product']['status']) ? $data['product']['status'] : NULL),['id'=>'','class'=>'form-control','required'=>true]) !!}
                                @if ($errors->has('status'))
                                    <label class="text-danger">{{ $errors->first('status') }}</label>
                                @endif
                            </div>
                        </div>
                        @if(isset($data['segment']->Segment->slag) && $data['segment']->Segment->slag != 'FOOD')
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    @lang("$string_file.is_display_on_home_screen")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('display_type',get_status(true,$string_file),old('display_type',isset($data['product']['display_type']) ? $data['product']['display_type'] : NULL),['id'=>'','class'=>'form-control']) !!}
                                @if ($errors->has('display_type'))
                                    <label class="text-danger">{{ $errors->first('display_type') }}</label>
                                @endif
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="product_description">
                                    @lang("$string_file.description")
                                    <span class="text-danger"></span>
                                </label>
                                {!! Form::textarea('product_description',old('product_description',isset($lang_data->description) ? $lang_data->description : NULL),['id'=>'','class'=>'form-control','cols'=>2,'rows'=>2,'maxlength'=>200]) !!}
                                @if ($errors->has('product_description'))
                                    <label class="text-danger">{{ $errors->first('product_description') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="product_ingredients">
                                    @lang("$string_file.ingredients")
                                </label>
                                {!! Form::textarea('product_ingredients',old('product_ingredients',isset($lang_data->ingredients) ? $lang_data->ingredients : NULL),['id'=>'','class'=>'form-control','cols'=>2,'rows'=>2,'maxlength'=>200]) !!}
                                @if ($errors->has('product_ingredients'))
                                    <label class="text-danger">{{ $errors->first('product_ingredients') }}</label>
                                @endif
                            </div>
                        </div>
                        @if(isset($data['segment']->Segment->slag) && $data['segment']->Segment->slag == 'FOOD')
                            <div class="col-md-4">
                                <label for="product_preparation_time">
                                    @lang("$string_file.preparation_time") (@lang("$string_file.in_minutes"))
                                </label>
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="wb-time" aria-hidden="true"></i></span>
                                    {!! Form::number('product_preparation_time',old('product_preparation_time',isset($data['product']['product_preparation_time']) ? $data['product']['product_preparation_time'] : NULL),['class'=>'form-control','id'=>'time','autocomplete'=>'off','min'=>0,'required','max'=>99999999]) !!}
                                </div>
                                @if ($errors->has('product_preparation_time'))
                                    <label class="text-danger">{{ $errors->first('product_preparation_time') }}</label>
                                @endif
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="category_id">
                                    @lang("$string_file.category")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('category_id',add_blank_option($data['arr_category'],trans("$string_file.select")),old('category_id ',isset($data['product']['category_id']) ? $data['product']['category_id'] : NULL),['id'=>'category_id','class'=>'select2 form-control','required'=>true,'autocomplete'=>'off']) !!}
                                @if ($errors->has('category_id'))
                                    <label class="text-danger">{{ $errors->first('category_id') }}</label>
                                @endif
                            </div>
                        </div>

                        @php $sub_category_required = false; /*sub category id is optional in all cases*/
                        /*$data['segment']->Segment->slag != 'FOOD' ? true : false*/
                        @endphp
{{--                        @if($data['segment']->Segment->slag != 'FOOD')--}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sub_category_id">
                                    @lang("$string_file.sub_category")
                                </label>
                                {!! Form::select('sub_category_id',add_blank_option($data['sub_category'],trans("$string_file.select")),old('sub_category_id ',isset($data['product']['sub_category_id']) ? $data['product']['sub_category_id'] : NULL),['id'=>'sub_category_id','class'=>'form-control','autocomplete'=>'off','required'=>$sub_cat_optional == 1 ? false : true]) !!}
                                @if ($errors->has('sub_category_id'))
                                    <label class="text-danger">{{ $errors->first('sub_category_id') }}</label>
                                @endif
                            </div>
                        </div>

                        @if(isset($data['arr_brands']) && !empty($data['arr_brands']))
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="brand_id">
                                    @lang("$string_file.brand")
                                </label>
                                {!! Form::select('brand_id',add_blank_option($data['arr_brands'],trans("$string_file.select")),old('brand_id ',isset($data['product']['brand_id']) ? $data['product']['brand_id'] : NULL),['id'=>'brand_id','class'=>'form-control','autocomplete'=>'off']) !!}
                                @if ($errors->has('brand_id'))
                                    <label class="text-danger">{{ $errors->first('brand_id') }}</label>
                                @endif
                            </div>
                        </div>
                        @endif

{{--                        @endif--}}
                        @if(isset($data['segment']->Segment->slag) && in_array($data['segment']->Segment->slag,array('FOOD','GROCERY')))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="food_type">
                                        @lang("$string_file.type")
{{--                                        <span class="text-danger">*</span>--}}
                                    </label>
                                    {!! Form::select('food_type',add_blank_option($data['arr_food_type'],trans("$string_file.select")),old('food_type ',isset($data['product']['food_type']) ? $data['product']['food_type'] : NULL),['id'=>'food_type','class'=>'form-control','autocomplete'=>'off']) !!}
                                    @if ($errors->has('food_type'))
                                        <label class="text-danger">{{ $errors->first('food_type') }}</label>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sequence">
                                    @lang("$string_file.sequence")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::number('sequence',old('sequence',isset( $data ['product']['sequence']) ? $data['product']['sequence'] : NULL),['id'=>'','class'=>'form-control','required'=>true,'autocomplete'=>'off','min'=> 0]) !!}
                                @if ($errors->has('sequence'))
                                    <label class="text-danger">{{ $errors->first('sequence') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    @lang("$string_file.do_you_want_to_manage_inventory")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('manage_inventory',get_status(true,$string_file),old('manage_inventory',isset($data['product']['manage_inventory']) ? $data['product']['manage_inventory'] : 2),['id'=>'','class'=>'form-control']) !!}
                                @if ($errors->has('manage_inventory'))
                                    <label class="text-danger">{{ $errors->first('manage_inventory') }}</label>
                                @endif
                            </div>
                        </div>
                        @if(isset($empty_bottle_return_enabled) && $empty_bottle_return_enabled == 1 && $data['segment']->Segment->slag == 'GROCERY')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="empty_bottle_return">
                                        @lang("$string_file.empty_bottle_return")
                                        <span class="text-danger">*</span>
                                    </label>
                                    {!! Form::select('empty_bottle_return', ['1' => 'Yes', '0' => 'No'], old('empty_bottle_return', isset($data['product']['empty_bottle_return']) ? $data['product']['empty_bottle_return'] : '0'), ['id' => 'empty_bottle_return', 'class' => 'form-control', 'required' => true, 'onchange' => 'toggleBottlePriceField(this.value)']) !!}
                                    @if ($errors->has('empty_bottle_return'))
                                        <label class="text-danger">{{ $errors->first('empty_bottle_return') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4" id="bottle_price_field" style="display: {{ old('empty_bottle_return', isset($data['product']['empty_bottle_return']) ? $data['product']['empty_bottle_return'] : '0') === '1' || (isset($data['product']['empty_bottle_return']) && $data['product']['empty_bottle_return'] === '1') ? 'block' : 'none' }};">
                                <div class="form-group">
                                    <label for="bottle_price">
                                        @lang("$string_file.bottle_price")
                                        <span class="text-danger">*</span>
                                    </label>
                                    {!! Form::number('bottle_price', old('bottle_price', isset($data['product']['bottle_price']) ? $data['product']['bottle_price'] : null), ['id' => 'bottle_price', 'class' => 'form-control', 'min' => 0, "value" => 0, 'step' => '0.01']) !!}
                                    @if ($errors->has('bottle_price'))
                                        <label class="text-danger">{{ $errors->first('bottle_price') }}</label>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if(isset($subscription_enabled) && $subscription_enabled == 1 && $data['segment']->Segment->slag == 'GROCERY')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="subscription_enabled">
                                        @lang("$string_file.subscription_enabled")
                                        <span class="text-danger">*</span>
                                    </label>
                                    {!! Form::select('subscription_enabled', ['1' => 'Yes', '0' => 'No'], old('subscription_enabled', isset($data['product']['subscription_enabled']) ? $data['product']['subscription_enabled'] : '0'), ['id' => 'subscription_enabled', 'class' => 'form-control', 'required' => true]) !!}
                                    @if ($errors->has('subscription_enabled'))
                                        <label class="text-danger">{{ $errors->first('subscription_enabled') }}</label>
                                    @endif
                                </div>
                            </div>
                            
                        @endif
                        <div class="col-md-4">
                            <label for="product_image">
                                {{--@lang("$string_file.other_images")--}}
                                @lang("$string_file.product") @lang("$string_file.list") @lang("$string_file.image")
                                {{--<span class="text-danger">*</span>--}}
                                (W:{{ $data['arr_size']['product_images']['width']  }} * H:{{ $data['arr_size']['product_images']['height'] }})

                            </label>
                            @php
                                $already_uploaded = isset($data['product']->ProductImage) && !empty($data['product']->ProductImage) ? $data['product']->ProductImage->count() : "0"; // Example: 2
                                $max_upload = 4;
                            @endphp
                            <div class="input-group form-group decrement">
                            <!-- ,'multiple'=>true -->
                                {{--,'required'=>$images_required--}}
                                {!! Form::file('product_image[]',['id'=>'product_image','class'=>'form-control','multiple'=>true,'data-existing' => $already_uploaded,'onchange' => 'checkUploadLimit(this)']) !!}
                                @if ($errors->has('product_image'))
                                    <label class="text-danger">{{ $errors->first('product_image') }}</label>
                                @endif
                            </div>
                            @if(isset($data['product']->ProductImage) && !empty($data['product']->ProductImage))
                               <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(90px,1fr)); gap:12px; margin-top:10px; align-items:center;">
                                    @foreach($data['product']->ProductImage as $img)
                                        @php $url = get_image($img->product_image,'product_image',$data['product']['merchant_id']); @endphp
                                        <div style="position:relative;border:1px solid #ddd;border-radius:8px;overflow:hidden;background:#fff;padding:1px;text-align:center;">
                                            <a href="{{ $url }}" target="_blank">
                                                <img src="{{ $url }}" style="width:100%;height:50px;object-fit:cover;border-radius:5px;">
                                            </a>
                                            <a href="{{ route('business-segment.product.image.remove',$img->id) }}"
                                               style="position:absolute;top:5px;right:5px;background:#ff4747;color:#fff;border-radius:50%;padding:4px 6px;font-size:8px;cursor:pointer;text-decoration:none;">
                                                <i class="icon wb-trash" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <div class="form-group  ">
                                <label for="product_cover_image">
                                    @lang("$string_file.product") @lang("$string_file.banner") @lang("$string_file.image")
{{--                                    <span class="text-danger">*</span> --}}
                                    (W:{{  $data['arr_size']['product']['width']  }} * H:{{  $data['arr_size']['product']['height']  }})
                                </label>
                                @if(!empty($data['product']['id']))
                                    <a href="{{ get_image($data['product']['product_cover_image'],'product_cover_image',$data['product']['merchant_id']) }}"
                                       target="_blank">@lang("$string_file.view")</a>
                                @endif
                                {!! Form::file('product_cover_image',['id'=>'product_cover_image','class'=>'form-control']) !!} {{-- ,'required'=>$images_required --}}
                                @if ($errors->has('product_cover_image'))
                                    <label class="text-danger">{{ $errors->first('product_cover_image') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        @if($id == NULL || $edit_permission )
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i>
                            @if($data ['product'] == null)
                                @lang("$string_file.save_and_continue_to_add_variant")
                            @else
                                @lang("$string_file.save")
                            @endif
                        </button>
                        @else
                            <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                        @endif
                    </div>
                    {!!  Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @parent
    <script>
        $(document).ready(function () {
            $('#category_id').change(function () {
                $.ajax({
                    url: "{{ route('business-segment.get.subcategory') }}",
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
    
    <script>
        function toggleBottlePriceField(value) {
            const bottlePriceField = document.getElementById('bottle_price_field');
            if (value === '1') {
                bottlePriceField.style.display = 'block';
            } else {
                bottlePriceField.style.display = 'none';
            }
        }

        // Ensure the field is displayed correctly on page load for edit mode
        document.addEventListener('DOMContentLoaded', function () {
            const emptyBottleReturn = document.getElementById('empty_bottle_return');
            if (emptyBottleReturn && emptyBottleReturn.value === '1') {
                toggleBottlePriceField('1');
            }
        });

        function checkUploadLimit(input) {
            let existing = parseInt(input.dataset.existing); // Already uploaded
            let selected = input.files.length;
            let max = 4;
            if(existing == 4){
                alert("{{trans("$string_file.you_can_upload_only")}} " + max + " {{trans("$string_file.images")}}");
                input.value = ""; // reset file selection
            }
            if ((existing + selected) > max) {
                alert("{{trans("$string_file.you_can_upload_only")}} " + (max - existing) + " {{trans("$string_file.more_images")}}");
                input.value = ""; // reset file selection
            }
        }
    </script>
@endsection
