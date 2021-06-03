<?php

class DataHandler {
    // Table, method Data
    public $path;
    public $method;
    public $table;

    // Rows data
    public $params;
    public $posts;
    public $putParams;

    // Acceptable data
    public $tableRows; // Acceptable WHERE'S and SELECT's (tables columns)
    private $validParams = array("select", "order", "where", "id", "filter", "limit", "offset", "page", "statement", "token");
    private $validPaths;
    public $idCol;
    public $loginInfo;
    public $statementPass;
	
	
    /**
     * Constructor
     */
    public
    function __construct($obj, $connect) {
            // Get the full URL and Valid paths and Method, such as POST, GET, PUT, DELETE
            $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $this -> path = basename(parse_url($url, PHP_URL_PATH));
            $this -> validPaths = array_keys($obj["paths"]);
            $this -> method = $_SERVER['REQUEST_METHOD'];
            $this -> serverKey = $obj["serverKey"];
            $this -> login = $obj["login"];
            // controlPath then get table rows
            $this -> controlPath();
            $this -> table = $obj["paths"][$this -> path]["name"];
            $this -> tableProperty = $obj["paths"][$this -> path];
            $this -> tableRows = $connect -> fetchArray("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='$this->table'");
            //Added due to the possibility of id being in another name
            $this -> primaryTableRows = $connect -> fetchArray("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.key_column_usage WHERE TABLE_NAME='$this->table' AND CONSTRAINT_NAME = 'PRIMARY'");
            $this -> idCol = $this -> primaryTableRows[0];
            // Get the GETS in the $this->validParams
            $this -> params = array_combine($this -> validParams, array_map("getGet", $this -> validParams));

            // Get posts, Only receives data sent
            $this -> posts = array_filter(array_combine($this -> tableRows, array_map("getPOST", $this -> tableRows)));
            // Get paths based on colum names and move id last ALWAYS.
            $putParams = array_combine($this -> tableRows, array_map("getGet", $this -> tableRows));
            $this -> putParams = array_values(moveIndexEnd($putParams, $this -> idCol));

            $this -> sqlParams = array(':id' => $this -> params["id"]); //No need to escape it
        }
        /**
         * errorHandler, echos ERROR JSON-response and it ends here
         */
    public
    function errorHandler() {
        $errorPath = array('validPaths' => $this -> validPaths, 'givenPath' => $this -> path);
        $errorParam = array('validSelects' => "ALL table columns, $this->path", 'validParams' => $this -> validParams, 'givenParams' => $this -> params);
        $error = array('Path' => $errorPath, 'Param' => $errorParam);
        echo json_encode($error, JSON_PRETTY_PRINT);
        exit();
    }

    /**
     * Token generator to Post Method
     */
    public
    function loginJwt($connect) {
            $username = getPOST('username');
            $password = getPOST('password');
            $executeArray = array(':username' => $username, ':password' => $password);
            $query = $connect -> fetchOneRow("SELECT * FROM ".$this -> login['table'].
                " WHERE ".$this -> login['username'].
                "=:username && ".$this -> login['password'].
                "=:password", $executeArray);
            if ($query) {
                $nbf = strtotime("now");
                $exp = strtotime($this -> login['expirationRemainingHours'].
                    ' hour');
                // create a token
                $payloadArray = array();
                $payloadArray['userId'] = $query[$this -> login['userId']];
                $payloadArray['username'] = $query[$this -> login['username']];
                $payloadArray['authorityLevel'] = $query[$this -> login['authorityLevel']];
                if (isset($nbf)) {
                    $payloadArray['nbf'] = $nbf;
                }
                if (isset($exp)) {
                    $payloadArray['exp'] = $exp;
                }
                $token = JWT::encode($payloadArray, $this -> serverKey);

                // return to caller
                $returnArray['token'] =  $token;

            } else {

                $returnArray['error'] = 'Invalid user ID or password.';
            }
            return $returnArray;
        }
        /**
         * @return true if valid, else false
         */
    public
    function controlPath() {
        if (!in_array($this -> path, $this -> validPaths)) {
            $this -> errorHandler();
        }
        return $this -> table;
    }

    public
    function controlParams() {
        // ---------------------------------------------------
        // Controll Select
        // Incoming matches valid value sets
		
        $selectParams = explode(",", $this -> params["select"]);
        foreach($selectParams as $selectParam) {
                if (!in_array($selectParam, $this -> tableRows) && $selectParam) {
                    $this -> errorHandler($this);
                }
            }
            // ---------------------------------------------------
            // Only these values are valid
        if (!is_numeric($this -> params["offset"]) && $this -> params["offset"]) {
            $this -> errorHandler();
        }

        if (!is_numeric($this -> params["limit"]) && $this -> params["limit"]) {
            $this -> errorHandler();
        }

        if (!is_numeric($this -> params["page"]) && $this -> params["page"]) {
            $this -> errorHandler();
        }
		
		
        if($this -> params["token"]) {
			

                    try {
                        $this -> loginInfo['login'] = JWT::decode($this -> params["token"], $this -> serverKey, array('HS256'));

						if(is_null(@$this -> tableProperty[$this -> params["statement"]]) or $this -> tableProperty[$this -> params["statement"]] >= $this -> loginInfo['login'] -> authorityLevel){
                        //$connect -> execute($sql -> sql['POST']);
							if (isset($this -> params["statement"]) and $this -> params["statement"] === "update" and isset($this -> posts[$data -> idCol])) {
				foreach($this -> tableProperty["notUpdate"] as $key => $value) {
					unset($this -> posts[$key]);
				}
				
							}
						$this -> statementPass = true;
						}else{
						$this -> statementPass = false;
						$this -> loginInfo['login']-> error = "Authority level not enough for ".$this -> params["statement"];
						}
                    } catch (Exception $e) {
						 $this -> statementPass = false;
						 $this -> loginInfo['login']['error'] = $e -> getMessage();
						}//token işlemini controlParams'a taşıyalım
                    //return $connect -> jsonResponse($sql -> sql['GET'], $data -> sqlParams, returnInfo($data, $sql, $this), $data);
                }
		
		
		
		
		
    }
}