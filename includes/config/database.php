<?php

//conectar a la base de datos
function conectarDB(): mysqli {
    $db = mysqli_connect('localhost', 'root', '', 'bienesraices_crud', 3306);

    //si no se pudo conectar
    if(!$db){
        echo 'error no se pudo conectar';
        exit; 
    }

    return $db;
}