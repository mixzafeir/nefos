<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database file
include_once 'mongodb_config.php';
include_once 'keyrock.php';

$dbname = 'mixalisdb';

//DB connection
$db = new DbManager();
$conn = $db->getConnection();

$jsonStr = file_get_contents("php://input"); //read the HTTP body.
$jsonStr = stripslashes(html_entity_decode($jsonStr));

$data = json_decode($jsonStr);
$collection = $data->dbcollection;
unset($data->dbcollection);

if($collection=="users"){
    $filter=[
        "username"=>$data->username
    ];
    $read   = new MongoDB\Driver\Query($filter, $option);
    $records = $conn->executeQuery("$dbname.$collection", $read);
    $arr     = iterator_to_array($records);
    if(count($arr)>0){
        http_response_code(409);    
        header('HTTP/1.1 409 Conflict');
        echo json_encode(
            array("message" => "Username already in use")
        );
        exit;
    }
}


// insert record
$insert = new MongoDB\Driver\BulkWrite();
$insert->insert($data);

$result = $conn->executeBulkWrite("$dbname.$collection", $insert);
// verify
if ($result->getInsertedCount() == 1) {
    //var_dump($data->username);

    if($collection=="users"){
        keyrockCreateUser($data->username, $data->email, $data->password);
    }
    echo json_encode(
		array("message" => "Record inserted created")
	);
} else {
    echo json_encode(
            array("message" => "Error while saving record")
    );
}

?>