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
function getGet($key, $default = null) {
    if (isset($_GET[$key])) {
        return $_GET[$key];
    } else {
        return $default;
    }
}

// The function returns the POST based on key.
function getPOST($key, $default = null) {
    if (isset($_POST[$key])) {
        return $_POST[$key];
    } else {
        return $default;
    }
}

function addStartEndSingleQuote($string) {
    return "'".$string.
    "'";
}

function sqlOperator($params) {
        $operator = "$params[filterOperator] $params[where] LIKE ";
        // create array with the sent in string
        $pieces = explode(",", $params["filter"]);
        // prepare SQL statement again with wildcards
        foreach($pieces as $key => $value) {
                $pieces[$key] = " '%".$value.
                "%' ";
            }
            // return string with operator for multiple "LIKEs" and "ANDs" and "ORs"
        return implode($operator, $pieces);
    }
//These two functions are added for ease of writing
function arrayCheck($value, $array) {
    return $array[array_search(strtolower($value), array_map('strtolower', $array))];
}

function arrayCheckIn($value, $array) {
        return in_array(strtolower($value), array_map('strtolower', $array));
    }
//To avoid injections that can be made for the query
function sqlStringEscaper($value) {
        return str_replace("'", "\'", str_replace('"', '\"', str_replace('\\\\', "\\", $value)));
    }
//To avoid injections that can be made for the query
function firsNumberFinder($value) {
        preg_match_all('!\d+!', $value, $matches);
        return $matches[0][0];
    }
// multible orderig organizer.
function orderOrganizer($order, $tableRows, $idCol) {
    $organizeOrder = "";
    $sortArray = array("ASC", "DESC");
    $slices = explode(";", $order);
    foreach($slices as $slice) {
        $sorter = "ASC";
        $col = $idCol;
        $parts = explode(",", $slice);
        foreach($parts as $part) {
                if (arrayCheckIn($part, $sortArray)) {
                    $sorter = arrayCheck($part, $sortArray);
                } elseif (arrayCheckIn($part, $tableRows)) {
                    $col = arrayCheck($part, $tableRows);
                }
            } //part and

        $organizeOrder .= "`".$col.
        "` ".$sorter;
        //prevents the comma from finally
        if ($slice !== end($slices)) {
            $organizeOrder .= ",";
        }
    }
    return $organizeOrder;
}


function btComOperatorOrganizer($col, $searchWords) {
	asort($searchWords);
    $searchFirst = current($searchWords);
    $searchEnd = end($searchWords);
            return "`".$col."` BETWEEN '".$searchFirst."' AND '".$searchEnd."' ";
	
}
function filterClauseOrganizer($col, $comOperator, $searchWords) {
    $search = current($searchWords);
    switch ($comOperator) {
        case "lk"://LIKE
            return "`".$col."` LIKE '".$search."' ";
        case "nlk"://NOT LIKE
            return "`".$col."` NOT LIKE '".$search."' ";
        case "cs"://contain string (string contains value)
            return "`".$col."` LIKE '%".$search."%' ";
        case "sw"://start with (string starts with value)
            return "`".$col."` LIKE '".$search."%' ";
        case "ew"://end with (string end with value)
            return "`".$col."` LIKE '%".$search."' ";
        case "eq"://equal (string or number matches exactly)
            return "`".$col."` = '".$search."' ";
        case "lt"://lower than (number is lower than value)
            return "`".$col."` > '".$search."' ";
        case "le"://lower or equal (number is lower than or equal to value)
            return "`".$col."` => '".$search."' ";
        case "ge"://greater or equal (number is higher than or equal to value)
            return "`".$col."` =< '".$search."' ";
        case "gt"://greater than (number is higher than value)
            return "`".$col."` < '".$search."' ";
        case "bt"://between (number is between two comma separated values)
            return btComOperatorOrganizer($col, $searchWords);
        case "in": //in (number or string is in comma separated list of values)
            return "`".$col."` IN (".implode(",", array_map("addStartEndSingleQuote",$searchWords)).") ";
        case "is"://is null (field contains "NULL" value)
            return "`".$col."` IS NULL ";
        default: //cace cs(?):
            return "`".$col."` LIKE '%".$search."%' ";
    }
}

function filterOrganizer($filter, $tableRows) {
    $comOperatorsArray = array("lk", "nlk", "cs", "sw", "ew", "eq", "lt", "le", "ge", "gt", "bt", "in", "is");
    $logOperatorsArray = array("AND", "OR", "||", "&&", "XOR");
    $slices = explode(";", $filter);
    $organizeFilter = "";

    foreach($slices as $slice) {
        $comOperator = "LIKE";
        $logOperator = "OR";
        $search = "";
        $parts = explode(",", $slice);
        $comOperator = current(array_intersect($parts, $comOperatorsArray));
        $logOperator = current(array_intersect($parts, $logOperatorsArray));
        $colIntersect = array_intersect($parts, $tableRows);
        $comLogOperatorsColDiff = array_diff($parts, array_merge($comOperatorsArray, $logOperatorsArray, $tableRows));
        $searchWords = array_map("sqlStringEscaper", $comLogOperatorsColDiff);

        if (empty($colIntersect)) {
            $cols = $tableRows;
        } else {
            $cols = $colIntersect;
        }
        $organizeFilter .= "(";
        foreach($cols as $col) {
            $organizeFilter .= filterClauseOrganizer($col, $comOperator, $searchWords);
            if ($col !== end($cols)) {
                $organizeFilter .= "OR".
                " ";
            }
        }
        $organizeFilter .= ") ";



        if ($slice !== end($slices)) {
            if (empty($logOperator)) {
                $logOperator = "OR";
            }
            $organizeFilter .= $logOperator.
            " ";
        }
    }
    return $organizeFilter;
}
	
// Update statement 
function updateOrganizer($table, $posts, $idCol) {
    $sql = "UPDATE `$table` SET ";
    foreach($posts as $key => $value) {
        if ($key !== $idCol) {
            $sql = $sql.$key.
            "=".addStartEndSingleQuote(sqlStringEscaper($value));
            if ($value !== end($posts)) {
                $sql = $sql.
                ",";
            }
        }
    }
    $sql = $sql.
    " WHERE ".$idCol.
    "=".$posts[$idCol];
    return $sql;

}

// Moves the element to the end index and return array
function returnInfo($data, $sql, $connect) {
    $params = $data -> params;
    $info["rowCount"] = $connect -> rowCount($sql -> paginationSQL);
    $info["tableRows"] = $data -> tableRows;
    if ($params["page"]) {
        $info["page"] = $params["page"];
    }
    if ($params["limit"]) {
        $info["limit"] = $params["limit"];
        $info["numberOfPages"] = ceil($info["rowCount"] / $info["limit"]);
    }
    if ($params["statement"] and $params["statement"] === "login"
        and getPOST('username') and getPOST('username')) {
        $info["login"] = $data -> loginJwt($connect);
    }
    elseif($params["token"]) {
        $info["posts"] = $data -> posts;
        try {
            $payload = JWT::decode($params["token"], $data -> serverKey, array('HS256'));
            $info["login"] = $payload;

        } catch (Exception $e) {
            $info["login"]['error'] = $e -> getMessage();
        }
        $info["login"]['token'] = $params["token"];
    }
    $info["id"] = $data -> idCol;
    return $info;
}