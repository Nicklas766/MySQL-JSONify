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

// The function returns the GET based on key.
function getPOST($key, $default = null) {
	return isset($_POST[$key]) ? $_POST[$key] : $default;
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
	// multible orderig organizer.
function orderOrganizer($order, $tableRows, $id) {
	$organizeOrder = "";
	$sortArray = array("ASC", "DESC");
	$slices = explode(";", $order);
	$numItems = count($slices);
	$i = 0;
	$comma = ",";
	foreach($slices as $slice) {
		$sorter = "ASC";
		$col = $id;
		$parts = explode(",", $slice);
		foreach($parts as $part) {
				if (arrayCheckIn($part, $sortArray)) {
					$sorter = arrayCheck($part, $sortArray);
				} else if (arrayCheckIn($part, $tableRows)) {
					$col = arrayCheck($part, $tableRows);
				}
			} //part and

		if (++$i === $numItems) {
			$comma = "";
		}
		$organizeOrder .= "`".$col.
		"` ".$sorter.$comma;
	}
	return $organizeOrder;
}



function filterOrganizer($filter, $tableRows) {
$comparisonOperatorsArray = array("LIKE", "NOT LIKE");
$logicalOperatorsArray = array("AND", "OR", "||", "&&", "XOR");
$slices = explode(";", $filter);
$organizeFilter = "";
$numItems = count($slices);
$i = 0;
foreach($slices as $slice) {
	$comOperator = "LIKE";
	$logOperator = "OR";
	$search = "";
	//$col=$id;
	$parts = explode(",", $slice);
	foreach($parts as $part) {
		if (arrayCheckIn($part, $comparisonOperatorsArray)) {
			$sorter = arrayCheck($part, $comparisonOperatorsArray);
		} else if (arrayCheckIn($part, $tableRows)) {
			$col = arrayCheck($part, $tableRows);
		} else if (arrayCheckIn($part, $logicalOperatorsArray)) {
			$logOperator = arrayCheck($part, $logicalOperatorsArray);
		} else {
			$search = $part;
		}
	}
	if (empty($col)) {
		$numItemsNonCol = count($tableRows);
		$j = 0;



		foreach($tableRows as $tableRow) {
			if (++$j === $numItemsNonCol) {
				if (++$i === $numItems) {
					$logOperator = "";
				}
			}
			$organizeFilter .= "`".$tableRow.
			"` ".$comOperator.
			" '".$search.
			"' ".$logOperator.
			" ";
		}




	} else {


		if (++$i === $numItems) {
			$logOperator = "";
		}

		$organizeFilter .= "`".$col.
		"` ".$comOperator.
		" '".$search.
		"' ".$logOperator.
		" ";
	}
}
return $organizeFilter;
}




// Moves the element to the end index and return array
function returnInfo($data, $sql, $rowCount) {
	$params = $data-> params;
	$info["rowCount"] = $rowCount;
	$info["page"] = $params["page"] ? $params["page"] : 1;
	$info["numberOfPages"] = ceil($info["rowCount"] / $info["page"] ? $info["page"] : 1);
	$info["id"] = $data-> id;
	return $info;
}