@extends('business-segment.layouts.main')
@section('content')
<div class="page">
    <div class="page-content">
       @include('business-segment.shared.errors-and-messages')
        <div class="panel panel-bordered">
            <header class="panel-heading">
                <div class="panel-actions">
                </div>
                <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                    @lang("$string_file.purchase_membership_plan")</h3>
            </header>
            <div class="panel-body container-fluid">
                
                <form method="POST" class="steps-validation wizard-notification" name="hotel-form" id="hotel-form"
                      enctype="multipart/form-data" action="{{ route('business-segment.membership.purchase') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="firstName3">
                                    @lang("$string_file.plan_name") :
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" name="membership_plan_id" id="membership_plan_id">
                                    <option>Choose Plan</option>
                                    @foreach($membershipPlan as $plan)
                                        <option value="{{$plan->id}}" {{isset($membershipPlanId) && !empty($membershipPlanId)? ($plan->id == $membershipPlanId ? 'selected' : '') : ''}}>{{$plan->plan_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('plan_name'))
                                    <label class="text-danger">{{ $errors->first('plan_name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 show-membership-data">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    @lang("$string_file.period")(in days) :
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="period"
                                       name="period" placeholder="" readonly>
                                @if ($errors->has('period'))
                                    <label class="text-danger">{{ $errors->first('period') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 show-membership-data">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    @lang("$string_file.max_amount_valid") :
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="max_amount_valid"
                                       name="max_amount_valid" placeholder="" readonly>
                                @if ($errors->has('max_amount_valid'))
                                    <label class="text-danger">{{ $errors->first('max_amount_valid') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 show-membership-data">
                            <div class="form-group">
                                <label for="location3">@lang("$string_file.order_limit") :</label>
                                <input type="text" class="form-control" id="order_limit"
                                name="order_limit" placeholder="" readonly>
                                @if ($errors->has('order_limit'))
                                    <label class="text-danger">{{ $errors->first('order_limit') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 show-membership-data">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    @lang("$string_file.price") :
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="price"
                                name="price" placeholder="" readonly>
                                @if ($errors->has('price'))
                                    <label class="text-danger">{{ $errors->first('price') }}</label>
                                @endif
                            </div>
                        </div>
                        @if(isset($countOrders) && $countOrders)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.orders_completed") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="orders_completed"
                                    name="orders_completed" placeholder="" value="{{$countOrders}}" readonly>
                                    @if ($errors->has('orders_completed'))
                                        <label class="text-danger">{{ $errors->first('orders_completed') }}</label>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if(isset($expiryDateForSubscription) && $expiryDateForSubscription)
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    @lang("$string_file.expiry_date") :
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="expiry_date"
                                name="expiry_date" placeholder="" value="{{$expiryDateForSubscription}}" readonly>
                                @if ($errors->has('expiry_date'))
                                    <label class="text-danger">{{ $errors->first('expiry_date') }}</label>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="form-actions float-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="wb-check-circle"></i> @lang("$string_file.save")
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
    $(document).ready(function() {
        $('.show-membership-data').hide();
        if($('#membership_plan_id').val()){
            var token = "{{csrf_token()}}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: "POST",
                url: "{{ route('business-segment.get-plan') }}",
                cache: false,
                data: {
                    plan_id: $('#membership_plan_id').val(),
                },
                success: function (response) {
                    console.log(response);
                    if (response.status === 'error') {
                        // Display error message
                        alert(response.message);
                    } else if (response.status === 'success') {
                        $('.show-membership-data').show();
                        $('#period').val(response.data.period);
                        $('#order_limit').val(response.data.order_limit);
                        $('#price').val(response.data.price);
                        $('#max_amount_valid').val(response.data.max_amount_valid);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle any other errors
                    console.error(xhr.responseText);
                    alert('An error occurred. Please try again.');
                }
            });
        }
        $('#membership_plan_id').change(function() {
            var selectedPlanId = $(this).val();
            var token = "{{csrf_token()}}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: "POST",
                url: "{{ route('business-segment.get-plan') }}",
                cache: false,
                data: {
                    plan_id: selectedPlanId,
                },
                success: function (response) {
                    console.log(response);
                    if (response.status === 'error') {
                        // Display error message
                        alert(response.message);
                    } else if (response.status === 'success') {
                        $('.show-membership-data').show();
                        $('#period').val(response.data.period);
                        $('#order_limit').val(response.data.order_limit);
                        $('#price').val(response.data.price);
                        $('#max_amount_valid').val(response.data.max_amount_valid);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle any other errors
                    console.error(xhr.responseText);
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });
</script>
@endsection