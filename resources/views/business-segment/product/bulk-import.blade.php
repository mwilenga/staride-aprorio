@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="row">
                        <div class="col-md-3">
                            <h3 class="panel-title">@lang("$string_file.import_bulk_products")</h3>
                        </div>
                        <div class="col-md-9">
                            <a class="btn btn-icon btn-primary float-right" style="margin:10px" href="{{route("business-segment.product.image.bulk-import")}}" target="_blank" type="button">
                                @lang("$string_file.bulk_image_upload")
                            </a>
                        </div>
                    </div>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          name="bulk-import-product"
                          id="bulk-import-product"
                          action="{{route('business-segment.product.bulk-import.preview')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <label>@lang("$string_file.import_file")  <span class="text-danger">*</span></label>
                                <div class="form-group">
                                    <input type="file" id="import_file" name="import_file" class="form-control" required>
                                    @if ($errors->has('import_file'))
                                        <label class="text-danger">{{ $errors->first('import_file') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary" id="previewButton">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.preview")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <h3 class="panel-title">
                        <i class=" icon wb-shopping-cart" aria-hidden="true"></i>@lang("$string_file.products")
                    </h3>
                </header>
                <div class="panel-body" style="overflow: scroll">
                    <table class="table table-bordered" id="" style="width:170%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.sku_no")</th>
                            <th>@lang("$string_file.product_name")</th>
                            <th>@lang("$string_file.description")</th>
                            <th>@lang("$string_file.ingredients")</th>
                            <th>@lang("$string_file.category")</th>
                            <th>@lang("$string_file.sequence")</th>
                            <th>@lang("$string_file.cover_image")</th>
                            <th>@lang("$string_file.product") @lang("$string_file.images")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.variants")</th>
                            <th>@lang("$string_file.inventory_status")</th>
                            <th>@lang("$string_file.message")</th>
                        </tr>
                        </thead>
                        @if(!empty($excelData))
                            @php $i = 1; @endphp
                            @foreach($excelData as $product)
                                <tr>
                                    <td>
                                        @if($product['is_valid'])
                                            <span class="badge badge-success">@lang("$string_file.valid")</span>
                                        @else
                                            <span class="badge badge-danger">@lang("$string_file.invalid")</span>
                                        @endif
                                    </td>
                                    <td>{{$i++}}</td>
                                    <td>{{$product['sku_id']}}</td>
                                    <td>{{$product['name']}}</td>
                                    <td>{{$product['description']}}</td>
                                    <td>{{$product['ingredients']}}</td>
                                    <td>{{$product['category_name']}}</td>
                                    <td>{{$product['sequence']}}</td>
                                    <td>
                                        <img src="{{ get_image($product['product_cover_image'],'product_cover_image',$merchant_id)}}" width="80px" height="80px">
                                    </td>
                                    <td>
                                        @if(!empty($product['product_image_1']))
                                            <img src="{{ get_image($product['product_image_1'],'product_image',$merchant_id)}}" width="80px" height="80px">
                                        @endif
                                        @if(!empty($product['product_image_2']))
                                            <img src="{{ get_image($product['product_image_2'],'product_image',$merchant_id)}}" width="80px" height="80px">
                                        @endif
                                        @if(!empty($product['product_image_3']))
                                            <img src="{{ get_image($product['product_image_3'],'product_image',$merchant_id)}}" width="80px" height="80px">
                                        @endif
                                        @if(!empty($product['product_image_4']))
                                            <img src="{{ get_image($product['product_image_3'],'product_image',$merchant_id)}}" width="80px" height="80px">
                                        @endif
                                    </td>
                                    <td>
                                        @if($product['status'] == 1)
                                            <span class="badge badge-success">{{$product_status[$product['status']]}}</span>
                                        @else
                                            <span class="badge badge-danger">{{$product_status[$product['status']]}}</span>
                                        @endif
                                    </td>
                                    <td>{{count($product['product_variants'])}}</td>
                                    <td>{{$inventory_status[$product['manage_inventory']]}}</td>
                                    <td>
                                        @if(!empty($product['error_messages']))
                                            @foreach($product['error_messages'] as $message)
                                                {{$message.","}}<br>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                @if(count($product['product_variants']) > 0)
                                    <tr>
                                        <td>-</td>
                                        <td colspan="12">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>@lang("$string_file.status")</th>
                                                    <th>@lang("$string_file.sn")</th>
                                                    <th>@lang("$string_file.sku_no")</th>
                                                    <th>@lang("$string_file.title")</th>
                                                    <th>@lang("$string_file.price")</th>
                                                    <th>@lang("$string_file.weight")</th>
                                                    <th>@lang("$string_file.is_title_show")</th>
                                                    <th>@lang("$string_file.status")</th>
                                                    <th>@lang("$string_file.current_stock")</th>
                                                    <th>@lang("$string_file.product_cost")</th>
                                                    <th>@lang("$string_file.selling_price")</th>
                                                    <th>@lang("$string_file.message")</th>
                                                </tr>
                                                @php $j = 1; @endphp
                                                @foreach($product['product_variants'] as $variant)
                                                    <tr>
                                                        <td>
                                                            @if($variant['is_valid'])
                                                                <span class="badge badge-success">@lang("$string_file.valid")</span>
                                                            @else
                                                                <span class="badge badge-danger">@lang("$string_file.invalid")</span>
                                                            @endif
                                                        </td>
                                                        <td>{{$j++}}</td>
                                                        <td>{{$variant['sku_id']}}</td>
                                                        <td>{{$variant['product_title']}}</td>
                                                        <td>{{$variant['product_price']}}</td>
                                                        <td>{{$variant['weight_text']}}</td>
                                                        <td>
                                                            @if($variant['is_title_show'] == 1)
                                                                <span class="badge badge-success">@lang("$string_file.yes")</span>
                                                            @else
                                                                <span class="badge badge-danger">@lang("$string_file.no")</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($variant['status'] == 1)
                                                                <span class="badge badge-success">{{$product_status[$variant['status']]}}</span>
                                                            @else
                                                                <span class="badge badge-danger">{{$product_status[$variant['status']]}}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{$variant['current_stock']}}</td>
                                                        <td>{{$variant['product_cost']}}</td>
                                                        <td>{{$variant['product_selling_price']}}</td>
                                                        <td>
                                                            @if(!empty($variant['error_messages']))
                                                                @foreach($variant['error_messages'] as $message)
                                                                    {{$message.","}}<br>
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <tr>
                                <th colspan="13" style="text-align: center">No Products Found For Preview</th>
                            </tr>
                        @endif
                        <tbody>
                        @php $sr = 1; @endphp
                        </tbody>
                    </table>
                </div>
                @if(!empty($excelData))
                    <div class="panel-footer">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              name="bulk-import-product-submit"
                              id="bulk-import-product-submit"
                              action="{{route('business-segment.product.bulk-import.submit')}}">
                            @csrf
                            {{--<input type="hidden" name="excel_data" value="{{$excelData}}">--}}
                            <div class="form-actions d-flex flex-row-reverse p-2">
                                <button type="submit" class="btn btn-primary" id="submitButton">
                                    <i class="fa fa-check-circle"></i> @lang("$string_file.submit")
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
