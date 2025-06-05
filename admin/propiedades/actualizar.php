<?php

require '../../includes/funciones.php';
$auth = estaAutenticado();

    if (!$auth){
        header('Location: /');
    }

//validar la url por id valido
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);


if (!$id) {
    header('location: /admin');
}

//base de datos
require '../../includes/config/database.php';
$db = conectarDB();

//obtener datos de la propiedad
$consulta = "SELECT * FROM propiedades WHERE id= $id ";
$resultado = mysqli_query($db, $consulta);
$propiedad = mysqli_fetch_assoc($resultado);


//consultar obtener vendedores
$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

//Arreglo con mensajes de errores
$errores = [];

$titulo = $propiedad['titulo'];
$precio = $propiedad['precio'];
$descripcion = $propiedad['descripcion'];
$habitaciones = $propiedad['habitaciones'];
$wc = $propiedad['wc'];
$estacionamiento = $propiedad['estacionamiento'];
$vendedores_Id = $propiedad['vendedores_id'];
$imagen = $propiedad['imagen'];



//ejecutar el codigo despues de que el usuario envia el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // echo "<pre>";
    // var_dump($_POST);
    // echo "</pre>";

    
    // echo "<pre>";
    //  var_dump($_FILES);
    //   echo "</pre>";

    $imagen = $_FILES['imagen'];

    $titulo = mysqli_real_escape_string($db, $_POST['titulo']);
    $precio = mysqli_real_escape_string($db, $_POST['precio']);
    $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
    $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
    $wc = mysqli_real_escape_string($db, $_POST['wc']);
    $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
    $vendedores_Id = mysqli_real_escape_string($db, $_POST['vendedores_Id']);
    $creado = date("Y-m-d");

    //asignar files a una variable

    if (!$titulo) {
        $errores[] = "debes añadir el titulo de la propiedad";
    }

    if (!$precio) {
        $errores[] = "debes añadir el precio de la propiedad";
    }

    if (strlen($descripcion) < 50) {
        $errores[] = "la descripción de la propiedad debe tener al menos 50 caracteres";
    }

    if (!$habitaciones) {
        $errores[] = "debes añadir el número de habitaciones de la propiedad";
    }

    if (!$wc) {
        $errores[] = "debes añadir el número de baños de la propiedad";
    }

    if (!$estacionamiento) {
        $errores[] = "debes añadir el número de estacionamientos de la propiedad";
    }

    if (!$vendedores_Id) {
        $errores[] = "debes añadir el vendedor de la propiedad";
    }

    //validar por tamaaño 1 maximo
    $medida = 1000 * 1000;
    if ($imagen['size'] > $medida) {
        $errores[] = "la imagen de la propiedad debe tener un tamaño maximo de 100kb";
    }



    // echo "<pre>";
    // var_dump($errores);
    // echo "</pre>";
    // exit;



    //revisar que el arreglo de errores esté vacío
    if (empty($errores)) {

        //crear carpeta imagenes
        $carpetaImagenes = '../../imagenes';


        if (!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        $nombreImagen='';

        /* Subida de archivos */
        if ($imagen['name']){
        
            
        //eliminar imagen anterior
            unlink($carpetaImagenes . $propiedad['imagen']);
            //generar un nombre unico para la imagen
        $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

         //subir la imagen
        move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . '/' . $nombreImagen);
        } else {
            $nombreImagen = $propiedad['imagen'];
        }
        
        //insertar base de datos
        $query = "UPDATE propiedades SET titulo = '$titulo', precio = '$precio', imagen = '$nombreImagen', descripcion = '$descripcion', habitaciones = '$habitaciones', wc = '$wc', 
        estacionamiento = '$estacionamiento', vendedores_id = '$vendedores_Id' WHERE id = $id";

        $resultado = mysqli_query($db, $query);
        if ($resultado) {
            //echo "Se ha creado la propiedad correctamente";

            //rediccionar al usuario a la página principal
            header("Location: /admin?resultado=2");
        }
    }


    //insertar en la base de datos

}



incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Actualizar Propiedad</h1>



    <a href="/admin" class="boton-verde">Volver</a>

    <?php foreach ($errores as $error):  ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>


    <form class="formulario" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>Información General</legend>

            <label for="titulo">Titulo de la Propiedad:</label>
            <input type="text" id="titulo" name="titulo" placeholder="Tu Titulo" value="<?php echo $titulo; ?>">

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" placeholder="precio propiedad" value="<?php echo $precio; ?>">

            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" accept="image/jpeg,image/png" name="imagen">

            <img src="/imagenes/<?php echo $imagen; ?>" class="imagen-small">

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion"><?php echo $descripcion; ?> </textarea>
        </fieldset>

        <fieldset>
            <legend>Actualizar la Propiedad</legend>

            <label for="habitaciones">Habitaciones:</label>
            <input type="number" id="habitaciones" name="habitaciones" placeholder="ej: 3" min="1" max="9" value="<?php echo $habitaciones; ?>">

            <label for="wc">Baños</label>
            <input type="number" id="wc" name="wc" placeholder="ej: 3" min="1" max="9" value="<?php echo $wc; ?>">

            <label for="estacionamiento">Estacionamiento:</label>
            <input type="number" id="estacionamiento" name="estacionamiento" placeholder="ej: 3" min="1" max="9" value="<?php echo $estacionamiento; ?>">
        </fieldset>

        <fieldset>
            <legend>Vendedor</legend>

            <select name="vendedores_Id">
                <option value="">--Selecciona un vendedor--</option>
                <?php while ($vendedor = mysqli_fetch_assoc($resultado)): ?>
                    <option <?php echo $vendedores_Id == $vendedor['id'] ? 'selected' : ''; ?> value="<?php echo $vendedor['id']; ?>">
                        <?php echo $vendedor['nombre'] . " " . $vendedor['apellido']; ?>
                    </option>

                <?php endwhile; ?>
            </select>
        </fieldset>

        <input type="submit" value="Actualizar Propiedad" class="boton-verde">
    </form>
</main>

<?php
incluirTemplate('footer');
?>