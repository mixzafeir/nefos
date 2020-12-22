<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database file
include_once 'mongodb_config.php';
$dbname = 'mixalisdb';

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return substr($haystack, 0, $length) === $needle;
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if (!$length) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}

$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);

$filter = [];
if (endsWith($uri_parts[0], "movies")) {
    $collection = "movies";
    if ($_GET["title"] != "") {
        $filter["title"] = ['$regex' => $_GET["title"]];
    }
    if ($_GET["cinema"] != "") {
        $filter["cinema"] = ['$regex' => $_GET["cinema"]];
    }
    if ($_GET["category"] != "") {
        $filter["category"] = ['$regex' => $_GET["category"]];
    }
    if ($_GET["date"] != "") {
        $filter['startdate'] = ['$lte' => $_GET["date"]];
        $filter['enddate']   = ['$gt' => $_GET["date"]];
    }
} elseif (endsWith($uri_parts[0], "cinemas")) {
    $collection = "cinemas";
    if ($_GET["owner"] != "") {
        $filter["owner"] = $_GET["owner"];
    }
} elseif (endsWith($uri_parts[0], "users")) {
    $collection = "users";
} elseif (endsWith($uri_parts[0], "favorites")) {
    $collection = "favorites";
    if ($_GET["user"] != "") {
        $filter["user"] = $_GET["user"];
    }
} else {
    echo "unknown collection.available collections: movies cinemas users";
    exit (400);
}

//DB connection
$db   = new DbManager();
$conn = $db->getConnection();

$option = [];
$read   = new MongoDB\Driver\Query($filter, $option);
//fetch records
$records = $conn->executeQuery("$dbname.$collection", $read);
$arr     = iterator_to_array($records);
$records = array();
foreach ($arr as $row) {
    $row->id = (string) $row->_id;
    unset($row->_id);
    $records[] = $row;
}
echo json_encode($records);
?>