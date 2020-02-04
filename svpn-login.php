<?php
require_once('./Autenticacion/auth.php');
require_once('./conf/mobile_detect.php');
if(isset($_GET["var"]))
{
	$error="Su sesion a caducado, por favor cierre todas las ventanas de ScanNet e ingrese nuevamente";
}
session_start(); 
session_destroy();
if($_SERVER['HTTP_HOST']=='svpn')
{
  header('Location: http://scannet/login.php');
}
if(isset($_SERVER['HTTPS']))
{
  header('Location: http://'.$_SERVER['HTTP_HOST'].'/login.php');
}
if(preg_match('/(?i)msie [5-9]/',$_SERVER['HTTP_USER_AGENT']))// Chequea version de explorer
{
  header('Location: navegador.php');
}
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$server_name = gethostbyaddr($_SERVER['SERVER_ADDR']);
if($server_name=='plnx0164.telecom.com.ar' || $server_name=='dlnx0092.telecom.com.ar'){
  $_SESSION['app']='';
}else{
    $_SESSION['app']='SA';
}
$scriptVersion = $detect->getScriptVersion();
if( isset($_POST['usuario']) and isset($_POST['password']) and strcmp($_POST['password'],"")!=0 and strcmp($_POST['usuario'],"")!=0)
{
                 $username = strtolower(trim($_POST['usuario']));
                 $password = trim($_POST['password']);
                 $ldap_config = Datos_LDAP_Server();
                 $server[1]=$ldap_config['server'];
         $opcion=1;
   if($opcion==1)
    {
                $ds = @ldap_connect($server[$opcion],636);
                $dn = "ou=Personas, ou=Usuarios, o=Telecom";
                @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
         if( !$ds )
           {
           	   $error="Error de conexion con LDAP server, ";
           }
         else
           {
           	   	$bind = @ldap_bind($ds, "cn=$username,".$dn, $password);
                         if( !$bind || !isset($bind))
                          {$error="Contrase&ntilde;a Incorrecta";}
                         else
                          {
                           $bind2 = @ldap_unbind($ds);
            		           $ds = @ldap_connect($server[$opcion],636);
                           @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
                           $dn2 = "ou=UsuariosEspeciales,ou=Usuarios,o=Telecom";
                           $username2 = Descrypt($ldap_config['user']);
                           $password2 = Descrypt($ldap_config['pass']);
                           $bind = @ldap_bind($ds, "cn=$username2,".$dn2, $password2);

                           $search = @ldap_search($ds, "cn=SVPN, cn=41981, cn=Level10, cn=RoleDefs, cn=RoleConfig, cn=AppConfig, cn=UserApplication, cn=DriverSet1, ou=Servicios, o=Telecom","equivalentToMe=cn=$username,ou=Personas, ou=Usuarios, o=Telecom");
                           $count_ldap = @ldap_count_entries($ds, $search);
             if($count_ldap>0)
              {
                               $search2 = @ldap_search($ds, "ou=Personas, ou=Usuarios, o=Telecom","uid=$username");
                               $count_ldap2 = @ldap_count_entries($ds, $search2);
                               $info = @ldap_get_entries($ds, $search);
                                if($count_ldap2>0)
                                  $info2 = @ldap_get_entries($ds, $search2);
                                            session_start();
                                            $_SESSION['Auth']="Autorizado";
                                            $_SESSION['Usr']=$username;
                                            $nombreRecibido = str_replace("'", "", $info2[0]["fullname"][0]);
                                            $_SESSION['Nombre']= $nombreRecibido;
                                            $_SESSION['NombreANT'] = "NO";
											if($username=='u195431'){
												$_SESSION['Rol']='Operadores';
											}else{
												$_SESSION['Rol']=$info[0]["tvalue"][0];
											}
                                            
                                            $_SESSION['Legajo']=$info2[0]["tlegajo"][0];
                                            $_SESSION['ip']=$_SERVER['REMOTE_ADDR'];
                                                if($server_name=='plnx0164.telecom.com.ar' ){
                                                      $_SESSION['url_server']="ws://svpnpc1:2116";
                                                      $_SESSION['url_notificaciones']="ws://svpnpc1:2117";
                                                      $_SESSION['app']='';
                                                }else{
                                                      if($server_name=='dlnx0092.telecom.com.ar'){
                                                          $_SESSION['url_server']="ws://10.66.33.184:2116";
                                                          $_SESSION['url_notificaciones']="ws://10.66.33.184:2117";
                                                          $_SESSION['app']='';
                                                      }else{
                                                                if($server_name=='scannetdes'){
                                                                    $_SESSION['app']='SA';
                                                                    $_SESSION['url_server']="ws://10.245.107.135:2116";
                                                                    $_SESSION['url_notificaciones']="ws://10.245.107.135:2117";
                                                                }else{
                                                                    $_SESSION['app']='SA';
                                                                    $_SESSION['url_server']="ws://plscannetapp1:2116";
                                                                    $_SESSION['url_notificaciones']="ws://plscannetapp1:2117";
                                                                }
                                                        }
                                                }
                                                if($_SESSION['Usr']=='u185128'){
                                                  $_SESSION['Rol']='Administrador';
                                                }
                                            $_SESSION['Sesion']="0";
                                            $_SESSION['navegador']=$deviceType;
                                }else{
                                    $error="Usuario no valido para la Aplicaci&oacute;n, solicitar alta de perfil a traves de TuId";
                                }
                          @ldap_close($ds);
                    } // Fin
          } //Fin prueba conexion
         }
        } //Fin If inicial
