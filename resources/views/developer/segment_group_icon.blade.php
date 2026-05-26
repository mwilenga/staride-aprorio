@extends('developer.layouts.main')
@section("content")
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Handyman Segment Group</h1>
        </div>

        <!-- Content Row -->
        <div class="row">
            <div class="col-xl-12 col-md-12 mb-12">
                <div class="row">
                    <form action="" enctype="multipart/form-data"
                          method="POST">
                        @csrf
                        <div class="panel panel-bordered">
                            <header class="panel-heading">
                                <div class="panel-actions">
                                   
                                </div>
                                <h5 class="panel-title">
                                    <i class="fa fa-cog fa-spin" aria-hidden="true"></i>
                                    @lang("$string_file.segment_group_icons")</h5>
                            </header>
                            <div id="exampleTransition" class="page-content container-fluid card" data-plugin="animateList">
                                <form action="{{route('developer.segment-group-icon-save')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="form-group">
                                                <label for="handyman_grouping_icon">
                                                    @lang("$string_file.handyman_grouping_icon"):
                                                    <span class="text-danger">*</span>
                                                    @if(!empty($merchant_segement_group_icons->handyman_segement_group_icon))
                                                        <a href="{{get_image($merchant_segement_group_icons->handyman_segement_group_icon,'segment_group_icons')}}" target="_blank">@lang("$string_file.view")</a>
                                                    @endif
                                                </label>
                                                <input type="file" class="form-control" id="icon_1"
                                                       name="handyman_grouping_icon"
                                                       placeholder="" >
                    
                                                @if ($errors->has('handyman_grouping_icon'))
                                                    <label class="text-danger">{{ $errors->first('handyman_grouping_icon') }}</label>
                                                @endif
                    
                                            </div>
                                            <div class="form-group">
                                                <label for="handyman_grouping_name">
                                                    @lang("$string_file.handyman_grouping_name"):
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="icon_1"
                                                       name="handyman_grouping_name" @if(!empty($merchant)) value="{{$merchant->handyman_segement_group_name}}" @endif
                                                       placeholder="" @if(empty($details)) required @endif>
                    
                                                @if ($errors->has('handyman_grouping_name'))
                                                    <label class="text-danger">{{ $errors->first('handyman_grouping_name') }}</label>
                                                @endif
                    
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <input type="submit" name="save" value="save" class="btn btn-primary">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
@endsection