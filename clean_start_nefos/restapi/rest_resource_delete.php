<?php


function cascadeDelete($username,$conn){
    $filter=[
        "owner"=>$username
    ];
    $read   = new MongoDB\Driver\Query($filter, $option);
    $records = $conn->executeQuery("mixalisdb.cinemas", $read);
    $arr     = iterator_to_array($records);
    if(count($arr)>0){
        $cinemaname=$arr[0]->cinema;
        $command = new MongoDB\Driver\BulkWrite();
        $command->delete(
            ['cinema' => $cinemaname],
            ['limit' => 0]
        );
        $result = $conn->executeBulkWrite("mixalisdb.cinemas", $command);


        $command = new MongoDB\Driver\BulkWrite();
        $command->delete(
            ['cinema' => $cinemaname],
            ['limit' => 0]
        );
        $result = $conn->executeBulkWrite("mixalisdb.movies", $command);

        $command = new MongoDB\Driver\BulkWrite();
        $command->delete(
            ['cinema' => $cinemaname],
            ['limit' => 0]
        );
        $result = $conn->executeBulkWrite("mixalisdb.favorites", $command);
    }
    $filter=[
        "user"=>$username
    ];
    $read   = new MongoDB\Driver\Query($filter, $option);
    $records = $conn->executeQuery("mixalisdb.favorites", $read);
    $arr     = iterator_to_array($records);
    if(count($arr)>0){
        $command = new MongoDB\Driver\BulkWrite();
        $command->delete(
            ['user' => $username],
            ['limit' => 0]
        );
        $result = $conn->executeBulkWrite("mixalisdb.favorites", $command);

    }
}

function cascadeMov($title,$cinema,$conn){
    $filter=[
        "title"=>$title,
        "cinema"=>$cinema
    ];
    $read   = new MongoDB\Driver\Query($filter, $option);
    $records = $conn->executeQuery("mixalisdb.favorites", $read);
    $arr     = iterator_to_array($records);
    if(count($arr)>0){
        $command = new MongoDB\Driver\BulkWrite();
        $command->delete(
            [
            'cinema' => $cinema,
            'title' => $title
            ],
            ['limit' => 0]
        );
        $result = $conn->executeBulkWrite("mixalisdb.favorites", $command);
    }
}



// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database file
include_once 'mongodb_config.php';

$dbname = 'mixalisdb';

//DB connection
$db   = new DbManager();
$conn = $db->getConnection();

//record to delete
$data = json_decode(file_get_contents("php://input", true));

// delete record
$command = new MongoDB\Driver\BulkWrite();

$collection = $data->dbcollection;

if ($collection == "favorites") {
    $command->delete(
        [
            'title' => $data->title,
            'user'  => $data->user,
            'cinema' => $data->cinema
        ],
        ['limit' => 0]
    );
}else{
    $command->delete(
        ['_id' => new \MongoDB\BSON\ObjectId($data->id)],
        ['limit' => 0]
    );
}
try {
    $result = $conn->executeBulkWrite("$dbname.$collection", $command);
    if($collection=="users"){
        cascadeDelete($data->username,$conn);
    }
    if($collection=="movies"){
        cascadeMov($data->title,$data->cinema,$conn);
    }
}catch (\Exception $e) {
    printf("Other error: %s\n", $e->getMessage());
    exit;
}
// verify
if ($result->getDeletedCount() > 0) {
    echo json_encode(
        array("message" => "Record successfully deleted")
    );
} else {
    echo json_encode(
        array("message" => "Error while deleting record")
    );
}

?>