if($_SESSION['Auth']=="Autorizado")
{
	if($deviceType=="computer"){
		header('Location: index.php');
	}else{
		//header('Location: mobile.php');
    header('Location: index.php');
	}
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ScanNet  <?php echo $_SESSION['app'] ?> | Inicio</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="lib/bootstrap/css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="lib/bootstrap/css/font-awesome.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="lib/adminLTE/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="lib/adminLTE/css/AdminLTE.css">
    <link rel="icon" type="image/png" sizes="32x32" href="img/logo_s.png">
<script>
var error="<?php echo $error?>";
</script>
<style>
.lockscreen-image {
    border-radius: 50%;
    position: absolute;
    left: -21px; 
    top: -7px;
    background: #fff;
    padding: 5px;
    z-index: 10;
}
small, .small {
      font-size: 65%;
}
</style>
  </head>
  <body onload="errores();img();"class="hold-transition lockscreen">
    <!-- Automatic element centering -->
    <div class="lockscreen-wrapper">
      <div class="lockscreen-logo">
        <a href="login.php"><b>Scan</b>Net</a><small>  <?php echo $_SESSION['app'] ?></small> 
      </div>
      <!-- User name -->
      <!-- START LOCK SCREEN ITEM -->
      <div class="lockscreen-item">
	      <div class="lockscreen-image">
		  <img src="lib/scannet/media/default_profile_2.jpg" id="img" alt="User Image">
	      </div>
	        <form id="formulario" action="" method="POST" class="lockscreen-credentials">
              <div class="input-group">
                <input id="usuario"  name="usuario" type="text" class="form-control" placeholder="Usuario" value="<?php echo $_POST['usuario'];?>">
              </div>
              <div class="input-group">
                <input id="password"  name="password" type="password" class="form-control" placeholder="Contrase&ntilde;a">
                <div class="input-group-btn">
                  <button class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
                </div>
              </div>
          </form><!-- /.lockscreen credentials -->
      </div><!-- /.lockscreen-item -->
      <div class="help-block text-center">
        Ingrese con sus credenciales de LAN
      </div>
      <div id="mje"></div>
      <div class="lockscreen-footer text-center">
        Copyright &copy; 2016 <b><a href="#" class="text-black">ScanNet</a></b><br>
        Todos los derechos reservados
      </div>
    </div><!-- /.center -->
  </body>
    <script src="lib/jquery/js/jquery2.1.4.js"></script>
    <script src="lib/bootstrap/js/bootstrap.js"></script>
    <script src="js/login.js"></script>
    <script>$("#usuario").focusout(function(){img();});
    </script>  
</html>
