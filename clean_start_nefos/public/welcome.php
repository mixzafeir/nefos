<?php
// require_once "config.php";
// session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Cinema - Manage your Cinema!</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function () {
            $(".datepicker").datepicker({dateFormat: "yy-mm-dd"});
        });
    </script>
</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a id="usernavbar" class="navbar-brand" href="#"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="welcome.php">Home
                        <span class="sr-only">(current)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="movies.php">Movies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" onclick="cinemaButton()">CinemaOwner</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" onclick="adminButton()">Administration</a>
                </li>
                <li class="nav-item">
                    <span id="nasur" class="nav-link active"></span>
                </li>
                <form method="post">
                <li class="nav-item">
                    <input id="logout-btn" class="btn btn-danger geia" name="logout" type="button" value="Log out"/>
                </li>
                </form>
            </ul>
        </div>
    </div>
</nav>
<h1 class="text-center">Welcome to the Movie App!</h1>
<a href="movies.php" class="btn btn-dark btn-lg btn-block active" role="button" aria-pressed="true">Our Movies</a>
<a class="btn btn-warning btn-lg btn-block active" role="button" onclick="cinemaButton()" aria-pressed="true">Manage Your Cinema (cinema owners only)</a>
<a class="btn btn-danger btn-lg btn-block active" role="button" onclick="adminButton()" aria-pressed="true">Manage the Website (admins only)</a>

<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">HMMY 2020</p>
    </div>
</footer>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>


    url = window.location.href;
    domain = url.split(":5000")[0]
    
    checkLogged()


    $("#usernavbar")[0].innerHTML="Hello, "+sessionStorage.getItem("username")+"!"
    $("#nasur")[0].innerHTML=sessionStorage.getItem("surname")+" "+sessionStorage.getItem("name")

    $("#logout-btn").click(function(){
        sessionStorage.clear();
        window.location.replace (domain+":5000/index.php");
    });

    function checkLogged(){
        if(sessionStorage.getItem("accessToken")==""){
            sessionStorage.clear();
            window.location.replace(domain+":5000/index.php");
        }
    }

    function adminButton(){
        checkLogged()
        if (sessionStorage.getItem("role") !== 'admin') {
            alert("Access Denied!");
        }else{
            window.location.replace (domain+":5000/administration.php");
        }
    }

    function cinemaButton(){
        checkLogged()
        if (sessionStorage.getItem("role") !== 'cinemaowner') {
            alert("Access Denied!");
        }else{
            window.location.replace (domain+":5000/cinemaowner.php");
        }
    }

    $(document).ready(function () {
        $("#alert-box").delay(3000).fadeOut("slow");
    });
    $(document).ready(function() {
        $(".dropdown-toggle").dropdown();
    }); 
</script>
</body>

</html>