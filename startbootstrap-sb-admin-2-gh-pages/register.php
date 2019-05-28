<?php

require 'conexion.php';
require 'funcs.php';

$errors = array();
if(!empty($_POST))
{
      $nombre = $mysqli->real_escape_string($_POST['nombre']);
      $usuario = $mysqli->real_escape_string($_POST['usuario']);
   
    $password = $mysqli->real_escape_string($_POST['password']);
    $con_password = $mysqli->real_escape_string($_POST['con_password']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $captcha = $mysqli->real_escape_string($_POST['g-recaptcha-response']);
   

    $activo =0;
    $tipo_usuario =2;
    $secret = '6Lem8aUUAAAAALuiRt-Ml2OSY4Myo5rGXH-qScqP';

    if($captcha){
      $errors[] = "Por favor verifica el captcha";
    }
        if(isNull($nombre, $usuario, $password, $con_password, $email))
    {
      $errors[] = "Debe llenar todos los campos";
    }
        if(!isEmail($email))
    {
      $errors[] = "Dirección de correo inválida";
    }
    
    if(!validaPassword($password, $con_password))
    {
      $errors[] = "Las contraseñas no coinciden";
    }   
    
    if(usuarioExiste($usuario))
    {
      $errors[] = "El nombre de usuario $usuario ya existe";
    }
    
    if(emailExiste($email))
    {
      $errors[] = "El correo electronico $email ya existe";
    }
    if(count($errors) == 0)
    {
      
      $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha");
      
      $arr = json_decode($response, TRUE);
      
      // if($arr['succes'])
      // {
        
        $pass_hash = hashPassword($password);
        $token = generateToken();
        
        $registro = registraUsuario($usuario, $pass_hash, $nombre, $email, $activo, $token, $tipo_usuario);     
        if($registro > 0)
        {       
          $url = 'http://'.$_SERVER["SERVER_NAME"].'/startbootstrap-sb-admin-2-gh-pages/activar.php?id='.$registro.'&val='.$token;
          
          $asunto = 'Activar Cuenta - Sistema de Usuarios';
          $cuerpo = "Estimado $nombre: <br /><br />Para continuar con el proceso de registro, es indispensable de click en la siguiente liga <a href='$url'>Activar Cuenta</a>";
          
          if(enviarEmail($email, $nombre, $asunto, $cuerpo)){
            
            echo "Para terminar el proceso de registro siga las instrucciones que le hemos enviado la direccion de correo electronico: $email";
            echo "<br><a href='index.html' >Iniciar Sesion</a>";
            exit;
            } else {
            $erros[] = "Error al enviar Email";
          }
          
          } else {
          $errors[] = "Error al Registrar";
        }
        
      //   } else {
      //   $errors[] = 'Error al comprobar Captcha';
      // }
}
}





?>


<html>

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Registro</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <script src='https://www.google.com/recaptcha/api.js'></script>
  <script src="https://www.google.com/recaptcha/api.js?render=reCAPTCHA_site_key"></script>
  <style>
  .imagen_registro{
    background: url("https://img.chilango.com/2017/11/ILUMINACIO%CC%81N-NARANJA-CDMX-2.jpg");
  background-position: center;
  background-size: cover;
}
.fondo {
  background-color: #000000;

  background-size: cover;
}
</style>

</head>

<body class="fondo">

  <div class="container">
      <div id="signupbox" style="margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info">
          <div class="panel-heading">
            <div class="panel-title">Reg&iacute;strate</div>
            <div style="float:right; font-size: 85%; position: relative; top:-10px"><a id="signinlink" href="index.php">Iniciar Sesi&oacute;n</a></div>
          </div>  
          
          <div class="panel-body" >
            
            <form id="signupform" class="form-horizontal" role="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" autocomplete="off">
              
              <div id="signupalert" style="display:none" class="alert alert-danger">
                <p>Error:</p>
                <span></span>
              </div>
              
              <div class="form-group">
                <label for="nombre" class="col-md-3 control-label">Nombre:</label>
                <div class="col-md-9">
                  <input type="text" class="form-control" name="nombre" placeholder="Nombre" value="<?php if(isset($nombre)) echo $nombre; ?>" required >
                </div>
              </div>
              
              <div class="form-group">
                <label for="usuario" class="col-md-3 control-label">Usuario</label>
                <div class="col-md-9">
                  <input type="text" class="form-control" name="usuario" placeholder="Usuario" value="<?php if(isset($usuario)) echo $usuario; ?>" required>
                </div>
              </div>
              
              <div class="form-group">
                <label for="password" class="col-md-3 control-label">Password</label>
                <div class="col-md-9">
                  <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
              </div>
              
              <div class="form-group">
                <label for="con_password" class="col-md-3 control-label">Confirmar Password</label>
                <div class="col-md-9">
                  <input type="password" class="form-control" name="con_password" placeholder="Confirmar Password" required>
                </div>
              </div>
              
              <div class="form-group">
                <label for="email" class="col-md-3 control-label">Email</label>
                <div class="col-md-9">
                  <input type="email" class="form-control" name="email" placeholder="Email" value="<?php if(isset($email)) echo $email; ?>" required>
                </div>
              </div>
              
              <div class="form-group">
                <label for="captcha" class="col-md-3 control-label"></label>
                <div class="g-recaptcha col-md-9" data-sitekey="6Lem8aUUAAAAAC6b9OVAKTXr67gzrWJEkHG8L0LY"></div>
              </div>
              
              <div class="form-group">                                      
                <div class="col-md-offset-3 col-md-9">
                  <button id="btn-signup" type="submit" class="btn btn-info"><i class="icon-hand-right"></i>Registrar</button> 
                </div>
              </div>
            </form>
            <?php echo resultBlock($errors); ?>
          </div>
        </div>
      </div>
    </div>

  <!-- Bootstrap core JavaScript-->
  <script>
    
</script>
    }
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

</body>

</html>
