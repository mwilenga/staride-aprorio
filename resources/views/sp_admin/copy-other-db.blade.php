<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <title>Sp Dashboard</title>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">SP Admin</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="#">Home</a></li>
            <li class="active"><a href="{{route("sp-admin.other-db.copy")}}">Copy Other DB Data</a></li>
            <li class=""><a href="{{route("sp-admin.out")}}">Logout</a></li>
        </ul>
    </div>
</nav>
<div class="container">
    @include('merchant.shared.errors-and-messages')
    <h2>Other DB Merchant Copy</h2>
    <br>
    <form method="POST" class="form steps-validation wizard-notification"
          enctype="multipart/form-data"
          id="merchant-copy-form" ,
          name="merchant-copy-form"
          action="{{ route('sp-admin.source.uploadDB') }}">
        @csrf
        <h4>Source Connection DB Detail</h4>
        <hr>
        <div class="row">
            <div class="form-group col-md-3">
                <label for="ip_address">IP Address</label>
                {!! Form::text("ip_address", old("ip_address"), array("class" => "form-control", "id" => "ip_address", "placeholder" => "IP Address", "required")) !!}
            </div>
            <div class="form-group col-md-3">
                <label for="username">Username</label>
                {!! Form::text("username", old("username"),array("class" => "form-control", "id" => "username", "placeholder" => "Username", "required")) !!}
            </div>
            <div class="form-group col-md-3">
                <label for="password">Password</label>
                {!! Form::text("password", old("password"),array("class" => "form-control", "id" => "password", "placeholder" => "Password", "required")) !!}
            </div>
            <div class="form-group col-md-3">
                <label for="db_name">DB Name</label>
                {!! Form::text("db_name", old("db_name"),array("class" => "form-control", "id" => "db_name", "placeholder" => "DB Name", "required")) !!}
            </div>
            <div class="from-group col-md-3">
                <lable for="source_merchant_id">
                    <select class="form-control" name="source_merchant_id" id="source_merchant_id" disabled>
                        <option value="">--Select Source Merchant--</option>
                    </select>
                </lable>
            </div>
            <div class="form-group col-md-3">
                <button type="button" id="fetch_db_button" class="btn btn-primary">Connect</button>
            </div>
        </div>
        <h4>Select Source Merchant Tables</h4>
        <hr>
        <div class="row" id="tables_div" style="display:none">
            <div class="form-group col-md-12">
                <label for="merchant_id">Select Table</label>
                <input type="checkbox" name="countries" id="countries"> &nbsp; Countries,
                <input type="checkbox" name="documents" id="documents"> &nbsp; Documents,
                <input type="checkbox" name="vehicle_types" id="vehicle_types"> &nbsp; Vehicle Types,
                <input type="checkbox" name="vehicle_make" id="vehicle_make"> &nbsp; Vehicle Makes,
                <input type="checkbox" name="vehicle_models" id="vehicle_models"> &nbsp; Vehicle Models,
                <input type="checkbox" name="account_types" id="account_types"> &nbsp; Account Type,
                <input type="checkbox" name="users" id="users"> &nbsp; Users,
                <input type="checkbox" name="drivers" id="drivers"> &nbsp; Manual Drivers Import,
                <input type="checkbox" name="copy_store_product" id="copy_store_product"> &nbsp; Manual Copy Store and Product(v2 to v3),
            </div>
        </div>
        <h4>Target Merchant</h4>
        <hr>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="target_merchant_id">Merchant:</label>
                {!! Form::select("target_merchant_id", $merchants, old("target_merchant_id"),array("class" => "form-control select2", "id" => "target_merchant_id", "required")) !!}
            </div>
        </div>
        <button type="submit" class="btn btn-default">Create</button>
    </form>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            closeOnSelect: false
        });
    });

    $(document).on('click','#fetch_db_button', function(){
        var token = $('[name="_token"]').val();
        var ip = $('#ip_address').val();
        var username = $('#username').val();
        var password = $('#password').val();
        var db_name = $('#db_name').val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': token
            },
            method: 'POST',
            url: "{{route('sp-admin.source.connectDB')}}",
            data: {
                ip: ip,
                username: username,
                password: password,
                db_name: db_name,
            },
            success: function (data) {
                console.log(data);

                if(data.result == 1){
                    swal(data.message);
                    $('#tables_div').show();
                    $('#source_merchant_id').html(data.merchants);
                    $('#source_merchant_id').removeAttr('disabled');
                    $("#fetch_db_button").prop('disabled', true);
                }
                //     var priceCardFare = parseInt(data.amount);
                //     $('#price_card_id').val(data.price_card_id);
                //     // console.log('priceCardFare :'+priceCardFare);
                //     var outstationFareTyep = $('#outstation_type_val').val();
                //     // console.log('outstationFareTyep :'+outstationFareTyep);
                //     if(service == 4 && $('#outstation_type_val').val() == 1){
                //         priceCardFare = priceCardFare *2;
                //     }

                //     var manualPrice = parseInt($('#price_for_ride_value').val());
                //     var iso = document.getElementById("isocode").value;
                //     var estimate = "Fare Estimate : "+iso+" "+ priceCardFare;
                //     $('#estimate_fare').val(priceCardFare);
                //     if($('#price_for_ride').val() == 2){
                //         estimate = "Fare Estimate : "+iso+" "+ manualPrice;
                //         $('#estimate_fare').val(manualPrice);
                //     }else if($('#price_for_ride').val() == 3){
                //         if(priceCardFare > manualPrice){
                //             estimate = "Fare Estimate : "+iso+" "+ manualPrice;
                //             $('#estimate_fare').val(manualPrice);
                //         }else{
                //             estimate = "Fare Estimate : "+iso+" "+ priceCardFare;
                //             $('#estimate_fare').val(priceCardFare);
                //         }
                //     }
                //     // console.log('fare estimate : '+estimate);
                //     $('#estimate_fare_ride').html(estimate);

                //     var price_card_id = $('#price_card_id').val();
                //     // console.log('price_card_id : '+price_card_id);
                //     // console.log('area : '+area);
                //     $.ajax({
                //         headers: {
                //             'X-CSRF-TOKEN': token
                //         },
                //         method: 'POST',
                //         url: "getPromoCode",
                //         data: {
                //             price_card_id: price_card_id,
                //             manual_area: area,
                //         },
                //         success: function (data) {
                //             $('#promo_code').html(data);
                //         }
                //     });
                // }else{
                //     swal(data.message);
                // }
            }
        });
    });
</script>
</body>
</html>
