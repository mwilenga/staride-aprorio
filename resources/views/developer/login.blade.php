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
    <title>Developer Tools - Login</title>
</head>
<body>
<div class="container">
    @include('merchant.shared.errors-and-messages')
    <h2>Login</h2>
    <form method="POST" class="form steps-validation wizard-notification"
          enctype="multipart/form-data"
          id="sp-login-form" ,
          name="sp-login-form"
          action="{{ route('developer.entry') }}">
        @csrf
        <div class="form-group">
            <label for="pwd">Pin:</label>
            <input type="password" class="form-control" id="pin" placeholder="Enter Access Pin" name="pin">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
    </form>
</div>
</body>
</html>
