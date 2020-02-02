<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Table</title>
 <script src= 
"https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"> 
    </script> 
</head>

<body>
<?php
    // GET details
    $obj = (include 'details.php');
	
	?>
<select  id="path" >
<?php
foreach($obj["paths"] as $key => $value):
echo '<option value="'.$key.'">'.$value.'</option>'; //close your tags!!
endforeach;
?>
</select>
<input type="text" id="selectCol" placeholder="selectCol" value="" >
<input type="text" id="filter" placeholder="filter" value="" >
<input type="text" id="order" placeholder="order" value="" >
<input type="number" id="offset" placeholder="offset" value="" >
<input type="number" id="page" placeholder="page" value="" >
<input type="number" id="limit" placeholder="limit" value="" >


<p id="linkName"></p>
   <table id="table" align = "center" border="1px"></table> 
    <script> 

$( "#path, #selectCol, #filter, #order, #offset, #page, #limit" )
  .on('keyup change',function () {
  var path = document.getElementById("path");
   var selectCol = document.getElementById("selectCol");
   var filter = document.getElementById("filter");
   var order = document.getElementById("order");
   var offset = document.getElementById("offset");
   var page = document.getElementById("page");
   var limit = document.getElementById("limit");
    var linkJson="api.php/"+ path.value + "?select=" + selectCol.value+"&filter=" + filter.value+"&order=" + order.value+"&offset=" + offset.value+"&page=" + page.value+"&limit=" + limit.value+"" ;

    $( "#linkName" ).html( linkJson );
			$.getJSON( linkJson, function( data ) {
  var items = [];
      var theader = "<thead><tr>" ;
  	    $.each( data.data[0], function( keyTh, valTh ) {
                theader += "<th>"+keyTh+"</th>" ;
	  });
	   theader += "</tr></thead>" ;
	   
	   
      var tbody = "<tbody>" ;
  $.each( data.data, function( key, val ) {
	   tbody += "<tr>" ;
	  	    $.each( val, function( keyTr, valTr ) {
                tbody += "<td>"+valTr+"</td>" ;
	  });
	   tbody += "</tr>" ;
  });
	   tbody += "</tbody>" ;
  $( "#table" ).html( theader+tbody );
});	
	})
  .keyup();		

	
     
    </script>  
</body>
</html>