<html>    
    <body>
    <?php
        require_once 'config.php';
        $valNombre = "";
        $valApellidos = "";
        $valEmail = "";
        $errores = [];
        if(isset($_POST['envio'])){
            $filtros = Array(
                'nombre'=> FILTER_SANITIZE_STRING,
                'apellidos'=> FILTER_SANITIZE_STRING,
                'email'=> FILTER_SANITIZE_EMAIL
            );
            $entradas = filter_input_array(INPUT_POST, $filtros);
            $valNombre = $entradas['nombre'];
            $valApellidos = $entradas['apellidos'];
            $valEmail = $entradas['email'];
            $valPassword = $_POST['password'];

            if(empty($entradas['nombre'])){
                $errores[] = 'Introduzca el nombre.';
            }
            if(empty($entradas['apellidos'])){
                $errores[] = 'Introduzca los apellidos';
            }
            if(empty($valPassword)){
                $errores[] = 'Introduzca la contraseña';
            }
            if(empty($entradas['email'])){
                $errores[] = "El email es obligatorio";
            } elseif(!preg_match("/@almacen\.com$/i", $valEmail)){
                $errores[] = "El email debe seguir el formato correo@almacen.com";
            }

            if(empty($errores)){
                $nombreSql = mysqli_real_escape_string($con, $entradas['nombre']);
                $apellidosSql = mysqli_real_escape_string($con, $entradas['apellidos']);
                $emailSql = mysqli_real_escape_string($con, $entradas['email']);

                $sql = "SELECT id_usuario FROM usuario WHERE email = '$emailSql'";
                $result = mysqli_query($con, $sql);

                if(mysqli_num_rows($result) > 0){
                    $errores[] = '<h1>ERROR: Email ya registrado.</h1>';
                } else {
                    $passwordHash = password_hash($valPassword, PASSWORD_DEFAULT);
                    $passwordSql = mysqli_real_escape_string($con, $passwordHash);

                    $insertarSql = "INSERT INTO usuario (nombre, apellidos, email, password, id_rol)
                                    VALUES ('$nombreSql', '$apellidosSql', '$emailSql', '$passwordSql' , 3)";
                    $insertarResult = mysqli_query($con, $insertarSql);

                    if($insertarResult){
                        echo '<h1>Registro completado con &eacute;xito</h1>';
                        $valNombre = "";
                        $valApellidos = "";
                        $valEmail = "";
                    } else {
                        $errores[] = ("Error al ejecutar la consulta: " . mysqli_error($con));
                    }
                }
            } if(!empty($errores)) {
                echo '<div style="color:red"><ul>';
                foreach($errores as $er) echo "<li>$er</li>";
                echo '</ul></div>';
            }

            if(isset($con)){
                mysqli_close($con);
            }
        }
    ?>

    <h1>Formulario de Registro</h1>
    <form method="POST" action="signup.php">
        Nombre: <input type="text" name="nombre" value="<?php echo $valNombre; ?>"><br/>
        
        Apellidos: <input type="text" name="apellidos" value="<?php echo $valApellidos; ?>"></br>
        
        Email: <input type="text" name="email" value="<?php echo $valEmail; ?>"></br>
        
        Contrase&ntilde;a: <input type="password" name="password"></br>
        
        <input type="submit" name="envio" value="Registrarse">
    </body>
</html>