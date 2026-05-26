@extends('handyman-store.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('handyman-store.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="mr--10 ml--10">
                        <div class="row " style="margin-right: 0rem;margin-left: 0rem">
                            <div class="col-md-2 col-sm-5">
                                <h3 class="panel-title"><i class="fa-users" aria-hidden="true"></i>
                                    @lang("$string_file.all_driver")</h3>
                            </div>
                            <div class="col-md-10 col-sm-7">
                                @if(!empty($info_setting) && $info_setting->view_text != "")
                                    <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                            data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                        <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                    </button>
                                @endif
                                <a href="{{route('handyman-store.driver.add')}}">
                                    <button type="button" class="btn btn-icon btn-success float-right"
                                            style="margin:10px">
                                        <i class="wb-plus"
                                           title="@lang("$string_file.add_driver")"></i>
                                    </button>
                                </a>
                                @if($export_permission)
                                    <a href="{{route('excel.driver',$arr_search)}}" data-toggle="tooltip">
                                        <button type="button" class="btn btn-icon btn-primary float-right"
                                                style="margin:10px">
                                            <i class="wb-download"
                                               title="@lang("$string_file.export_excel")"></i>
                                        </button>
                                    </a>
                                @endif

                            </div>
                        </div>
                        <div class="row " style="margin-right: 0rem;margin-left: 0rem">
                            <div class="col-md-12 col-sm-7">
                                {{--                                //driver registration status --}}
                            </div>
                        </div>
                    </div>
                </header>
                <div class="panel-body container-fluid">
                    {!! $search_view !!}
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th> @lang("$string_file.id")</th>
                            <th>@lang("$string_file.service_area") </th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.gender")</th>
                            <th>@lang("$string_file.service_statistics")</th>
                            <th>@lang("$string_file.transaction_amount")</th>
                            <th>@lang("$string_file.registered_date")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $drivers->firstItem() @endphp
                        @foreach($drivers as $driver)
                            <tr>
                                <td>{{$sr}}</td>
                                <td><a href="{{ route('driver.show',$driver->id) }}"
                                       class="hyperLink">{{ $driver->merchant_driver_id }}</a>
                                </td>
                                <td>
                                    {{ $driver->CountryArea->CountryAreaName }}
                                </td>
                                <td>
                                    <span class="long_text">
                                        {{ is_demo_data($driver->fullName,$driver->Merchant) }}<br>
                                        {{ is_demo_data($driver->phoneNumber,$driver->Merchant) }}<br>
                                        {{ is_demo_data($driver->email,$driver->Merchant) }}
                                    </span>
                                </td>

                                @switch($driver->driver_gender)
                                    @case(1)
                                        <td>@lang("$string_file.male")</td>
                                        @break
                                    @case(2)
                                        <td>@lang("$string_file.female")</td>
                                        @break
                                    @default
                                        <td>------</td>
                                @endswitch

                                <td>

                                    @php
                                        $handyman_orders = isset($driver->HandymanOrder) ? $driver->HandymanOrder->where('order_status',7)->count() : 0;
                                    @endphp

                                    <br>
                                    @lang("$string_file.rating") :
                                    @if (!empty($driver->rating) && $driver->rating>0)
                                        @while($driver->rating>0)
                                            @if($driver->rating >0.5)
                                                <img src="{{ view_config_image('static-images/star.png') }}"
                                                     alt='Whole Star'>
                                            @else
                                                <img src="{{ view_config_image('static-images/halfstar.png') }}"
                                                     alt='Half Star'>
                                            @endif
                                            @php $driver->rating--; @endphp
                                        @endwhile
                                    @else
                                        @lang("$string_file.not_rated_yet")
                                    @endif
                                </td>
                                <td style="width:250px;float:left">
                                    @if($driver->total_earnings)
                                        @lang("$string_file.earning")
                                        :- {{ $driver->CountryArea->Country->isoCode." ". $driver->total_earnings }}
                                    @else
                                        @lang("$string_file.earning")
                                        :- @lang("$string_file.no_services")
                                    @endif
                                    <br>
                                    @if($driver->total_comany_earning)
                                        @lang("$string_file.company_profit")
                                        :- {{ $driver->CountryArea->Country->isoCode." ".$driver->total_comany_earning }}
                                    @else
                                        ---
                                    @endif
                                    <br>

                                    @if($driver->wallet_money)
                                        @lang("$string_file.wallet_money") :- <a
                                                href="{{ route('handyman-store.driver.wallet.show',$driver->id) }}">{{ $driver->wallet_money }}</a>
                                    @else
                                        @lang("$string_file.wallet_money") :- ------
                                    @endif

                                    <br>
                                <td>{!! convertTimeToUSERzone($driver->created_at, $driver->CountryArea->timezone, null, $driver->Merchant) !!}</td>


                                <td>
                                    <div class="button-margin">
                                        {{--                                        @if(Auth::user('merchant')->can('edit_drivers'))--}}
                                        <a href="{{ route('handyman-store.driver.add',$driver->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <i class="wb-edit"></i> </a>
                                        <a onclick="AddWalletMoneyMod(this)"
                                           data-ID="{{ $driver->id }}"
                                           data-original-title="@lang("$string_file.add_money")"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn text-white btn-sm btn-success">
                                            <i class="fa fa-money"></i> </a>
                                        <a href="{{ route('handyman-store.driver.wallet.show',$driver->id) }}"
                                           data-original-title="@lang("$string_file.wallet_transaction")"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm menu-icon btn-primary btn_money action_btn">
                                            <span class="icon fa-window-maximize"></span></a>
                                        @if($driver->driver_admin_status == 1)
                                            <a href="{{ route('handyman-store.driver.active.deactive',['id'=>$driver->id,'status'=>2]) }}"
                                               data-original-title="@lang("$string_file.inactive")"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                                <i class="fa fa-eye-slash"></i> </a>
                                        @else
                                            <a href="{{ route('handyman-store.driver.active.deactive',['id'=>$driver->id,'status'=>1]) }}"
                                               data-original-title="@lang("$string_file.active")"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @php $sr++; @endphp
                        @endforeach
                        </tbody>
                    </table>
                    {{--                    @include('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => $arr_search])--}}
                    {{--                    <div class="pagination1 float-right">{{ $drivers->appends($arr_search)->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="addWalletMoneyModel" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b>@lang("$string_file.add_money_in_driver_wallet")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    {{--                    @csrf--}}
                    <div class="modal-body">
                        <label>@lang("$string_file.payment_method"): </label>
                        <div class="form-group">
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="1">@lang("$string_file.cash")</option>
                                <option value="2">@lang("$string_file.non_cash")</option>
                            </select>
                        </div>

                        <label>@lang("$string_file.receipt_number"): </label>
                        <div class="form-group">
                            <input type="text" name="receipt_number" id="receipt_number" placeholder=""
                                   class="form-control" required>
                        </div>

                        <label for="transaction_type">
                            @lang("$string_file.transaction_type")<span
                                    class="text-danger">*</span>
                        </label>
                        <div class="form-group">
                            <select id="transaction_type" name="transaction_type" class="form-control" required>
                                <option value="1">@lang("$string_file.credit")</option>
                                <option value="2">@lang("$string_file.debit")</option>
                            </select>
                        </div>

                        <label>@lang("$string_file.amount"): </label>
                        <div class="form-group">
                            <input type="number" name="amount" id="amount" placeholder="@lang("$string_file.amount")"
                                   class="form-control" required>
                        </div>
                        <input type="hidden" name="add_money_driver_id" id="add_money_driver_id">
                        {{--                        <p id="amount_error" class="d-none text-danger">The amount must be atleast 1.</p>--}}

                        <label>@lang("$string_file.description"): </label>
                        <div class="form-group">
                            <textarea class="form-control" id="title1" rows="3" name="description"
                                      placeholder=""></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal"
                               value="@lang("$string_file.close")">
                        <input type="submit" id="add_money_button" class="btn btn-primary"
                               value="@lang("$string_file.save")">
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
@section('js')
    {{--    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>--}}
    <script>
        $('#export-excel').on('click', function () {
            var action = '{{route("excel.driver")}}';
            var arr_param = [];
            var arr_param = $("#driver-search").serializeArray();
            $.ajax({
                type: "GET",
                data: {arr_param},
                url: action,
                success: function (data) {
                    console.log(data);
                }, error: function (err) {
                }
            });
        });
        $('#add_money_button').on('click', function () {
            $('#add_money_button').prop('disabled', true);
            $('#myLoader').removeClass('d-none');
            $('#myLoader').addClass('d-flex');
            var payment_method = document.getElementById('payment_method').value;
            var receipt_number = document.getElementById('receipt_number').value;
            var amount = document.getElementById('amount').value;
            var transaction_type = document.getElementById('transaction_type').value;
            var desc = document.getElementById('title1').value;
            var driver_id = document.getElementById('add_money_driver_id').value;
            // console.log(payment_method,receipt_number,'recipt');
            // if(!receipt_number){
            //     alert()
            // }
            if (amount > 0) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    },
                    type: "POST",
                    data: {
                        payment_method_id: payment_method,
                        receipt_number: receipt_number,
                        amount: amount,
                        transaction_type: transaction_type,
                        description: desc,
                        driver_id: driver_id
                    },
                    url: "{{ route('handyman-store.driver.AddMoney') }}",
                    success: function (data) {
                        console.log(data,'hello');
                        if (data.result == 1) {
                            $('#myLoader').removeClass('d-flex');
                            $('#myLoader').addClass('d-none');
                            swal({
                                title: "@lang("$string_file.driver_account")",
                                text: "@lang("$string_file.money_added_successfully")",
                                icon: "success",
                                buttons: true,
                                dangerMode: true,
                            }).then((isConfirm) => {
                                if (isConfirm) {
                                    window.location.href = "{{ route('handyman-store.driver.index') }}";
                                } else {
                                    window.location.href = "{{ route('handyman-store.driver.index') }}";
                                }
                            });
                        }else{
                            alert(data.message);
                            $('#add_money_button').prop('disabled', false);
                        }
                    }, error: function (err) {
                        $('#myLoader').removeClass('d-flex');
                        $('#myLoader').addClass('d-none');
                    }
                });
            } else {
                $('#amount_error').removeClass('d-none');
                $('#add_money_button').prop('disabled', false);
            }

        });

        function AddWalletMoneyMod(obj) {
            let ID = obj.getAttribute('data-ID');
            $(".modal-body #add_money_driver_id").val(ID);
            $('#addWalletMoneyModel form')[0].reset();
            $('#amount_error').addClass('d-none');
            $('#addWalletMoneyModel').modal('show');
        }

    </script>

    <script>
        function DeleteEvent(id) {
            var token = $('[name="_token"]').val();
            swal({
                title: "@lang("$string_file.are_you_sure")",
                text: "@lang("$string_file.delete_warning")",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: "POST",
                        data: {
                            id: id,
                        },
                        url: "{{ route('driverDelete') }}",
                    }).done(function (data) {
                        swal({
                            title: "@lang("$string_file.deleted")",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "{{ route('driver.index') }}";
                    });
                } else {
                    swal("@lang("$string_file.data_is_safe")");
                }
            });
        }

        function selectSearchFields() {
            var segment_id = $('#segment_id').val();
            var area_id = $('#area_id').val();
            var by = $('#by_param').val();
            var by_text = $('#keyword').val();
            if (segment_id.length == 0 && area_id == "" && by == "" && by_text == "" && driver_status == "") {
                alert("Please select at least one search field");
                return false;
            } else if (by != "" && by_text == "") {
                alert("Please enter text according to selected parameter");
                return false;
            } else if (by_text != "" && by == "") {
                alert("Please select parameter according to entered text");
                return false;
            }
        }

        // get location
        $('.view_current_location').on('click', function () {
            var ats_id = $(this).attr("id");
            var driver_id = $(this).attr("driver_id");
            var timezone = $(this).attr("timezone");

            if (ats_id == "" || ats_id == "NA") {
                alert("@lang("$string_file.ats_id_error")");
                return true;
            }
            var token = $('[name="_token"]').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                type: "POST",
                data: {
                    "ats_id": ats_id,
                    "driver_timezone": timezone,
                },
                url: "{{ route('merchant.get-lat-long') }}",
                success: function (data) {
                    console.log(data);
                    $("#" + driver_id).html(data);
                }, error: function (err) {
                    $("#" + driver_id).text("No Data");
                }
            });
        });

        $('#entries').on('change', function() {
            var selectedValue = $(this).val();
            var baseUrl = "{{ route('driver.index') }}";
            var currentUrl = baseUrl + window.location.search; // Get the current URL including existing query parameters

            // Parse the query parameters into an object
            var queryParams = new URLSearchParams(window.location.search);
            if (queryParams.has('per_page')) {
                queryParams.set('per_page', selectedValue);
            } else {
                queryParams.append('per_page', selectedValue);
            }

            // Construct the updated URL with the modified query parameters
            var updatedUrl = baseUrl + '?' + queryParams.toString();
            location.href = updatedUrl;
        });

    </script>
@endsection
