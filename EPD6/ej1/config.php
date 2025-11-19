<?php

    $con = mysqli_connect("localhost", "root", "", "EPD06");
    if(!$con){
        die('No se pudo conectar: ' . mysqli_error());
    }
?>
