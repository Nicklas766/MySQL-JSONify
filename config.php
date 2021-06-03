<?php
require_once("classes/Connect.php");
require_once("classes/dataHandler.php");
require_once("classes/SQLify.php");
require_once("classes/functions.php");
require_once('jwt.php');
ini_set('display_errors', 'On');
error_reporting(-1);

// GET details
$obj = (include 'details.php');

// create all classes
$connect = new Connect($obj["database"]);
$data = new DataHandler($obj, $connect);
$sql = new SQLify($data);

$data->controlParams();
