<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Interswitch</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body onload ='form1.submit()'>
<div class="container">
    <h2>Payment Details</h2>
    <form action="{{$payment_transaction['ACSUrl']}}" method="POST" name="form1" id="form1">
        <div class="form-group">
            <input type="text" class="form-control" id="TermUrl" value="{{$payment_transaction['TermUrl']}}" name="TermUrl">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="MD" value="{{$payment_transaction['MD']}}" name="MD">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="PaReq" value="{{$payment_transaction['PaReq']}}" name="PaReq">
        </div>
    </form>
</div>
</body>
</html>