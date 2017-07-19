<?php
require_once("classes/Connect.php");
require_once("classes/dataHandler.php");
require_once("classes/SQLify.php");
require_once("classes/functions.php");
ini_set('display_errors', 'On');
error_reporting(-1);

// GET JSON-file
$str = file_get_contents('response.json');
$json = json_decode($str, true); // decode the JSON into an associative array


// create all classes
$connect = new Connect($json["database"]);
$data = new DataHandler($json, $connect);
$sql = new SQLify($data);

$data->controlParams();
