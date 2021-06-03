<?php

return
array(
    "author" => "NICKLAS ENVALL,TURAN FURKAN TOPAK",
    "version" => "3",
    "serverKey" => "5f2b5cdbe5194f10b3241568fe4e2b24",
    "login" =>
    array(
        "table" => "users",
        "userId" => "id",
        "username" => "username",
        "password" => "password",
        "authorityLevel" => "authority",
        "expirationRemainingHours" => "6"
    ),
    "database" =>
    array(
        "host" => "localhost",
        "dbname" => "fullhdci_mysql-jsonify",
        "username" => "XXX",
        "password" => "XXX"
    ),

    "paths" =>
    array(
        "path" =>
        array(
            "name" => "MYSQL-TABLE-NAME",
            "select" => "1",
            "insert" => "1",
            "update" => "1",
			"notUpdate" =>
        array(
            "tableName" => "0"
        ),
            "delete" => "1"
        ),
        "users" =>
        array(
            "name" => "users",
            "select" => "0",
            "insert" => "0",
            "update" => "0",
            "delete" => "0"
        ),
        "products" =>
        array(
            "name" => "Shop_Products",
            "insert" => "1",
            "update" => "1",
            "delete" => "1"
        )
    )
);
