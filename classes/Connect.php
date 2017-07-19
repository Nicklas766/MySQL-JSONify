<?php

class Connect
{
    protected $db;

    /**
     * Constructor
     * @param $dsn string The dsn to the database-file
     * @return void
     */
    public function __construct($obj)
    {
        // Server
        $databaseConfig = [
            "dsn"      => "mysql:host=$obj[host];dbname=$obj[dbname]",
            "login"    => "$obj[username]",
            "password" => "$obj[password]",
            "options"  => [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"],
        ];

        try {
            $db = new PDO(...array_values($databaseConfig));
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db = $db;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            throw new PDOException("Could not connect to database, hiding details.");
        }
      }

    //
    public function startResponse($data, $sql)
    {
      // Based on method, do GET, POST, PUT, DELETE
      switch ($data->method) {

        case 'GET':
          return $this->jsonResponse($sql->sql, $data->sqlParams);
        case 'POST':
          $this->execute($sql->sql, $data->posts);
          return $this->jsonResponse("SELECT * FROM $data->table WHERE id=" . $this->db->lastInsertId());
        case 'PUT':
          $this->execute($sql->sql, $data->putParams);
          return $this->jsonResponse("SELECT * FROM $data->table WHERE id=" . end($data->putParams));
        case 'DELETE':
          $this->execute($sql->sql, $data->sqlParams);
          return $this->jsonResponse("SELECT * FROM $data->table");
        }

    }

    public function execute($sql, $sqlParams = null) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($sqlParams);
    }
    // Fetches from MySQL DB and returns as JSON
    public function jsonResponse($sql, $sqlParams = null) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($sqlParams);
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
    }

    // get res with one fetch
    public function fetchArray($sql)
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
