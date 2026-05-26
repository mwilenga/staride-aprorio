@extends('merchant.layouts.main')

@section('content')
<div class="page">
    <div class="page-content">
        <div class="panel panel-bordered">

            <header class="panel-heading">
                <h3 class="panel-title">
                    <i class="wb-flag"></i> Edit Banner / Offer
                </h3>
            </header>

            <div class="panel-body">
                <form action="{{ route('merchant.banners_offers.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <select name="type" id="type" class="form-control mb-3">
                        <option value="BANNER" {{ $data->type == 'BANNER' ? 'selected' : '' }}>Banner</option>
                        <option value="OFFER" {{ $data->type == 'OFFER' ? 'selected' : '' }}>Offer</option>
                    </select>

                    <input type="text" name="title" value="{{ $data->title }}" class="form-control mb-2" placeholder="Title" required>

                    <input type="text" name="sub_title" value="{{ $data->sub_title }}" class="form-control mb-2" placeholder="Sub Title">

                    <div class="banner-only">
                        
                        @if($data->image)
                            <img src="{{ get_image($data->image, 'banners_image', $data->merchant_id) }}" 
                                 class="img-thumbnail mb-2" width="120">
                        @endif

                        <input type="file" name="image" class="form-control mb-2">

                        <input type="text" name="hyperlink" value="{{ $data->hyperlink }}" 
                               class="form-control mb-2" placeholder="Hyperlink">
                    </div>

                    <button class="btn btn-primary w-100">Update</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
const type = document.getElementById('type'),
      banner = document.querySelector('.banner-only');

function toggle() {
    banner.style.display = type.value === 'OFFER' ? 'none' : 'block';
}

type.addEventListener('change', toggle);
toggle(); // run on load (important for edit)
</script>

@endsection