<div style='background:lightblue; color:white; border: orange solid 1px; width:900px; margin:auto; text-align:center; height:100%;'>
  <h1> Restful API with PHP and SQL, returns JSON </h1>
  <h2 style="border-bottom: orange solid 1px;"> Crudify </h2>

<?php
    // GET JSON-file
    $str = file_get_contents('response.json');
    $json = json_decode($str, true); // decode the JSON into an associative array

    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $path = basename(parse_url($url, PHP_URL_PATH));
    $validPaths = array_keys($json["paths"]);
    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";
    if (!in_array($path, $validPaths)) {
        echo "<h3> Please before going further, please choose which TABLE you would like to try, by choosing a PATH: </h3>";

        foreach ($validPaths as $value) {
          echo "<h1><a href='$url/$value'>$value</a></h1>";
        }
        die();
    }


    include ('config.php');



?>

  <div style='background:orange; color:white; width:800px; margin:auto; border-radius:2em; padding:10px;'>
    <p> <?= $sql->sql ?> </p>
      </div>
    <div style="background:white; color:orange; width:49.75%; float:left; min-height:500px; border-right: solid 1px;">
      <h1> Try a couple links </h1>
      <ul>
        <li><a style="color:orange;" href="<?= "$url?order=asc"?>"> ?order=asc</a></li>
        <li><a style="color:orange;" href="<?= "$url?order=desc"?>"> ?order=desc</a></li>

        -----

        <li><a style="color:orange;" href="<?= "$url?select=" .$data->tableRows[0]. ""?>">?select=<?=$data->tableRows[0]?></a></li>
        <li><a style="color:orange;" href="<?= "$url?select=" .$data->tableRows[1]. ""?>">?select=<?=$data->tableRows[1]?></a></li>

        -----
        <li><a style="color:orange;" href="<?= "$url?limit=5"?>"> ?limit=5</a></li>
        <li><a style="color:orange;" href="<?= "$url?limit=2&offset=3"?>"> ?limit=2&offset=3</a></li>

        -----
      </ul>
    </div>
    <div style='background:white;width:50%; color:black; float:right; min-height:500px; border-left: orange solid 1px;'>
        <h1> Output </h1>
      <pre style='text-align:left;'>
        <?=  $connect->startResponse($data, $sql) ?>
      </pre>
    </div>


</div>
