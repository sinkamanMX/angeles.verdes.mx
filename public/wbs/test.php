<?php

$con = mysql_connect("192.168.6.106","dba","t3cnod8A!");
    if ($con){
      $base = mysql_select_db("SIMA",$con);
      echo "OK";
    } else {
      echo "NO";   
}

?>
