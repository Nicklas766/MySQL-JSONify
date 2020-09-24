<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Table</title>
 <script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"> </script> 
</head>
<body>
<style>
ol#pagination li {
  display:inline;
  margin-right: 5px;
}
</style>
<?php
    // GET details
    $obj = (include 'details.php');
?>
<select  id="path" >
<?php
foreach($obj["paths"] as $key => $value){
    echo '<option value="'.$key.'">'.$value.'</option>'; 
}
?>
</select>
<input type="text" id="filter" placeholder="filter" value="" >
<input type="number" id="offset" placeholder="offset" value="" >
<input type="number" id="limit" placeholder="limit" value="" >
<p id="linkName"></p>
<p id="info"></p>
<ol id="pagination">
</ol>
<div id="checboxes">
</div>
<table id="table" align = "center" border="1px">
    <thead id="tablehead"></thead>
    <tbody id="tablebody"></tbody>
</table> 
    <script> 
var currentPage = "";
var selectCols = [];
var orderCols = {};
var orderCol = "";
var selectColChecked = "";
var orderColed = '';
/*Select Module*/
$("#checboxes").click(function() {
    selectCols = [];
    $.each($("input[name='column']:checked"), function() {
        selectCols.push($(this).val());
    });
    selectColChecked = selectCols.join(",");

});
/*Order Module*/
$("table").click(function(event) {
    orderCol = event.target.getAttribute("data-col");
    if (orderCol !== null) {
        if (orderCols[orderCol] == 'asc') {
            orderCols[orderCol] = 'desc';
        } else if (orderCols[orderCol] == 'desc') {
            orderCols[orderCol] = 'asc';
        } else {
            if (!(event.shiftKey)) {
                orderCols = {};
            }
            //First click
            orderCols[orderCol] = 'asc';
        }
    }
    $("#info").html(JSON.stringify(orderCols));
    orderColed = '';
    $.each(orderCols, function(keyOr, valOr) {
        orderColed += '' + keyOr + ',' + valOr + ';';
    });
});
/*Pagination Module*/
$("#pagination").click(function(event) {
    if (event.target.getAttribute("data-page") !== null) {
        currentPage = event.target.getAttribute("data-page");
    }
});
/*Table Module*/
$("#path, #selectCol, #filter, #order, #offset, #page, #limit, #pagination, #checboxes, table")
    .on('keyup change click', function(event) {
        var path = document.getElementById("path");
        var filter = document.getElementById("filter");
        var offset = document.getElementById("offset");
        var limit = document.getElementById("limit");
        var linkJson = "api.php/" + path.value + "?select=" + selectColChecked + "&filter=" + filter.value + "&order=" + orderColed + "&offset=" + offset.value + "&page=" + currentPage + "&limit=" + limit.value + "";
        $("#linkName").html(linkJson);
        //Json System
        $.getJSON(linkJson, function(data) {
            //Pagination system
            var pagination = "";
            for (i = 1; i <= data.info.numberOfPages; i++) {
                pagination += '<a href="javascript:void(0)"><li class="page" data-page="' + i + '">' + i + '</li></a>';
            }
            $("#pagination").html(pagination);
            //Select System
            var checboxes = "";
            $.each(data.info.tableRows, function(keyCh, valCh) {
                if (jQuery.inArray(valCh, selectCols) !== -1) {
                    checboxes += '<label><input type="checkbox" value="' + valCh + '" name="column" checked>' + valCh + '</label>';
                } else {
                    checboxes += '<label><input type="checkbox" value="' + valCh + '" name="column">' + valCh + '</label>';
                }
            });
            $("#checboxes").html(checboxes);
            //Table system
            //Thead system start
            $("#tablehead").html("");
            $("#tablebody").html("");
            var theader = '<tr>';
            $.each(data.data[0], function(keyTh, valTh) {
                theader += '<th data-col="' + keyTh + '">' + keyTh + '</th>';
            });
            theader += "</tr>";
            $("#tablehead").html(theader);
            //Thead system end
            //Tbody system start

            $.each(data.data, function(key, val) {
                var tbody = "";
                tbody += "<tr>";
                $.each(val, function(keyTr, valTr) {
                    tbody += '<td>' + valTr + '</td>';
                });
                tbody += "</tr>";
                $("#tablebody").append(tbody);
            });
            //Tbody system end
        });
    })
    .click();
    </script>  
</body>
</html>