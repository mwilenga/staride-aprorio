                            @if($slug_name == 'PALM_PAY')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="hidden" id="currency" name="currency" class="form-control" placeholder="" value="{{$currency}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang("$string_file.payee_bank_code") <span class="text-danger">*</span></label>
                                        <select class="form-control" name="bank_code">
                                        <option value="">--Choose Bank Name--</option>
                                            @foreach($bank_data as $option)
                                                <option value="{{$option['bankCode']}}">{{$option['bankName']}}</option>
                                            @endforeach
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang("$string_file.payee_account_number") <span class="text-danger">*</span></label>
                                        <input type="text" id="phone" name="phone" class="form-control" placeholder="" required>
                                </div>
                            </div>
                            @endif