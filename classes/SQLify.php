<?php

class SQLify {
    public $select;
    public $where;
    public $sql;
    public $paginationSQL;
    /**
     * Constructor
     */
    public
    function __construct($data) {
        $this -> select = $this -> getSQLPart($data, "select");
        $this -> where = $this -> getSQLPart($data, "where");
        $this -> sql = $this -> createSQL($data);
        $this -> paginationSQL = $this -> getSQL($data, "pagination");
    }

    // Return part (select, where) if exists, else "*"
    public
    function getSQLPart($data, $part) {
        if ($data -> params[$part]) {
            return $data -> params[$part];
        }
        return "*";
    }

    public
    function createSQL($data, $purpose = null) {
        // Based on method, create SQL
        switch ($data -> method) {
            case 'GET':
                return $this -> getSQL($data);
            case 'POST':
                return $this -> postSQL($data);
            case 'PUT':
                return $this -> updateSQL($data);
            case 'DELETE':
                return "DELETE FROM $data->table WHERE $data->id = :id ";
        }
    }
    // Create statement and return it
    public
    function updateSQL($data) {
            $set = arrayKeyRemove($data -> tableRows, 'id');
            $set = implode($set, " = ?, ").
            " = ? WHERE $data->id = ?";
            return "UPDATE $data->table SET $set";
        }
        // Create statement and return it
    public
    function getSQL($data, $purpose = null) {
        $params = $data -> params;
        // If parameter exists, then make string else empty
        if ($params["id"]) {
            $id = "WHERE $data->id = :id";
        } else {
            $id = "";
        }
        //Order if
        if ($params["order"]) {
            $order = "ORDER BY ".orderOrganizer($params["order"], $data -> tableRows, $data -> idCol);
        } else {
            $order = "";
        }
        //Limit if
        if ($params["limit"] > 0) {
            $limit = "LIMIT $params[limit]";
        } else {
            $limit = "LIMIT 18446744073709551610";
        }
        //Page params change but it is not organize will be change
        if ($params["offset"] > 0) {
            $offset = "OFFSET $params[offset]";
        }
        elseif($params["page"] > 0 and $params["limit"] > 0) {
            $offset = "OFFSET ".($params["page"] - 1) * $params["limit"];
        } else {
            $offset = "";
        }
        //filter is back thanks for filterOrganizer function
        if ($params["filter"]) {
            $filter = " WHERE ".filterOrganizer($params["filter"], $data -> tableRows);
        } else {
            $filter = "";
        }
        //creates the required sql to create the total number of pages required for pagination
        if ($purpose == "pagination") {
            $stack = array($id, $filter, $order);
            return "SELECT $this->select FROM $data->table ".implode(" ", array_filter($stack));
        } else {
            $stack = array($id, $filter, $order, $limit, $offset);
            return "SELECT $this->select FROM $data->table ".implode(" ", array_filter($stack));
        }
    }

    public
    function postSQL($data) {
        $questionMarks = implode(array_fill(0, count($data -> posts), '?'), ",");
        $set = implode(arrayKeyRemove($data -> tableRows, 'id'), ",");

        return "INSERT INTO $data->table ($set) VALUES ($questionMarks)";
    }


}