@extends('business-segment.layouts.main')
@section('content')
    @php
     use Illuminate\Support\Str;
    @endphp
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-shopping-cart" aria-hidden="true"></i>
                        @lang("$string_file.business_segment") </h3>
                </header>
                <div class="panel-body">
                    
                    <table class="table table-bordered">
                        <tbody>
                            {{-- Segments with products --}}
                            @foreach($product_variant_grouped as $segmentKey => $variants)
                                @php
                                    list($segmentId, $segmentName) = explode('|', $segmentKey);
                                @endphp
                                <tr class="segment-row align-middle" 
                                    style="cursor:pointer;" 
                                    data-segment="{{ Str::slug($segmentName) }}"
                                    onclick="window.location='{{ route('business-segment.warehouse.show-product', $segmentId) }}'">
                                    <td>
                                        <span style="font-weight:600; color:#2c3e50;">
                                            {{ $segmentName }}
                                            <i class="fa fa-chevron-right ml-2 text-secondary"></i>
                                        </span>
                                        <span class="badge badge-primary ml-2">{{ $variants->count() }}</span>
                                        <span class="badge badge-success ml-2">Stock: {{ $variants->sum(fn($v) => $v->ProductInventory->current_stock) }}</span>
                                        <span class="badge badge-info ml-2">Active: {{ $variants->where('status', 1)->count() }}</span>
                                    </td>
                                </tr>
                            @endforeach
                    
                            {{-- Segments without products --}}
                            @foreach($bsWithoutProducts as $segment)
                                <tr class="segment-row align-middle" 
                                    style="cursor:pointer; opacity: 0.6; background-color: #f8f9fa;" 
                                    data-segment="{{ Str::slug($segment['name']) }}"
                                    onclick="window.location='{{ route('business-segment.warehouse.show-product', $segment['id']) }}'">
                                    <td>
                                        <span style="font-weight:600; color:#6c757d;">
                                            {{ $segment['name'] }}
                                            <i class="fa fa-chevron-right ml-2 text-secondary"></i>
                                        </span>
                                        <span class="badge badge-primary ml-2">0</span>
                                        <span class="badge badge-danger ml-2">No Product Available</span>
                                        <span class="badge badge-info ml-2">Active: 0</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection