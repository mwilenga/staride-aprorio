@extends('merchant.layouts.main')

@section('content')
<div class="page">
    <div class="page-content">
        <div class="panel panel-bordered">
            
            <header class="panel-heading">
                <h3 class="panel-title">
                    <i class="wb-flag"></i> Add Banner / Offer
                </h3>
            </header>

            <div class="panel-body">
                <form action="{{ route('merchant.banners_offers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <select name="type" id="type" class="form-control mb-3">
                        <option value="BANNER">Banner</option>
                        <option value="OFFER">Offer</option>
                    </select>
                    <input type="text" name="title" class="form-control mb-2" placeholder="Title" required>
                    <input type="text" name="sub_title" class="form-control mb-2" placeholder="Sub Title">

                    <div class="banner-only">
                        <input type="file" name="image" class="form-control mb-2">
                        <input type="text" name="hyperlink" class="form-control mb-2" placeholder="Hyperlink">
                    </div>

             

                    <button class="btn btn-success w-100">Submit</button>
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
toggle();
</script>
@endsection