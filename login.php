<?php

require 'includes/config/database.php'; 
$db = conectarDB();
//auntentica el usuario

$errores = [];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // echo "<pre>";
    // var_dump($_POST);
    // echo "</pre>";


    $email = mysqli_real_escape_string($db, filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
    $password = mysqli_real_escape_string($db, $_POST['password']);


    if (!$email){
        $errores[] = "el email no es valido";
    }

    if (!$password){
        $errores[] = "la contraseña no es valida";
    }

    if (empty($errores)){
        //revisar si el usuario existe

        $query = "SELECT * FROM usuarios WHERE email = '$email'";
        $resultado = mysqli_query ($db, $query);
        

        if ($resultado->num_rows){
            //revisar si la contraseña es correcta
            $usuario = mysqli_fetch_assoc($resultado);



            //verificar si la contraseña es correcta
            $auth = password_verify($password, $usuario['password']);

            if ($auth){
                //el usuario existe esta autenticado
                session_start();

                //llenar el arreglo de la sesion
                $_SESSION['usuario'] = $usuario['email'];
                $_SESSION['login'] = true;

                header('Location: /admin');

            }else{
                $errores[] = "el password no es correcto";
            }
        } else{
            $errores[] = "el usuario no existe";
        }   
    }
}

//Incluye el header

require 'includes/funciones.php';
incluirTemplate ('header');
?> 

    <main class="contenedor seccion contenido-centrado">
        <h1>Iniciar Sesion</h1>

        <?php foreach ($errores as $error): ?> 
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
        <?php endforeach; ?>

        <form method="POST" class="formulario">
            <fieldset>
                <legend>Email Y Password</legend>
                
                <label for="email">E-mail</label>
                <input type="email" name="email" placeholder="Tu Email" id="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Tu Password" id="password" required>
            </fieldset>

            <input type="submit" value ="Iniciar Sesion" class="boton-verde">
        </form>
    </main>

<?php
    incluirTemplate ('footer');
?>