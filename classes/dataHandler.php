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
    private $validParams = array("select", "order", "where", "id", "filter", "limit", "offset", "page");
    //private $validParams = array("select", "order", "where", "id", "limit", "offset", "page");
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
	  //Added due to the possibility of id being in another name
	  $this->primaryTableRows = $connect->fetchArray("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.key_column_usage WHERE TABLE_NAME='$this->table' AND CONSTRAINT_NAME = 'PRIMARY'");
	  $this->id = $this->primaryTableRows[0];
      // Get the GETS in the $this->validParams
      // $this->params = methodData($this->method);
      $this->params = array_combine($this->validParams, array_map("getGet", $this->validParams));

      // Get posts, remove ID
      $this->posts = array_map("getPOST", arrayKeyRemove($this->tableRows, $this->id));

      // Get paths based on colum names and move id last ALWAYS.
      $putParams = array_combine($this->tableRows, array_map("getGet", $this->tableRows));
      $this->putParams = array_values(moveIndexEnd($putParams, $this->id));

      $this->sqlParams = array(':id' => $this->params["id"]); # No need to escape it
    }

    /**
     * errorHandler, echos ERROR JSON-response and it ends here
     */
    public function errorHandler()
    {
      $errorPath = array('validPaths' => $this->validPaths, 'givenPath' => $this->path);
      $errorParam = array('validSelects' => "ALL table columns, $this->path", 'validParams' => $this->validParams, 'givenParams' => $this->params);
      $error = array('Path' => $errorPath, 'Param' => $errorParam);
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
	   $selectParams = explode(",", $this->params["select"]);
	   foreach($selectParams as $selectParam) {
	   }
       if (!in_array($selectParam, $this->tableRows) && $selectParam) {
           $this->errorHandler($this);
       }

      // ---------------------------------------------------
      // Only these values are valid
      //$orders = array("asc", "desc", null);
		//No need thanks to orderOrganizer function
      if (!is_numeric($this->params["offset"]) && $this->params["offset"]) {
          $this->errorHandler();
      }

      if (!is_numeric($this->params["limit"]) && $this->params["limit"]) {
          $this->errorHandler();
      }
	  
      if (!is_numeric($this->params["page"]) && $this->params["page"]) {
          $this->errorHandler();
      }	
      // Incoming matches valid value sets
	  //No need thanks to orderOrganizer function
	  /*
      if (!in_array($this->params["order"], $orders)) {
          $this->errorHandler();
      }*/
	  
	  
	  
	  
    }
}
