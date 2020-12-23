<!DOCTYPE html>
<html lang="en">
<head>
    <script>


        url1 = window.location.href;
        //"http://35.246.180.16:5000/index.php"
        domain = url1.split(":5000")[0]
        //"http://35.246.180.16"

        function blockEntry(){
            if (sessionStorage.getItem("role")!=="cinemaowner"){
                alert("Access Denied!")
                window.location.replace(domain+":5000/welcome.php");
            }
        }
        blockEntry();
        $(function () {
            $(".datepicker").datepicker({dateFormat: "yy-mm-dd"});
        });
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Cinema - Manage your Cinema!</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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
                <li class="nav-item">
                    <a class="nav-link" href="welcome.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="movies.php">Movies</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" onclick="navBarCinemaButton()">CinemaOwner
                        <span class="sr-only">(current)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" onclick="navBarAdminButton()">Administration</a>
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

<div class="container">
    <div id="alert" class="col-3">
        <div class="message"></div>
    </div>
    <div class="row">
        <div class="col-3"></div>
        <div class="col-9 card-columns"></div>

    </div>
</div>

<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">HMMY 2020</p>
    </div>
</footer>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>


    url1 = window.location.href;
    domain = url1.split(":5000")[0]

    checkLogged();
    findCinema();

    $("#usernavbar")[0].innerHTML = "Hello, " + sessionStorage.getItem("username") + "!"
    $("#nasur")[0].innerHTML = sessionStorage.getItem("surname") + " " + sessionStorage.getItem("name")

    $("#logout-btn").click(function () {
        sessionStorage.clear();
        window.location.replace(domain+":5000/index.php");
    });

    function checkLogged(){
        if(sessionStorage.getItem("accessToken")==""){
            sessionStorage.clear();
            window.location.replace(domain+":5000/index.php");
        }
    }

    function navBarAdminButton() {
        checkLogged();
        if (sessionStorage.getItem("role") !== 'admin') {
            alert("Access Denied!");
        } else {
            window.location.replace(domain+":7000/administration.php");
        }
    }

    function navBarCinemaButton() {
        checkLogged();
        if (sessionStorage.getItem("role") !== 'cinemaowner') {
            alert("Access Denied!");
        } else {
            window.location.replace(domain+":5000/cinemaowner.php");
        }
    }

    function createMovie() {
        checkLogged();
        isValid=true;
        var o = {
            dbcollection: "movies",
            title: document.getElementById("movieCreationTitle").value,
            startdate: document.getElementById("movieCreationStartDate").value,
            enddate: document.getElementById("movieCreationEndDate").value,
            category: document.getElementById("movieCreationCategory").value,
            cinema: sessionStorage.getItem("ownedCinema")
        }
        if(o.title=="" || o.startdate=="" || o.enddate=="" || o.category=="" || o.cinema==""){
            alertError("Fields cannot be empty")
            isValid=false;
        }
        if (isValid==true){
            $.ajax({
                type: "POST",
                url: domain+":7000/rest_resource_create.php",
                data: JSON.stringify(o)
            }).done(function () {
                console.log('created movie with title ' + o.title)
                $(".card-columns").find(".booking-card").remove()
                refreshCards()
                refreshDatePicker()
            })
        }
    }

    function alertError(msg){
        $("#alert").fadeIn();
        $("#alert").addClass("alert alert-danger")
        $("#alert").find('.message').text(msg);
        $("#alert").delay(3000).fadeOut("slow");
    } 

    function createCinema() {
        checkLogged();
        isValid=true;
        var cinemaName = document.getElementById("cinemaCreationName").value;
        var o = {
            dbcollection: "cinemas",
            cinema: cinemaName,
            owner: sessionStorage.getItem("username"),
        }
        if(o.cinema==""){
            alertError("Field Cannot be Empty")
            isValid=false;
        }
        if (isValid==true){
            $.ajax({
                type: "POST",
                url: domain+":7000/rest_resource_create.php",
                data: JSON.stringify(o)
            }).done(function (response) {
                sessionStorage.setItem("ownedCinema", cinemaName)
                console.log('created ciname with name ' + cinemaName)
                $(".card-columns").find(".booking-card").remove()
                $("#create-cinema-form").remove()
                refreshAddMovieDiv()
                refreshCards()

                refreshDatePicker()
            })
        }
    }

    function refreshAddMovieDiv() {
        checkLogged();
        $(".container").find(".row").find(".col-3").append(
            `<h2 class="my-4">Add Movie</h2>
            <div class="create-container">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input id="userid" type="hidden" class="form-control" value="<?php echo $_SESSION['id']?>" >
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input id="movieCreationTitle" type="text" class="form-control" name="title" placeholder="Enter title">
                    </div>
                    <div class="form-group">
                        <label for="startdate">Start Date</label>
                        <input id="movieCreationStartDate" type="text" class="form-control datepicker" name="startdate" placeholder="Enter from (YYYY-MM-DD)">
                    </div>
                    <div class="form-group">
                        <label for="enddate">End date</label>
                        <input  id="movieCreationEndDate" type="text" class="form-control datepicker" name="enddate" placeholder="Enter until (YYYY-MM-DD)">
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input  id="movieCreationCategory" type="text" class="form-control" name="category" placeholder="Enter category">
                    </div>
                    <input id="add-btn" onclick="createMovie()" class="btn btn-primary" type="button" name="create" value="create">
                </form>
            </div>`
        );
    }

    function refreshDatePicker() {
        $(".datepicker").datepicker({dateFormat: "yy-mm-dd"});
    }

    function deleteMovie(id,title,cinema) {
        checkLogged();
        var o = {
            dbcollection: "movies",
            id: id,
            title: title,
            cinema: cinema
        }
        $.ajax({
            type: "DELETE",
            url: domain+":7000/rest_resource_delete.php",
            data: JSON.stringify(o)
        }).done(function () {
            refreshCards();
        })
    }

    function findCinema() {
        checkLogged();
        console.log("here")
        $.ajax({
            type: "GET",
            url: domain+`:7000/rest_resource_read.php/cinemas?owner=${sessionStorage.getItem("username")}`
        }).done(function (response) {
            if (response.length === 0) {
                cinemaname = ""
                $(".container").find(".row").find(".col-3").append(
                    `<form id="create-cinema-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="title">Cinema Name</label>
                        <input id="cinemaCreationName" type="text" class="form-control" name="cinemacreate" placeholder="Cinema Name">
                    </div>
                    <input id="add-cinema-btn" class="btn btn-primary btn-sm" name="createcinema" onclick="createCinema()" type="button" value="create">
                </form>`
                )
            } else {
                cinemaname = response[0].cinema;
                sessionStorage.setItem("ownedCinema", cinemaname)
                refreshAddMovieDiv();
                refreshCards();
            }
            refreshDatePicker()
        })
    }

    function updatecard(id) {
        checkLogged();
        var o = {
            dbcollection: "movies",
            id: id,
            title: $(`.obj${id} input[name='title']`)[0].value,
            startdate: $(`.obj${id} input[name='startdate']`)[0].value,
            enddate: $(`.obj${id} input[name='enddate']`)[0].value,
            category: $(`.obj${id} input[name='category']`)[0].value,
            cinema: $(`.obj${id} input[name='cinemaname']`)[0].value,
        }
        $.ajax({
            type: "PUT",
            url: domain+":7000/rest_resource_update.php",
            data: JSON.stringify(o)
        }).done(function () {
            refreshCards();
            refreshDatePicker()
        })
    }

    function refreshCards() {
        checkLogged();
        console.log("here")
        $.ajax({
            type: "GET",
            url: domain+":7000/rest_resource_read.php/movies?cinema=" + sessionStorage.getItem("ownedCinema")
        }).done(function (response) {
            console.log("1312");
            $(".card-columns").find(".booking-card").remove()
            for (var row of response) {
                add_card(row)
            }
            refreshDatePicker()
        })
    }

    function add_card(obj) {
        checkLogged();
        $(".card-columns").append(`<div class="card booking-card mt-2 mb-4">
        <div class="view overlay">
            <img class="card-img-top" src="https://dummyimage.com/400x200/444444/ffffff&amp;text=${obj.title}" alt="Card image cap">
            <a href="#!">
                <div class="mask rgba-white-slight"></div>
            </a>
        </div>
        <div class="card-body obj${obj.id}">
            <form action="/cinemaowner.php" method="post">
                <div class="form-group">
                    <label for="id">ID</label>
                    <input type="text" class="form-control" name="id" value="${obj.id}" readonly="">
                    <small class="form-text text-muted">This is the id of the movie. Cannot edit.</small>
                </div>
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" name="title" value="${obj.title}">
                </div>
                <div class="form-group">
                    <label for="startdate">Start Date</label>
                    <input type="text" class="form-control datepicker hasDatepicker" name="startdate" value="${obj.startdate}" id="dp1607969343266">
                </div>
                <div class="form-group">
                    <label for="enddate">End date</label>
                    <input type="text" class="form-control datepicker hasDatepicker" name="enddate" value="${obj.enddate}" id="dp1607969343267">
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" class="form-control" name="category" value="${obj.category}">
                </div>
                <div class="form-group">
                    <label for="cinemaname">Cinema</label>
                    <input type="text" class="form-control" name="cinemaname" value="${obj.cinema}" readonly="">
                </div>
                <input class="btn btn-primary btn-danger btn-sm" name="delete" onclick="deleteMovie('${obj.id}','${obj.title}','${obj.cinema}')" type="button" value="delete">
                <input class="btn btn-primary btn-sm" name="update" onclick="updatecard('${obj.id}')" type="button" value="update">
            </form>
        </div>
    </div>`)
    }
</script>

</body>

</html>
