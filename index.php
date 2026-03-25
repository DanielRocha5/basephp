<?php
session_start();
include 'conexion.php';
$errores=[];
$email = '';
$nombre = '';
$password = '';
$password_verify = '';
$mensaje = '';



if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $nombre = $_POST['nombre'];
    $password = $_POST['password'];
    $password_verify = $_POST['password_verify'];

    $_SESSION['email'] = $email;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['password'] = $password;
    $_SESSION['password_verify'] = $password_verify;

    if(empty($email)){
        $errores['email'] = "* Ingresa el Gmail.";
    }
    elseif(!strpos($email, '@')){
        $errores['email'] = "* Falta el @ en el gmail";
    }
    elseif(!strpos($email, '.')){
        $errores['email'] = "* Falta un .";
    }

    if(empty($nombre)){
        $errores['nombre'] = "* Este campo es obligatorio";
    }

    if(empty($password)){
        $errores['password'] = "* Este campo es obligatorio";
    }
    elseif(strlen($password)<8){
        $errores['password'] = "* Debe tener mas de 8 caracteres";
    }
    elseif(strtolower($password) === $password){
        $errores['password'] = "* Debe tener al menos 1 mayuscula.";
    }
    elseif(strtoupper($password) === $password){
        $errores['password'] = "* Debe contener minusculas";
    }
    elseif(ctype_alnum($password)){
        $errores['password'] = "* Debe contener un caracter especial";
    }

    if(empty($password_verify)){
        $errores['password_verify'] = "* Este campo es obligatorio.";
    }
    elseif($password_verify !== $password){
        $errores['password_verify'] = "* Las contraseñas no coinciden.";
    }

    if(!empty($errores)){
        $mensaje = "* Debes llenar todos los campos correctamente";
    }

    if(empty($errores)){
        $val = $pdo->prepare("SELECT * FROM usuario WHERE email = ?");
        $val->execute([$email]);

        $valida_ex = $val->fetch();
        if($valida_ex){
            $errores['email'] = "* Ya se encuentra registrado este usuario.";
            $mensaje = "* Este correo ya esta registrado";
        }
        else{
            $password_encrip = password_hash($password, PASSWORD_BCRYPT);
            $val1 = $pdo->prepare("INSERT INTO usuario (email, nombre, password) VALUES (?, ?, ?)");
            $val1->execute([$email, $nombre, $password_encrip]);
            session_destroy();
            session_start();
            $_SESSION['mensaje'] = "Usuario registrado exitosamente";
            header('Location: index.php');
            exit();
        }
    }
}

if(isset($_SESSION['mensaje'])){
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="Contenedor">
        <h1>Registrar Usuario</h1>

        <?php if(!empty($mensaje)){ ?>
        <div class="<?php echo empty($errores) ? 'bg-green-100 border border-green-400 text-green-600' : 'bg-red-100 border border-red-400 text-red-600'; ?> text-sm p-3 rounded mb-4">
        <?php echo $mensaje; ?>
        </div>
        <?php 
        } 
        ?>

        <form action="" method="post">

            <label>Correo Electronico</label>
            <input type="text" name="email" value="<?php echo $email;?>">
            <?php 
            if(!empty($errores['email'])){ 
                echo "<span class='text-red-400 text-sm'>".$errores['email']."</span>"; 
            } 
            ?>

            <label>Nombre de Usuario</label>
            <input type="text" name="nombre" value="<?php echo $nombre;?>">
            <?php 
            if(!empty($errores['nombre'])){
                 echo "<span class='text-red-400 text-sm'>".$errores['nombre']."</span>"; 
            } 
            ?>

            <label>Contraseña</label>
            <input type="password" name="password">
            <?php 
            if(!empty($errores['password'])){
                 echo "<span class='text-red-400 text-sm'>".$errores['password']."</span>"; 
            } 
            ?>

            <label>Verificar Contraseña</label>
            <input type="password" name="password_verify">
            <?php 
            if(!empty($errores['password_verify'])){
                 echo "<span class='text-red-400 text-sm'>".$errores['password_verify']."</span>"; 
            } 
            ?>

            <button type="submit" name="submit" value="Registrar">Registrar</button>
        </form>
    </div>
</body>
</html>