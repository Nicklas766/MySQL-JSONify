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
    return isset($_GET[$key]) ? $_GET[$key] : $default;
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

function filterOrganizer($filter, $tableRows) {
        $comOperatorsArray = array("LIKE", "NOT LIKE", );
        $logOperatorsArray = array("AND", "OR", "||", "&&", "XOR");
        $slices = explode(";", $filter);
        $organizeFilter = "";
        foreach($slices as $slice) {
            $comOperator = "LIKE";
            $logOperator = "OR";
            $search = "";
            $parts = explode(",", $slice);
            foreach($parts as $part) {
                if (arrayCheckIn($part, $comOperatorsArray)) {
                    $comOperator = arrayCheck($part, $comOperatorsArray);
                } elseif (arrayCheckIn($part, $tableRows)) {
                    $col = arrayCheck($part, $tableRows);
                } elseif (arrayCheckIn($part, $logOperatorsArray)) {
                    $logOperator = arrayCheck($part, $logOperatorsArray);
                } else {
                    $search = sqlStringEscaper($part);
                }
            }
            if (empty($col)) {
                $organizeFilter .= "(";
                foreach($tableRows as $tableRow) {
                    $organizeFilter .= "`".$tableRow.
                    "` ".$comOperator.
                    " '".$search.
                    "' ";
                    if ($tableRow !== end($tableRows)) {
                        $organizeFilter .= "OR".
                        " ";
                    }
                    elseif($slice !== end($slices)) {
                        $organizeFilter .= ") ".$logOperator.
                        " ";
                    } else {
                        $organizeFilter .= ") ";
                    }
                }
            } else {
                $organizeFilter .= "`".$col.
                "` ".$comOperator.
                " '".$search.
                "' ";
                if ($slice !== end($slices)) {
                    $organizeFilter .= $logOperator.
                    " ";
                }
            }
        }
        return $organizeFilter;
    }
// Update statement 
function updateOrganizer($table, $posts, $idCol) {
    $sql = "UPDATE ".$table.
    " SET ";
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
        $info["login"]['token'] = $params["token"];
        $info["posts"] = $data -> posts;
        try {
            $payload = JWT::decode($params["token"], $data -> serverKey, array('HS256'));
            $info["login"] = $payload;
        } catch (Exception $e) {
            $info["login"] = $e -> getMessage();
        }
    }
    $info["id"] = $data -> idCol;
    return $info;
}