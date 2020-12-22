
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
                <h2>Have an account?</h2>
                <a id="label-login" for="log-login-show" href="index.php">Login</a>

            </div>               
            <div class="white-panel">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h1>REGISTER</h1>    
                <div class="login-show">
                    <input type="text" placeholder="Enter your name" name="name" class="form-control" value="">
                    <span class="help-block"></span>
                    <input type="text" placeholder="Enter your surname" name="surname" class="form-control" value="">
                    <span class="help-block"></span>
                    <input type="text" placeholder="Enter your username" name="username" class="form-control" value="">
                    <span class="help-block"></span>
                    <input type="password" placeholder="Enter your password" name="password" class="form-control" value="">
                    <span class="help-block"></span>
                    <input type="password" placeholder="Enter the same password" name="confirm_password" class="form-control" value="">
                    <span class="help-block"></span>
                    <input type="email" placeholder="Enter your e-mail" name="email" class="form-control" value="">
                    <span class="help-block"></span>
                    <label for="role">Sign up as:</label>
                    <select id="role" name="role">
                        <option value="user">User</option>
                        <option value="cinemaowner">Cinema Owner</option>
                        <option value="admin">Admin</option>
                    </select> 
                    <span class="help-block"></span>
                </div>
                <input id="add-btn" class="btn btn-primary" name="create" type="button" value="create">   
                <div id="alert">
                    <div class="message"></div>
                </div>
            </div>
    </div>
</body>
<script>


url1 = window.location.href;
    //"http://35.246.180.16:5000/index.php"
domain = url1.split(":5000")[0]
    //"http://35.246.180.16"

function alertError(msg){
    $("#alert").fadeIn();
    $("#alert").addClass("alert alert-danger")
    $("#alert").find('.message').text(msg);
    $("#alert").delay(3000).fadeOut("slow");
}


$( "#add-btn" ).click(function() {
    password2=$(".login-show input[name='confirm_password']")[0].value
    var o = {
        dbcollection: "users",
        name: $(".login-show input[name='name']")[0].value,
        surname: $(".login-show input[name='surname']")[0].value,
        username: $(".login-show input[name='username']")[0].value,
        password: $(".login-show input[name='password']")[0].value,
        email: $(".login-show input[name='email']")[0].value,
        role: $(".login-show select[name='role']")[0].value,
        confirmed:0
    }
    isValid=true;
    username1=o.username
    if(o.name=="" || o.surname=="" || o.username=="" || o.password=="" || o.email==""){
        alertError("Fields cannot be empty")
        isValid=false;
    }
    if(o.password!=password2){
        alertError("Passwords do not match")
        isValid=false;
    }
    if(isValid==true){
        $.post(domain+':7000/rest_resource_create.php',JSON.stringify(o)).done(function(response){
            window.location.replace (domain+":5000/index.php");
        }).fail(function(response){
            alertError("Username already in use!")
        })
    }




    // $.ajax({
    //     type: "GET",
    //     url: domain+":7000/rest_resource_read.php/users"
    //     }).done(function (response) {
    //         console.log(isValid)
    //         for (var row of response) {
    //             if(o.username==row.username){
    //                 isValid=false;
    //                 alertError("Username already in use")
    //             }
    //         }
    //         if(isValid==true){
    //             $.post(domain+':7000/rest_resource_create.php',JSON.stringify(o),function(){

    //                 window.location.replace (domain+":5000/index.php");
    //             })
    //         }
    // })
});


$(document).ready(function(){
    $('.login-info-box').fadeOut();
    $('.login-show').addClass('show-log-panel');
});


$('.login-reg-panel input[type="radio"]').on('change', function() {
    if($('#log-login-show').is(':checked')) {
        $('.register-info-box').fadeOut(); 
        $('.login-info-box').fadeIn();
        
        $('.white-panel').addClass('right-log');
        $('.register-show').addClass('show-log-panel');
        $('.login-show').removeClass('show-log-panel');
        
    }
    else if($('#log-reg-show').is(':checked')) {
        $('.register-info-box').fadeIn();
        $('.login-info-box').fadeOut();
        
        $('.white-panel').removeClass('right-log');
        
        $('.login-show').addClass('show-log-panel');
        $('.register-show').removeClass('show-log-panel');
    }
});
  
</script>
</html>




