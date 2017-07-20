<?php

class DataHandler
{
    // Table, method Data
    public $path;
    public $method;
    public $table;

    // Rows data
    public $params;
    public $posts;
    public $putParams;

    // Acceptable data
    public $tableRows; # Acceptable WHERE'S and SELECT's (tables columns)
    private $validParams = ["select", "order", "where", "id", "filter", "limit", "offset"];
    private $validPaths;

    /**
     * Constructor
     */
    public function __construct($obj, $connect)
    {
      // Get the full URL and Valid paths and Method, such as POST, GET, PUT, DELETE
      $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      $this->path = basename(parse_url($url, PHP_URL_PATH));
      $this->validPaths = array_keys($obj["paths"]);
      $this->method = $_SERVER['REQUEST_METHOD'];

      // controlPath then get table rows
      $this->controlPath();
      $this->table = $obj["paths"][$this->path];
      $this->tableRows = $connect->fetchArray("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='$this->table'");

      // Get the GETS in the $this->validParams
      // $this->params = methodData($this->method);
      $this->params = array_combine($this->validParams, array_map("getGet", $this->validParams));

      // Get posts, remove ID
      $this->posts = array_map("getPOST", arrayKeyRemove($this->tableRows, 'id'));

      // Get paths based on colum names and move id last ALWAYS.
      $putParams = array_combine($this->tableRows, array_map("getGet", $this->tableRows));
      $this->putParams = array_values(moveIndexEnd($putParams, 'id'));

      $this->sqlParams = [':id' => $this->params["id"]]; # No need to escape it
    }

    /**
     * errorHandler, echos ERROR JSON-response and it ends here
     */
    public function errorHandler()
    {
      $errorPath = ['validPaths' => $this->validPaths, 'givenPath' => $this->path];
      $errorParam = ['validSelects' => "ALL table columns, $this->path", 'validParams' => $this->validParams, 'givenParams' => $this->params];
      $error = ['Path' => $errorPath, 'Param' => $errorParam];
      echo json_encode($error, JSON_PRETTY_PRINT);
      die();
    }
    /**
     * @return true if valid, else false
     */
    public function controlPath()
    {
      if (!in_array($this->path, $this->validPaths)) {
          $this->errorHandler();
      }
      return $this->table;
    }

    public function controlParams()
     {
       // ---------------------------------------------------
       // Controll Select
       // Incoming matches valid value sets
       if (!in_array($this->params["select"], $this->tableRows) && $this->params["select"]) {
           $this->errorHandler($this);
       }

      // ---------------------------------------------------
      // Only these values are valid
      $orders = ["asc", "desc", null];

      if (!is_numeric($this->params["offset"]) && $this->params["offset"]) {
          $this->errorHandler();
      }

      if (!is_numeric($this->params["limit"]) && $this->params["limit"]) {
          $this->errorHandler();
      }

      // Incoming matches valid value sets
      if (!in_array($this->params["order"], $orders)) {
          $this->errorHandler();
      }
    }
}
