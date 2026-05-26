@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('business-segment.product.bulk-import') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" fa fa-building" aria-hidden="true"></i>
                        @lang("$string_file.import_bulk_product_images")

                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          name="bulk-import-product-image"
                          id="bulk-import-product-image"
                          action="{{route('business-segment.product.image.bulk-import.preview')}}">
                        @csrf
                        <div class="row">

                            <div class="col-md-4">
                                <label>
                                    @lang("$string_file.image") @lang("$string_file.type")<span class="text-danger">*</span>
                                </label>
                                <div class="form-group">
                                    <select id="image_type" name="image_type" class="form-control" >
                                        <option value="1">@lang("$string_file.product") @lang("$string_file.cover") @lang("$string_file.image")</option>
                                        <option value="2">@lang("$string_file.product") @lang("$string_file.image")</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label>
                                    @lang("$string_file.import_files")<span class="text-danger">*</span> (W:{{  $arr_size['product']['width']  }} * H:{{  $arr_size['product']['height']  }})
                                </label>
                                <div class="form-group">
                                    <input type="file" id="import_files" name="import_files[]" class="form-control" multiple required>
                                    @if ($errors->has('import_files'))
                                        <label class="text-danger">{{ $errors->first('import_files') }}</label>
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
                        <i class=" icon wb-shopping-cart" aria-hidden="true"></i>@lang("$string_file.images")
                    </h3>
                </header>
                <div class="panel-body" style="overflow: scroll">
                    <table class="table table-bordered" id="" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.image")</th>
                        </tr>
                        </thead>
                        @if(!empty($imageData))
                            @php $i = 1; @endphp
                            @foreach($imageData as $image)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$image}}</td>
                                    <td>
                                        <img src="{{ get_image($image,'product_cover_image',$merchant_id)}}" width="80px" height="80px">
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <th colspan="13" style="text-align: center">No Product Images Found For Preview</th>
                            </tr>
                        @endif
                        <tbody>
                        @php $sr = 1; @endphp
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
