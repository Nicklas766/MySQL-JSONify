<?php


// Moves the element to the end index and return array
function moveIndexEnd($array, $key) {
  $newArray = $array;
  unset($newArray[$key]);
  $newArray[] = $array[$key];
  return $newArray;
}
// Returns array without given $key, array_values for re-index array
function arrayKeyRemove($arr, $key) {
    return array_values(array_filter($arr, function($k) use($key) {
        return $k != $key;
    }, ARRAY_FILTER_USE_KEY));
}

// The function returns the GET based on key.
function getGet($key, $default = null)
{
    return isset($_GET[$key])
        ? $_GET[$key]
        : $default;
}

// The function returns the GET based on key.
function getPOST($key, $default = null)
{
    return isset($_POST[$key])
        ? $_POST[$key]
        : $default;
}

function sqlOperator($params) {
    $operator = "$params[filterOperator] $params[where] LIKE ";
    // create array with the sent in string
    $pieces = explode(",", $params["filter"]);

    // prepare SQL statement again with wildcards
    foreach ($pieces as $key => $value) {
        $pieces[$key] = " '%" . $value . "%' ";
    }
    // return string with operator for multiple "LIKEs" and "ANDs" and "ORs"
    return implode($operator, $pieces);
}
