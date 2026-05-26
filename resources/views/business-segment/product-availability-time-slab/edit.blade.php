@extends('business-segment.layouts.main')
@section('content')
    @php $id = NULL; @endphp
    @if(isset($data['slab']['id']))
        @php $id = $data['service_time_slot']['id']; @endphp
    @endif
    <div class="page">
        <div class="page-content">
            {{-- file to display error and success message --}}
            @include('business-segment.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->add_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px" data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="{{ route('product-availability-time-slabs') }}">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.edit") @lang("$string_file.product_availability_time_slabs")@if(!empty($id))
                        @endif
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        {!! Form::open(["id" => "product-availability-time-slabs.save","name" => "product-availability-time-slabs.save-form","class"=>"steps-validation wizard-notification","files"=>true,"url"=>route("product-availability-time-slabs.save")]) !!}
                        {!! Form::hidden('time_format',$data['time_format']) !!}
                        {!! Form::hidden('calling_from',"edit") !!}
                        <fieldset>
                            <div class="row">
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-bordered" id="timeSlotsTable">
                                        <thead>
                                        <tr>
                                            <th>@lang("$string_file.time_slab") @lang("$string_file.name")</th>
                                            <th> @lang("$string_file.image") </th>
                                            <th> @lang("$string_file.custom") </th>
                                            <th>@lang("$string_file.start") @lang("$string_file.time")</th>
                                            <th>@lang("$string_file.end") @lang("$string_file.time")</th>
                                            <th> @lang("$string_file.available_all_day") </th>
                                            <th> @lang("$string_file.priority") </th>
                                            <th> @lang("$string_file.custom") @lang("$string_file.price") </th>
                                            <th width="80">@lang("$string_file.action")</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $oldids  = $data['slab']->pluck("id");
                                            $oldNames  = $data['slab']->pluck("name");
                                            $oldStart  = $data['slab']->pluck("start_time");
                                            $oldEnd    = $data['slab']->pluck("end_time");
                                            $oldAllDay = $data['slab']->pluck("end_time");
                                            $old_priority = $data['slab']->pluck("priority");
                                            $old_is_custom = $data['slab']->pluck("is_custom");
                                            $old_custom_price = $data['slab']->pluck("custom_price");
                                            $rowCount  = $data['slab']->count();
                                        @endphp

                                        @for($i=0; $i < $rowCount; $i++)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="time_slab_id[]" value="{{ $oldids[$i] }}">
                                                    <input type="text" name="time_slab_name[]"
                                                           class="form-control"
                                                           value="{{ $oldNames[$i] ?? '' }}">
                                                </td>

                                                <td>
                                                    <input type="file" name="image_file[{{$i}}]"
                                                           class="form-control">
                                                </td>

                                                <td>
                                                    <input type="checkbox"
                                                           name="is_custom[{{ $i }}]"
                                                           value="1"
                                                           {{ ($old_is_custom[$i] ?? 0) == 1 ? 'checked' : '' }}
                                                           onchange="handleCustomSlab(this)">
                                                </td>

                                                <td>
                                                    <input type="text" name="start_time[]"
                                                           class="form-control timepicker"
                                                           value="{{ $oldStart[$i] ?? '' }}"
                                                           {{ ($old_is_custom[$i] ?? 0) == 1 ? 'readonly' : '' }}>
                                                </td>

                                                <td>
                                                    <input type="text" name="end_time[]"
                                                           class="form-control timepicker"
                                                           value="{{ $oldEnd[$i] ?? '' }}"
                                                           {{ ($old_is_custom[$i] ?? 0) == 1 ? 'readonly' : '' }}>
                                                </td>

                                                <td>
                                                    <input type="checkbox"
                                                           name="available_all_day[{{ $i }}]"
                                                           value="1"
                                                           {{ ($old_is_custom[$i] ?? 0) == 1 ? 'readonly' : '' }}
                                                           onchange="handleAllDay(this)">
                                                </td>
                                                
                                                <td>
                                                    <input type="number" name="priority[{{$i}}]"
                                                           class="form-control"
                                                           value="{{ $old_priority[$i] ?? '' }}"
                                                           {{ ($old_is_custom[$i] ?? 0) == 1 ? 'readonly' : '' }}>
                                                </td>

                                                <td>
                                                    <input type="number" 
                                                           name="custom_price[]"
                                                           class="form-control custom-price-input"
                                                           step="0.01"
                                                           min="0"
                                                           value="{{ $old_custom_price[$i] ?? '' }}"
                                                           style="display: {{ ($old_is_custom[$i] ?? 0) == 1 ? 'block' : 'none' }};">
                                                </td>

                                                <td>
                                                    @if($i == 0)
                                                        <button type="button" class="btn btn-success addRow"><i class="fa fa-plus"></i></button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endfor

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </fieldset>
                        <div class="form-actions float-right">
                            @if($edit_permission)
                                <br>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-square-o"></i>{!! $data['submit_button'] !!}
                                </button>
                            @else
                                <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                            @endif
                        </div>
                        {!! Form::close() !!}
                    </section>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }

        .table-responsive table {
            min-width: 1000px; /* adjust as needed */
        }

        /* Optional: Style the scrollbar */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

    </style>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection
@section('js')
    <script type="text/javascript">
        let slotIndex = $("#timeSlotsTable tbody tr").length;

        $(document).on("click", ".addRow", function () {

            let row = `
                <tr>
                    <td><input type="text" name="time_slab_name[${slotIndex}]" class="form-control"></td>

                    <td>
                        <input type="file" name="image_file[${slotIndex}]"
                                class="form-control">
                    </td>
                    
                    <td>
                        <input type="checkbox"
                                name="is_custom[${slotIndex}]"
                                value="1"
                                onchange="handleCustomSlab(this)">
                    </td>

                    <td><input type="text" name="start_time[${slotIndex}]" class="form-control timepicker"></td>

                    <td><input type="text" name="end_time[${slotIndex}]" class="form-control timepicker"></td>

                    <td>
                        <input type="checkbox" name="available_all_day[${slotIndex}]" value="1" onchange="handleAllDay(this)">
                    </td>

                    <td>
                        <input type="number" class="form-control" name="priority[${slotIndex}]"  min="1">
                    </td>

                    <td>
                        <input type="number" name="custom_price[${slotIndex}]" class="form-control custom-price-input" step="0.01" min="0" style="display: none;">
                    </td>

                    <td>
                        <button type="button" class="btn btn-danger removeRow">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $("#timeSlotsTable tbody").append(row);

            $('.timepicker').timepicker({
                timeFormat: 'H:i'
            });

            slotIndex++;
        });

        $(document).on("click", ".removeRow", function () {
            $(this).closest("tr").remove();
        });

        $('.timepicker').timepicker({
            timeFormat: 'H:i'
        });

        function handleAllDay(checkbox) {
            let row = checkbox.closest("tr");
            let endInput = row.querySelector('input[name^="end_time"]');
            let time =  "23:59";

            if (checkbox.checked) {
                endInput.disabled = true;
                endInput.value = time;
            } else {
                endInput.disabled = false;
                endInput.value = "";
            }
        }

        function handleCustomSlab(checkbox) {
            let row = checkbox.closest("tr");

            let startInput = row.querySelector('input[name^="start_time"]');
            let endInput = row.querySelector('input[name^="end_time"]');
            let availableAllDay = row.querySelector('input[name^="available_all_day"]');
            let priority = row.querySelector('input[name^="priority"]');
            let customPrice = row.querySelector('input[name^="custom_price"]');

            if (checkbox.checked) {
                startInput.readOnly = true;
                endInput.readOnly = true;
                availableAllDay.readOnly = true;
                priority.readOnly = true;
                customPrice.style.display = 'block';
                customPrice.required = true;
            } else {
                startInput.readOnly = false;
                endInput.readOnly = false;
                availableAllDay.readOnly = false;
                priority.readOnly = false;
                customPrice.style.display = 'none';
                customPrice.required = false;
                customPrice.value = '';
            }
        }

        // Initialize custom price visibility on page load
        $(document).ready(function() {
            $('input[name^="is_custom"]').each(function() {
                if (this.checked) {
                    handleCustomSlab(this);
                }
            });
        });
    </script>
@endsection