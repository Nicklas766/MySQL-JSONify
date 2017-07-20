# MySQL-JSONify

A RESTful API made with PHP to convert MySQL to JSON.
-------------

Table of contents

- Introduction
- Setup
- Paths/table
- Parameters
- Examples (GET, POST, PUT, DELETE)

Introduction
-------------
This is a RESTful API created with PHP.  You connect your MySQL database which allows you to CRUD (GET, POST, PUT, DELETE).

The responses are in JSON. You can use parameters to create SQL-statmenents (safely) before fetching data.

Setup
-------------

In the `details.php` file you can type in your database information, so the Connect-class knows where to fetch the data.

```
"database" =>
    [
         "host" => "XXX",
         "dbname" => "XXX",
         "username" => "XXX",
         "password" => "XXX"
    ],

```

Paths
-------------

You can connect your MySQL-tables to the URL path, by editing the `details.php` file. To add more simply name your path and then the table.

```
"paths" =>
    [
         "path" => "MYSQL-TABLE-NAME",
         "users" => "users",
         "products" => "Shop_Products"
    ]
```

If we would request data from `api.php/products`  then we would receive data from the table "Shop_Products". So you choose the path and which table to connect it to.

Parameters
-------------
The data is fetched with a SQL-statement. Therefore you can with parameters decide fairly good how you would like it to look like.

Available params
```
["select", "order", "id",  "limit", "offset"]
```

So let's go through how the SQL-statements can look like,


Basic
```
 api.php/products == SELECT * FROM Shop_Products

 api.php/products?select=name == SELECT name FROM Shop_Products

 api.php/products?order=asc == SELECT * FROM Shop_Products ORDER BY asc

 api.php/users?id=1 == SELECT * FROM users WHERE id = :id
```

Limit
```

 api.php/users?limit=5 == SELECT * FROM users LIMIT 5

 api.php/users?offset=3 == SELECT * FROM users LIMIT 18446744073709551610 OFFSET 3

 api.php/users?limit=2&offset=3 == SELECT * FROM users LIMIT 2 OFFSET 3

```
---
Examples (GET, POST, PUT, DELETE)
===
For the following examples we will use the following SQL-setup code.

```

CREATE TABLE `users`
(
  `id` INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `username` VARCHAR(120) UNIQUE,
  `password` VARCHAR(120),
  `authority` VARCHAR(120)

) ENGINE INNODB CHARACTER SET utf8 COLLATE utf8_swedish_ci;


INSERT INTO `users` (`username`, `password`, `authority`) VALUES
    ("Nicklas766", "pass1", "admin"),
    ("Rasmus Lerdorf", "pass2", "admin"),
    ("Jessica", "pass3", "admin"),
    ("Steve", "pass4", "user"),
    ("Adam", "pass5", "user");

```


GET
-------------

`api.php/users`
```
[
    {
        "id": "1",
        "username": "Nicklas766",
        "password": "pass1",
        "authority": "admin"
    },
    {
        "id": "2",
        "username": "Rasmus Lerdorf",
        "password": "pass2",
        "authority": "admin"
    },
...

```
`api.php/users?order=desc`

```
[
    {
        "id": "5",
        "username": "Adam",
        "password": "pass5",
        "authority": "user"
    },
    {
        "id": "4",
        "username": "Steve",
        "password": "pass4",
        "authority": "user"
    },
...
```

`api.php/users?select=username`
```
[
    {
        "username": "Adam"
    },
    {
        "username": "Jessica"
    },
    {
        "username": "Nicklas766"
    },
    {
        "username": "Rasmus Lerdorf"
    },
    {
        "username": "Steve"
    }
]
```
POST
-------------
You can send in the column names as parameters name. If the value is empty then it will be null.

So with  the following parameters,
```
username = Anna
password = pass6
authority = user

```

`POST api.php/users`

```
{
    "id": "6",
    "username": "Anna",
    "password": "pass6",
    "authority": "user"
}
```

PUT
-------------
You can send in the column names as parameters, in order to update the rows.
`api.php/users?username=Amanda&password=pass3UPDATED&id=3&authority=user`


Before
```
   {
        "id": "3",
        "username": "Jessica",
        "password": "pass3",
        "authority": "admin"
    }
```
After (this is also the response)
```
[
    {
        "id": "3",
        "username": "Amanda",
        "password": "pass3UPDATED",
        "authority": "user"
    }
]
```


DELETE
-------------
You can delete by sending in the ID, response will return the entire table. So in this case user "Nicklas766" should be removed.
`api.php/users?id=1`

```
[
    {
        "id": "2",
        "username": "Rasmus Lerdorf",
        "password": "pass2",
        "authority": "admin"
    },
    {
        "id": "3",
        "username": "Amanda",
        "password": "pass3UPDATED",
        "authority": "user"
    },
    {
        "id": "4",
        "username": "Steve",
        "password": "pass4",
        "authority": "user"
    },
    {
        "id": "5",
        "username": "Adam",
        "password": "pass5",
        "authority": "user"
    },
    {
        "id": "6",
        "username": "Anna",
        "password": "pass6",
        "authority": "user"
    }
]
```
