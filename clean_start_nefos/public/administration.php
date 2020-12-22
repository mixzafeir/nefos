<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - Manage your website!</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>

        url1 = window.location.href;
        //"http://35.246.180.16:5000/index.php"
        domain = url1.split(":5000")[0]
        //"http://35.246.180.16"


        function blockEntry(){
            if (sessionStorage.getItem("role")!=="admin"){
                alert("Access Denied!")
                window.location.replace(domain+":5000/welcome.php");
            }
        }
        blockEntry();

        $(function () {
            $("#datepicker").datepicker({dateFormat: "yy-mm-dd"});
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
<div class="container">
    <div class="row">

        <div class="col-12 card-columns">

        </div>
    </div>
</div>

<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">HMMY 2020</p>
    </div>
</footer>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>

    $(document).ready(function () {
        $("#alert-box").delay(3000).fadeOut("slow");
    });

</script>

<script>
    url1 = window.location.href;
    domain = url1.split(":5000")[0]


    $("#usernavbar")[0].innerHTML = "Hello, " + sessionStorage.getItem("username") + "!"
    $("#nasur")[0].innerHTML = sessionStorage.getItem("surname") + " " + sessionStorage.getItem("name")

    checkLogged();
    refreshCards();

    function checkLogged(){
        if(sessionStorage.getItem("accessToken")==""){
            sessionStorage.clear();
            window.location.replace(domain+":5000/index.php");
        }
    }

    function updateCard(id, pass) {
        checkLogged();
        var o = {
            dbcollection: "users",
            id: id,
            name: $(`.obj${id} input[name='name']`)[0].value,
            surname: $(`.obj${id} input[name='surname']`)[0].value,
            password: pass,
            username: $(`.obj${id} input[name='username']`)[0].value,
            email: $(`.obj${id} input[name='email']`)[0].value,
            role: $(`.obj${id} input[name='role']`)[0].value,
            confirmed: $(`.obj${id} select[name='confirmed']`)[0].value
        }
        $.ajax({
            type: "PUT",
            url: domain+":7000/rest_resource_update.php",
            data: JSON.stringify(o)
        }).done(function (response) {
            refreshCards();
        })
    }

    function deleteUser(id,username) {
        checkLogged();
        var o = {
            dbcollection: "users",
            id: id,
            username: username
        }
        $.ajax({
            type: "DELETE",
            url: domain+":7000/rest_resource_delete.php",
            data: JSON.stringify(o)
        }).done(function (response) {
            alert("User deleter successfully!")
            refreshCards();
        })
    }


    function adminButton() {
        checkLogged();
        if (sessionStorage.getItem("role") !== 'admin') {
            alert("Access Denied!");
        } else {
            window.location.replace(domain+":5000/administration.php");
        }
    }

    function cinemaButton() {
        checkLogged();
        if (sessionStorage.getItem("role") !== 'cinemaowner') {
            alert("Access Denied!");
        } else {
            window.location.replace(domain+":5000/cinemaowner.php");
        }
    }

    $("#logout-btn").click(function () {
        sessionStorage.clear();
        window.location.replace(domain+":5000/index.php");
    });

    function refreshCards() {
        checkLogged();
        $.ajax({
            type: "GET",
            url: domain+":7000/rest_resource_read.php/users"
        }).done(function (response) {
            $(".card-columns").find(".booking-card").remove()
            for (var row of response) {
                addCard(row)
            }
        })
    }


    function addCard(obj) {
        checkLogged();
        var temp;
        var show1;
        var show2;
        if (obj.confirmed == 1) {
            temp = 0;
            show1 = "confirmed";
            show2 = "declined";
        } else if (obj.confirmed == 0) {
            temp = 1;
            show1 = "declined";
            show2 = "confirmed";
        }
        $(".card-columns").append(`<div class="card booking-card mt-2 mb-4">
                <div class="view overlay">
                    <img class="card-img-top"
                         src="https://dummyimage.com/400x200/444444/ffffff&text=${obj.username}"
                         alt="Card image cap">
                    <a href="#!">
                        <div class="mask rgba-white-slight"></div>
                    </a>
                </div>
                <div class="card-body obj${obj.id}">
                    <form action="/administration.php" method="post">
                        <div class="form-group">
                            <label for="id">ID</label>
                            <input type="text" class="form-control" name="id" value="${obj.id}"
                                   readonly>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" name="username"
                                   value="${obj.username}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" value="${obj.name}"
                                   readonly>
                        </div>
                        <div class="form-group">
                            <label for="surname">Surname</label>
                            <input type="text" class="form-control" name="surname"
                                   value="${obj.surname}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" name="email"
                                   value="${obj.email}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <input type="text" class="form-control" name="role"
                                   value="${obj.role}" readonly>
                        </div>
                        <select id="confirmed" name="confirmed">
                            <option value="${obj.confirmed}">${show1}</option>
                            <option value="${temp}">${show2}</option>
                        </select> 
                        <input class="btn btn-primary btn-danger" name="delete" onclick="deleteUser('${obj.id}','${obj.username}')" value="delete">
                        <input class="btn btn-primary" name="update" onclick="updateCard('${obj.id}','${obj.password}')" value="update">
                    </form>
                </div>
            </div>`)
    }

</script>
</body>

</html>