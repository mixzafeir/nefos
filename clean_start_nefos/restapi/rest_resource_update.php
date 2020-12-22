<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database file
include_once 'mongodb_config.php';

$dbname = 'mixalisdb';

//DB connection
$db   = new DbManager();
$conn = $db->getConnection();

//record to update
$data = json_decode(file_get_contents("php://input", true));

$collection = $data->dbcollection;
unset($data->dbcollection);
//_id field value
$id = $data->id;
// update record
$update = new MongoDB\Driver\BulkWrite();
$update->update(array('_id' => new \MongoDB\BSON\ObjectId($id)), $data);

$result = $conn->executeBulkWrite("$dbname.$collection", $update);

// verify
if ($result->getModifiedCount() == 1) {
    echo json_encode(
        array("message" => "Record successfully updated")
    );
} else {
    echo json_encode(
        array("message" => "Error while updating record")
    );
}

?>