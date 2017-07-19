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
          return "DELETE FROM $data->table WHERE id = :id ";
        }
    }

    // Create statement and return it
    public function updateSQL($data) {
      $set = arrayKeyRemove($data->tableRows, 'id');
      $set = implode($set, " = ?, ") . " = ? WHERE id = ?";

      return "UPDATE $data->table SET $set";
    }
    // Create statement and return it
    public function getSQL($data) {
      $params = $data->params;

      // If parameter exists, then make string else empty
      $id = $params["id"] ? "WHERE id = :id" : "";
      $order = $params["order"] ? "ORDER BY id $params[order]" : "";
      $limit = $params["limit"] ? "LIMIT $params[limit]" : "LIMIT 18446744073709551610"; # default limit
      $offset = $params["offset"] ? "OFFSET $params[offset]" : "";
      $filter = $params["filter"] ? " WHERE * LIKE='$params[filter]'" : "";

      $stack = [$id, $order, $limit, $offset, $filter];

      return "SELECT $this->select FROM $data->table " . implode(" ", array_filter($stack));
    }

    public function postSQL($data) {
      // $questionMarks = default (?, ?, ?). Based on $data->posts length.
      $questionMarks = implode(array_fill(0, count($data->posts), '?'), ",");
      $set = implode(arrayKeyRemove($data->tableRows, 'id'), ",");

      return "INSERT INTO $data->table ($set) VALUES ($questionMarks)";
    }


}
