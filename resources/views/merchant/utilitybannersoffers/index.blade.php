@extends('merchant.layouts.main')

@section('content')
<div class="page">
    <div class="page-content">

        <div class="panel panel-bordered">

            {{-- Header --}}
            <header class="panel-heading d-flex justify-content-between align-items-center">
                <h3 class="panel-title">
                    <i class="wb-flag" aria-hidden="true"></i> Banners & Offers List
                </h3>

                <a href="{{ route('merchant.banners_offers.create') }}" class="btn btn-primary">
                    Add New
                </a>
            </header>

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success m-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Table --}}
            <div class="panel-body container-fluid">
                <table class="display nowrap table table-hover table-striped w-full" 
                       id="customDataTable" style="width: 100%">

                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Sub Title</th>
                            
                            <th>Type</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($transactions as $item)
                            <tr>
                                <td>{{ $item->title }}</td>

                                <td>{{ $item->sub_title ?? '-' }}</td>

                  

                                <td>
                                    <span class="badge bg-{{ $item->type == 'banner' ? 'success' : 'info' }}">
                                        {{ ucfirst($item->type) }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('merchant.banners_offers.edit', $item->id) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('merchant.banners_offers.destroy', $item->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No data found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>
@endsection