@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('business-segment.warehouse.product.index') }}">
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
                    <form method="POST" class="steps-validation wizard-notification" enctype="multipart/form-data" action="{{route('business-segment.warehouse.save',$product_variant->id)}}" autocomplete="false">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sku_id">
                                    @lang("$string_file.sku_no")
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="sku_id" name="sku_id" value="{{ $product_variant->sku_id ? $product_variant->sku_id : old('sku_id') }}" required>
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
                                <input type="text" class="form-control" id="product_name" name="product_name" value="{{ !empty($product_variant) && $product_variant->is_title_show == 1 ? $product_variant->Name($merchant_id) : (!empty($product_variant) ? $product_variant->Product->Name($merchant_id) : old('product_name')) }}" required>
                                @if ($errors->has('product_name'))
                                    <label class="text-danger">{{ $errors->first('product_name') }}</label>
                                @endif
                            </div>
                        </div>
                        <input type="hidden" id="product_id" name="product_id" value="{{!empty($product_variant) ? $product_variant->product_id : "" }}">
                        <input type="hidden" id="business_segment_id" name="business_segment_id" value="{{!empty($product_variant) ? $product_variant->Product->business_segment_id : "" }}">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="price" class="control-label">@lang("$string_file.price")</label>
                                <input type="number" class="form-control" id="price" name="price" step="any" min="0" placeholder="@lang("$string_file.price")" value="{{!empty($product_variant) ? custom_number_format($product_variant->product_price,$trip_calculation_method) : old('price') }}"
                                                       required>
                            </div>
                            @if ($errors->has('price'))
                                    <label class="text-danger">{{ $errors->first('price') }}</label>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="discount" class="control-label">@lang("$string_file.discount")</label>
                                <input type="number" class="form-control" id="discount" name="discount" step="any" min="0" placeholder="@lang("$string_file.discount")" value="{{!empty($product_variant) ? custom_number_format($product_variant->discount,$trip_calculation_method) : old('price') }}"
                                                       required>
                            </div>
                            @if ($errors->has('discount'))
                                    <label class="text-danger">{{ $errors->first('discount') }}</label>
                            @endif
                        </div>
                        
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="weight_unit_id">@lang("$string_file.weight_unit")</label>
                                <select name="weight_unit_id" id="weight_unit_id" class="form-control">
                                    <option value="">@lang("$string_file.select")</option>
                                    @foreach($arr_weight_unit as $wuId=>$unit)
                                        <option value="{{ $wuId }}"
                                            {{ !empty($product_variant) && $product_variant->weight_unit_id == $wuId ? 'selected' : '' }}>
                                            {{ $unit }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('weight_unit_id') <label class="text-danger">{{ $message }}</label> @enderror
                            </div>
                        </div>
                    
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="weight">@lang("$string_file.weight")</label>
                                <input type="number" class="form-control" id="weight" name="weight" step="any" min="0"
                                       value="{{ $product_variant->weight ?? old('weight') }}">
                                @error('weight') <label class="text-danger">{{ $message }}</label> @enderror
                            </div>
                        </div>
                    
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="is_title_show">@lang("$string_file.show_title")</label>
                                <select name="is_title_show" id="is_title_show" class="form-control">
                                    <option value="1" {{ $product_variant->is_title_show == 1 ? 'selected' : '' }}>@lang("$string_file.yes")</option>
                                    <option value="0" {{ $product_variant->is_title_show == 0 ? 'selected' : '' }}>@lang("$string_file.no")</option>
                                </select>
                                @error('is_title_show') <label class="text-danger">{{ $message }}</label> @enderror
                            </div>
                        </div>
                    
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">@lang("$string_file.status")</label>
                                <select name="status" id="status" class="form-control">
                                    @foreach($product_status as $key => $statusLabel)
                                        <option value="{{ $key }}" {{ $product_variant->status == $key ? 'selected' : '' }}>
                                            {{ $statusLabel }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status') <label class="text-danger">{{ $message }}</label> @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="current_stock">@lang("$string_file.current_stock")</label>
                                <input type="number" class="form-control" id="current_stock" name="current_stock" min="0"
                                       value="{{ $product_variant->ProductInventory->current_stock ?? old('current_stock') }}">
                                @error('current_stock') <label class="text-danger">{{ $message }}</label> @enderror
                            </div>
                        </div>
                    
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="product_cost">@lang("$string_file.product_cost")</label>
                                <input type="number" class="form-control" id="product_cost" name="product_cost" step="any" min="0"
                                       value="{{ $product_variant->ProductInventory->product_cost ?? old('product_cost') }}">
                                @error('product_cost') <label class="text-danger">{{ $message }}</label> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions d-flex flex-row-reverse p-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                                    </button>
                                </div>
                </div>
            </div>
        </div>
    </div>