@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h4 class="panel-title">
                        <i class=" icon fa-gears" aria-hidden="true"></i>
                        @lang("$string_file.packaging_preferences")

                    </h4>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification" enctype="multipart/form-data"
                          action="{{ route('business-segment.save-packaging-preferences', ["id"=> (!empty($subscription)? $subscription->id : NULL)]) }}">
                        @csrf
                        <div>
                            @php
                                $values =  !empty($packaging_preferences) ? $packaging_preferences  : [];
                                $vars_count = count($values);
                                if ($vars_count == 0) $vars_count = 1;
                            @endphp
                            <input type="hidden" name="slab_count" id="slab_count" value="{{ $vars_count }}">

                            <div id="packaging-container">
                                @if (count($values)>0)
                                    @foreach ($values as $index => $value)
                                        <div class="packaging-row" data-row-id="{{ $index + 1 }}">
                                            <div class="row">
                                                <input type="hidden" name="package_preference_id[]" value="{{$value->id}}">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="description_{{ $index + 1 }}">@lang("$string_file.description")<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="description[]" id="description_{{ $index + 1 }}" placeholder="@lang("$string_file.description")" value="{{ $value->getPackagingPreferenceDescriptionAttribute() }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="amount_{{ $index + 1 }}">@lang("$string_file.amount")<span class="text-danger">*</span></label>
                                                        <input type="number" class="form-control" name="amount[]" id="amount_{{ $index + 1 }}" placeholder="@lang("$string_file.amount")" value="{{ $value->amount }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center">
                                                        {{-- File Input on the Left --}}
                                                        <div class="flex-grow-1">
                                                            <div class="form-group">
                                                                <label for="icon_{{ $index + 1 }}">
                                                                    @lang("$string_file.icon") <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="file" class="form-control" name="icon[]" id="icon_{{ $index + 1 }}" placeholder="@lang("$string_file.icon")">
                                                            </div>
                                                        </div>

                                                        {{-- Image Preview on the Right --}}
                                                        @if(!empty($value->icon))
                                                            <div class="ml-3">
                                                                <a href="{{ get_image($value->icon ,'packaging_preferences', $merchant_id) }}" target="_blank">
                                                                    <img width="70" height="70" style="border-radius: 50%"
                                                                         src="{{ get_image($value->icon ,'packaging_preferences', $merchant_id) }}">
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mt-4">
                                                        <button type="button" class="btn btn-success add-row">@lang("$string_file.add")</button>
                                                        <button type="button" class="btn btn-danger remove-row">@lang("$string_file.remove")</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- Default empty row if no values exist --}}
                                    <div class="packaging-row" data-row-id="1">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="description_1">@lang("$string_file.description")<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="description[]" id="description_1" placeholder="@lang("$string_file.description")" required>
                                                </div>
                                            </div>
{{--                                            <div class="col-md-3">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <label for="icon_1">@lang("$string_file.icon")<span class="text-danger">*</span></label>--}}
{{--                                                    <input type="file" class="form-control" name="icon[]" id="icon_1" placeholder="@lang("$string_file.icon")" required>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="amount_1">@lang("$string_file.amount")<span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="amount[]" id="amount_1" placeholder="@lang("$string_file.amount")" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    {{-- File Input on the Left --}}
                                                    <div class="flex-grow-1">
                                                        <div class="form-group">
                                                            <label for="icon_1">
                                                                @lang("$string_file.icon") <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="file" class="form-control" name="icon[]" id="icon_1" placeholder="@lang("$string_file.icon")">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mt-4">
                                                    <button type="button" class="btn btn-success add-row">@lang("$string_file.add")</button>
                                                    <button type="button" class="btn btn-danger remove-row">@lang("$string_file.remove")</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>


                        <div class="form-actions right" style="margin-bottom: 3%">
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-check-square-o"></i> @lang("$string_file.save")
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('js')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById("packaging-container");
            let rowCount = document.querySelectorAll(".packaging-row").length; // Start count from existing rows

            // Add new row
            document.addEventListener("click", function (event) {
                if (event.target.classList.contains("add-row")) {
                    rowCount++;

                    if(document.querySelectorAll(".packaging-row").length > 2){
                        alert("Maximum 3 preferences are allowed ");
                    }
                    else{
                        let newRow = event.target.closest(".packaging-row").cloneNode(true);
                        newRow.setAttribute("data-row-id", rowCount);

                        // Remove any preview image from the clone
                        let image = newRow.querySelector("img");
                        if (image) {
                            image.parentNode.remove(); // remove <a> tag containing <img>
                        }

                        // Update input names and clear values
                        newRow.querySelectorAll("input").forEach((input) => {
                            let baseName = input.getAttribute("name").replace("[]", "");
                            input.setAttribute("id", baseName + "_" + rowCount);
                            input.value = ""; // Clear previous value
                        });

                        container.appendChild(newRow);

                        let last_count = parseInt($('#slab_count').val(), 10);
                        last_count++;
                        $('#slab_count').val(last_count);
                    }


                }
            });

            // Remove row
            document.addEventListener("click", function (event) {
                if (event.target.classList.contains("remove-row")) {
                    let row = event.target.closest(".packaging-row");
                    if (document.querySelectorAll(".packaging-row").length > 1) {
                        row.remove();

                        let last_count = parseInt($('#slab_count').val(), 10);
                        last_count--;
                        $('#slab_count').val(last_count);
                    }
                    else {
                        alert("At least one row is required.");
                    }
                }
            });
        });

    </script>
@endsection