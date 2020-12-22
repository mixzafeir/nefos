<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="stylehome.css" rel="stylesheet">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>

<div class="login-reg-panel">
    <div class="register-info-box">
        <h2>Don't have an account?</h2>
        <a id="label-login" for="log-login-show" href="register.php">Register</a>
    </div>
    <div class="white-panel">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h1>LOGIN</h1>
            <div class="login-show">
                <input type="text" placeholder="Enter your username" name="username" class="form-control"
                       value="">
                <span class="help-block"></span>
                <input type="password" placeholder="Enter your password" name="password" class="form-control">
                <span class="help-block"></span>
            </div>
            <input id="add-btn" class="btn btn-primary" type="button" name="create" value="Login">
        </form>
        <div id="alert">
        <div class="message"></div>
        </div>
    </div>
</div>

</body>
<script>


    $("#add-btn").click(function () {
        var o = {
            username: $(".login-show input[name='username']")[0].value,
            password: $(".login-show input[name='password']")[0].value
        }

        url = window.location.href;
        domain = url.split(":5000")[0]
        apiurl = domain + ":7000/login_api.php"

        $.post(apiurl, JSON.stringify(o), function (data) {
            if(data.message=="Invalid login"){
                $("#alert").addClass("alert alert-danger")
                $("#alert").find('.message').text("Incorrect username or password");
                $("#alert").delay(3000).fadeOut("slow");
            }
            if (data.user != null) {
                sessionStorage.setItem("username", data.user.username);
                sessionStorage.setItem("name", data.user.name);
                sessionStorage.setItem("surname", data.user.surname);
                sessionStorage.setItem("role", data.user.role);
                sessionStorage.setItem("confirmed", data.user.confirmed);
                sessionStorage.setItem("accessToken", data.user.accessToken);
                console.log(sessionStorage.getItem("confirmed"));
                if (sessionStorage.getItem("confirmed") == "1") {
                    window.location.replace(domain+":5000/welcome.php");
                } else {
                    sessionStorage.clear();
                    window.location.replace(domain+":5000/index.php");
                }

            }
            // else {
            //     window.location.replace("http://172.28.1.2/index.php");
            // }
        })
    });



    $(document).ready(function () {
        $('.login-info-box').fadeOut();
        $('.login-show').addClass('show-log-panel');
    });


    $('.login-reg-panel input[type="radio"]').on('change', function () {
        if ($('#log-login-show').is(':checked')) {
            $('.register-info-box').fadeOut();
            $('.login-info-box').fadeIn();

            $('.white-panel').addClass('right-log');
            $('.register-show').addClass('show-log-panel');
            $('.login-show').removeClass('show-log-panel');

        } else if ($('#log-reg-show').is(':checked')) {
            $('.register-info-box').fadeIn();
            $('.login-info-box').fadeOut();

            $('.white-panel').removeClass('right-log');

            $('.login-show').addClass('show-log-panel');
            $('.register-show').removeClass('show-log-panel');
        }
    });

</script>
</html>