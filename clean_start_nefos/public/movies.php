<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Movies - Manage your movies!</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function () {
            $("#datepicker").datepicker({dateFormat: "yy-mm-dd"});
        });
    </script>
</head>

<body>

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
                <li class="nav-item active">
                    <a class="nav-link" href="movies.php">Movies
                        <span class="sr-only">(current)</span>
                    </a>
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
        <div class="col-3">
            <h2 class="my-4">Your favorites</h2>
            <div id="favorites-list">
                <div class="list-group">
                    <span class="fav-list-elem"></span>
                </div>
            </div>
            <div class="search-container">
                <h2 class="my-4">Search</h2>
                <form id="search-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-row align-items-center">
                        <div class="col-auto">
                            <label class="sr-only" for="Title">Title</label>
                            <input id="searchTitle" type="text" class="form-control mb-2" name="title"
                                   placeholder="Enter title">
                        </div>
                        <div class="col-auto">
                            <label class="sr-only" for="Date">Date</label>
                            <input type="text" class="form-control mb-2 datepicker" name="date"
                                   placeholder="Enter date YYYY-MM-DD" id="searchDate">
                        </div>
                        <div class="col-auto">
                            <label class="sr-only" for="Category">Category</label>
                            <input id="searchCategory" type="text" class="form-control mb-2" name="category"
                                   placeholder="Enter category">
                        </div>
                        <div class="col-auto">
                            <label class="sr-only" for="Cinema">Cinema</label>
                            <input id="searchCinema" type="text" class="form-control mb-2" name="cinema"
                                   placeholder="Enter cinema">
                        </div>
                        <div class="col-auto">
                            <span onclick="setFilter()" class="btn btn-primary mb-2">Submit</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-9 card-columns">


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

    url1 = window.location.href;
    domain = url1.split(":5000")[0]
    refreshDatePicker()
    refreshFavs()
    checkLogged()

    $('document').ready(function () {
        refreshCards()
    });


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


    function adminButton() {
        checkLogged()
        if (sessionStorage.getItem("role") !== 'admin') {
            alert("Access Denied!");
        } else {
            window.location.replace(domain+":5000/administration.php");
        }
    }

    function cinemaButton() {
        checkLogged()
        if (sessionStorage.getItem("role") !== 'cinemaowner') {
            alert("Access Denied!");
        } else {
            window.location.replace(domain+":5000/cinemaowner.php");
        }
    }

    function refreshFavs() {
        checkLogged()
        favorites = []
        $.ajax({
            type: "GET",
            url: domain+":7000/rest_resource_read.php/favorites?user=" + sessionStorage.getItem("username")
        }).done(function (response) {
            $("#favorites-list").find(".list-group").find(".fav-list-elem").remove()
            for (var row of response) {
                showFavs(row.title)
                favorites.push(row.title)
            }
        })
    }

    function showFavs(obj) {
        $("#favorites-list").find(".list-group").append(
            `<span class=fav-list-elem>${obj}</span>`
        )
    }

    function refreshDatePicker() {
        $(".datepicker").datepicker({dateFormat: "yy-mm-dd"});
    }

    function refreshCards() {
        checkLogged()
        url = domain+":7000/rest_resource_read.php/movies"
        if (sessionStorage.getItem("filter") != null) {
            url = domain+":7000/rest_resource_read.php/movies" + sessionStorage.getItem("filter");
        }
        $.ajax({
            type: "GET",
            url: url
        }).done(function (response) {
            sessionStorage.setItem("filter", "")
            $(".card-columns").find(".booking-card").remove()
            for (var row of response) {
                addCard(row)
                refreshDatePicker()
            }
        })
    }

    function setFilter() {
        checkLogged()
        urlExtension = ""
        has = false
        if (document.getElementById("searchTitle") != null) {
            title = document.getElementById("searchTitle").value;
            if (title != null && title !== "") {
                urlExtension += "?title=" + title
                has = true
            }
        }
        if (document.getElementById("searchDate") != null) {
            date = document.getElementById("searchDate").value;
            if (date != null && date !== "") {
                if (has) {
                    urlExtension += "&date=" + date
                } else {
                    urlExtension += "?date=" + date
                    has = true
                }
            }
        }
        if (document.getElementById("searchCategory") != null) {
            category = document.getElementById("searchCategory").value;
            if (category != null && category !== "") {
                if (has) {
                    urlExtension += "&category=" + category
                } else {
                    urlExtension += "?category=" + category
                    has = true
                }
            }
        }
        if (document.getElementById("searchCinema") != null) {
            cinema = document.getElementById("searchCinema").value;
            if (cinema != null && cinema !== "") {
                if (has) {
                    urlExtension += "&cinema=" + cinema
                } else {
                    urlExtension += "?cinema=" + cinema
                    has = true
                }
            }
        }
        sessionStorage.setItem("filter", urlExtension)
        refreshCards()
    }

    function addToFav(id) {
        checkLogged()
        var o = {
            dbcollection: "favorites",
            title: $(`.obj${id} input[name='title']`)[0].value,
            cinema: $(`.obj${id} input[name='cinemaname']`)[0].value,
            user: sessionStorage.getItem("username")
        }
        $.post(domain+":7000/rest_resource_create.php", JSON.stringify(o), function () {
            refreshFavs();
            refreshCards()
        })
    }

    function removeFromArray(ar, el) {
        checkLogged()
        index = ar.indexOf(el);
        if (index > -1) {
            ar.splice(index, 1);
        }
    }

    function removeFromFav(title) {
        checkLogged()
        user = sessionStorage.getItem("username")
        var o = {
            dbcollection: "favorites",
            title: title,
            user: user,
        }
        $.ajax({
            type: "DELETE",
            url: domain+":7000/rest_resource_delete.php",
            data: JSON.stringify(o)
        }).done(function (response) {
            refreshFavs()
            refreshCards();
            refreshDatePicker()
        })
    }

    function addCard(obj) {
        checkLogged()
        if (favorites.includes(obj.title)) {
            favBtn = `<span class="btn btn-danger btn-sm" onclick="removeFromFav('${obj.title}')">Unfavorite</span>`
        } else {
            favBtn = `<span class="btn btn-primary btn-sm" onclick="addToFav('${obj.id}')">favorite</span>`
        }
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
                    <input type="text" class="form-control" name="title" value="${obj.title}" readonly="">
                </div>
                <div class="form-group">
                    <label for="startdate">Start Date</label>
                    <input type="text" class="form-control datepicker hasDatepicker" name="startdate" value="${obj.startdate}" id="dp1607969343266" readonly="">
                </div>
                <div class="form-group">
                    <label for="enddate">End date</label>
                    <input type="text" class="form-control datepicker hasDatepicker" name="enddate" value="${obj.enddate}" id="dp1607969343267" readonly="">
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" class="form-control" name="category" value="${obj.category}" readonly="">
                </div>
                <div class="form-group">
                    <label for="cinemaname">Cinema</label>
                    <input type="text" class="form-control" name="cinemaname" value="${obj.cinema}" readonly="">
                </div>
                ${favBtn}
            </form>
        </div>
    </div>`)
    }
</script>
</body>

</html>