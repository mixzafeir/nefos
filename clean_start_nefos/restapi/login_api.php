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
$collection='users';

//DB connection
$db = new DbManager();
$conn = $db->getConnection();

$jsonStr = file_get_contents("php://input"); //read the HTTP body.
$jsonStr = stripslashes(html_entity_decode($jsonStr));
//print_r($jsonStr);

$data = json_decode($jsonStr,true);


//$filter=[];
$option = [];
$read = new MongoDB\Driver\Query($data, $option);
//fetch records
$records = $conn->executeQuery("$dbname.$collection", $read);
$arr=iterator_to_array($records);

// verify
if (empty($arr)){
    echo json_encode(
		array("message" => "Invalid login")
	);
} else {
    $user=$arr[0];
    $user->accessToken=keyrockLogin($user->email,$user->password);
    echo json_encode(
            array("message" => "Success login",
            "user"=>$user
            )
    );
}
?>