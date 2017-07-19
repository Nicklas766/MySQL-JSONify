<?php
// Make sure the APP that requests data receives JSON-response
//------------------------------------------------------------------------------
header('Content-Type: application/json');
//------------------------------------------------------------------------------

include ('config.php');

echo $connect->startResponse($data, $sql);
