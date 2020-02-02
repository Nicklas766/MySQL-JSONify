<?php

class SQLify
{
    public $select;
    public $where;
    public $sql;

    /**
     * Constructor
     */
    public function __construct($data)
    {
      $this->select = $this->getSQLPart($data, "select");
      $this->where = $this->getSQLPart($data, "where");

      $this->sql = $this->createSQL($data);
    }

    // Return part (select, where) if exists, else "*"
    public function getSQLPart($data, $part) {
        if ($data->params[$part]) {
          return $data->params[$part];
        }
        return "*";
    }

    public function createSQL($data) {
      // Based on method, create SQL
      switch ($data->method) {
        case 'GET':
          return $this->getSQL($data);
        case 'POST':
          return $this->postSQL($data);
        case 'PUT':
          return $this->updateSQL($data);
        case 'DELETE':
          return "DELETE FROM $data->table WHERE $data->id = :id ";
        }
    }

    // Create statement and return it
    public function updateSQL($data) {
      $set = arrayKeyRemove($data->tableRows, 'id');
      $set = implode($set, " = ?, ") . " = ? WHERE $data->id = ?";

      return "UPDATE $data->table SET $set";
    }
    // Create statement and return it
    public function getSQL($data) {
      $params = $data->params;
      // If parameter exists, then make string else empty
      $id = $params["id"] ? "WHERE $data->id = :id" : "";
      $order = $params["order"] ? "ORDER BY ". orderOrganizer($params["order"],$data->tableRows,$data->id) : "";
      $limit = $params["limit"] ? "LIMIT $params[limit]" : "LIMIT 18446744073709551610"; # default limit
      //Page params change but it is not organize will be change
	  if(isset($params["offset"])and $params["offset"]>0){$offset = "OFFSET $params[offset]" ;}elseif(isset($params["page"]) and $params["page"]>0){$offset = "OFFSET ".($params["page"]-1) * ($params["limit"] ? $params["limit"] : 1000) ;
	  if(($params["page"]-1) * ($params["limit"] ? $params["limit"] : 1000)<0){$offset ="";}
	  }else{$offset ="";}
		//filter is back thanks for filterOrganizer function
      $filter = $params["filter"] ? " WHERE " .filterOrganizer($params["filter"], $data->tableRows) : "";

      $stack = array($id, $filter, $order, $limit, $offset);
      return "SELECT $this->select FROM $data->table " . implode(" ", array_filter($stack));
    }

    public function postSQL($data) {
      // $questionMarks = default (?, ?, ?). Based on $data->posts length.
      $questionMarks = implode(array_fill(0, count($data->posts), '?'), ",");
      $set = implode(arrayKeyRemove($data->tableRows, 'id'), ",");

      return "INSERT INTO $data->table ($set) VALUES ($questionMarks)";
    }


}
