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
            <li class="active"><a href="#">Home</a></li>
            <li class=""><a href="{{route("sp-admin.other-db.copy")}}">Copy Other DB Data</a></li>
            <li class=""><a href="{{route("sp-admin.out")}}">Logout</a></li>
        </ul>
    </div>
</nav>
<div class="container">
    @include('merchant.shared.errors-and-messages')
    <h2>Merchant Copy</h2>
    <br>
    <form method="POST" class="form steps-validation wizard-notification"
          enctype="multipart/form-data"
          id="merchant-copy-form" ,
          name="merchant-copy-form"
          action="{{ route('sp-admin.merchant.copy') }}">
        @csrf
        <h4>Basic Detail</h4>
        <hr>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="business_name">Business Name:</label>
                {!! Form::text("business_name", old("business_name"), array("class" => "form-control", "id" => "business_name", "placeholder" => "Business Name", "required")) !!}
            </div>
            <div class="form-group col-md-4">
                <label for="owner_first_name">Owner First Name:</label>
                {!! Form::text("owner_first_name", old("owner_first_name"),array("class" => "form-control", "id" => "owner_first_name", "placeholder" => "Owner First Name", "required")) !!}
            </div>
            <div class="form-group col-md-4">
                <label for="owner_last_name">Owner Last Name:</label>
                {!! Form::text("owner_last_name", old("owner_last_name"),array("class" => "form-control", "id" => "owner_last_name", "placeholder" => "Owner Last Name", "required")) !!}
            </div>
            <div class="form-group col-md-4">
                <label for="email">Email:</label>
                {!! Form::email("email", old("email"),array("class" => "form-control", "id" => "email", "placeholder" => "Email", "required")) !!}
            </div>
            <div class="form-group col-md-4">
                <label for="password">Password:</label>
                {!! Form::password("password",array("class" => "form-control", "id" => "password", "required")) !!}
            </div>
            <div class="form-group col-md-4">
                <label for="confirm_password">Confirm Password:</label>
                {!! Form::text("confirm_password", old("confirm_password"),array("class" => "form-control", "id" => "confirm_password", "required")) !!}
            </div>
            <div class="form-group col-md-4">
                <label for="address">Address:</label>
                {!! Form::text("address", old("address"),array("class" => "form-control", "id" => "address", "placeholder" => "Adress", "required")) !!}
            </div>
            <div class="form-group col-md-4">
                <label for="phone">Phone:</label>
                {!! Form::text("phone", old("phone"),array("class" => "form-control", "id" => "phone", "placeholder" => "Phone", "required")) !!}
            </div>
            <div class="form-group col-md-4">
                <label for="phone">Business Logo:</label>
                {!! Form::file("business_logo",array("class" => "form-control", "id" => "business_logo", "required")) !!}
            </div>
        </div>
        <h4>Source Merchant</h4>
        <hr>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="merchant_id">Merchant:</label>
                {!! Form::select("merchant_id", $merchants, old("merchant_id"),array("class" => "form-control select2", "id" => "merchant_id", "required")) !!}
            </div>
        </div>
        <h4>Select Source Merchant Tables</h4>
        <hr>
        <div class="row" id="tables_div" >
            <div class="form-group col-md-12">
                <label for="merchant_id">Select Table</label>
                <input type="checkbox" name="countries" id="countries"> &nbsp; Countries,
                <input type="checkbox" name="documents" id="documents"> &nbsp; Documents,
                <input type="checkbox" name="vehicle_types" id="vehicle_types"> &nbsp; Vehicle Types,
                <input type="checkbox" name="vehicle_make" id="vehicle_make"> &nbsp; Vehicle Makes,
                <input type="checkbox" name="vehicle_models" id="vehicle_models"> &nbsp; Vehicle Models,
                <input type="checkbox" name="account_types" id="account_types"> &nbsp; Account Type,
                <input type="checkbox" name="pricing_parameter" id="pricing_parameter"> &nbsp; Pricing Parameter
                
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
</script>
</body>
</html>
