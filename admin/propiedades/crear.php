<?php

require '../../includes/app.php';
use App\Propiedad;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager as Image;

$auth = estaAutenticado(); 
//base de datos

$db = conectarDB();

$propiedad = new Propiedad;

//consultar obtener vendedores
$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

//Arreglo con mensajes de errores
$errores = Propiedad::getErrores();

//ejecutar el codigo despues de que el usuario envia el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* crea una nueva instancia */
    $propiedad = new Propiedad($_POST);

    /* subida de archivos */
    //generar un nombre unico para la imagen
    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
    if($_FILES['imagen']['tmp_name']){
        $manager = new Image(Driver::class);
        $imagen = $manager->read($_FILES['imagen']['tmp_name'])->cover(800, 600);
        $propiedad-> imagen = $nombreImagen;
    }

    $errores = $propiedad->validar();


    
    if (empty($errores)) {

        /* Subida de archivos */
        //USAMOS SUPER GLOBAR QUE LA MANDAMOS A LLAMAR DESDE FUNCIONES.PHP
        if (!is_dir(CARPETA_IMAGENES)) {
            mkdir(CARPETA_IMAGENES);
        }

            //guarda la imagen en el servidor
            $imagen->save(CARPETA_IMAGENES . '/' . $nombreImagen);
        
        $resultado =$propiedad->guardar();
        if ($resultado) {
            //rediccionar al usuario a la página principal
            header("Location: /admin?resultado=1");
        }
    }
}



incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Crear</h1>



    <a href="/admin" class="boton-verde">Volver</a>

    <?php foreach ($errores as $error):  ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>


    <form class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
        <?php include '../../includes/templates/formulario_propiedades.php'; ?>

        <input type="submit" value="Crear Propiedad" class="boton-verde">
    </form>
</main>

<?php
incluirTemplate('footer');
?>