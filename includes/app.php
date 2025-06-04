<?php 

$db = new mysqli('localhost', 'root', '', 'bienesraices_Crud');

if (!$db) {
    echo "Error al conectar con la base de datos";
    exit;
}


require 'funciones.php'; 
require 'config/database.php';
require __DIR__ . '/../vendor/autoload.php';

use App\Propiedad;

Propiedad::setDB($db);