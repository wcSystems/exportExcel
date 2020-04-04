<?php
    $ubicacion = $_FILES['i_file']['tmp_name'];
    $nombre = $_FILES['i_file']['name'];
    move_uploaded_file($ubicacion, "excel/".$nombre); 
    echo $nombre;
?>