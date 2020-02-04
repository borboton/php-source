<?
define('LDAP_OPT_DIAGNOSTIC_MESSAGE', 0x0032);
// require_once('Autenticacion/auth_SA.php');
// require_once('Autenticacion/auth.php');
$sistema = $_GET['sis'];
if($sistema == 'scannet'){
	require_once('Autenticacion/auth.php');
} else {
require_once('Autenticacion/auth_SA.php');
}
$usuarios = array("u585717","u166505","u186441","u584635");
$username=$_GET['usuario'];
$ldap_config = Datos_LDAP_Server();
$ldaphost=$ldap_config['server'];
$username2= Descrypt($ldap_config['user']);
$password2 = Descrypt($ldap_config['pass']);

$dn = "ou=UsuariosEspeciales,ou=Usuarios,o=Telecom";
$ds= ldap_connect($ldaphost,636);
if ($ds) {

	@ldap_set_option($ds, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error);
	$dn2 = "ou=UsuariosEspeciales,ou=Usuarios,o=Telecom";
	echo "  ---   conectando con User: ".$username2." -> ";
	$bind = ldap_bind($ds, "cn=$username2,".$dn2, $password2);
	if (!$bind){
        echo "Error Binding to LDAP: $extended_error";
		die("FAIL.<br>");
	}else{
		echo "OK. <br>";
	   $search2 = @ldap_search($ds, "ou=Personas, ou=Usuarios, o=Telecom","uid=$username");
	   $count_ldap2 = @ldap_count_entries($ds, $search2);
       if($count_ldap2>0){
			$info2 = @ldap_get_entries($ds, $search2);
			 $users[0]["NOMBRE"] = $info2[0]["fullname"][0];
				$users[0]["UID"] = $info2[0]["uid"][0];
				$users[0]["LOCALIDAD"] = $info2[0]["tlocalidad"][0];
				$users[0]["EDIFICIO"] = $info2[0]["tbuildingname"][0];
				$users[0]["CELULAR"] = $info2[0]["mobile"][0];
				$users[0]["MAIL"] = $info2[0]["mail"][0];
				$users[0]["TELEFONO"] = $info2[0]["telephonenumber"][0];
				$users[0]["PUESTO"] = $info2[0]["titulopuesto"][0];
				$users[0]["OU"] = $info2[0]["ou"][0];
				$users[0]["NRF"] = $info2[0]["nrfmemberof"];
                echo "<pre>";
				print_r($users);
                echo "</pre><br>";
				
			
				$searchSA = @ldap_search($ds, "cn=SCANNETSA,cn=TECO000407,cn=Level10,cn=RoleDefs,cn=RoleConfig,cn=AppConfig,cn=UserApplication,cn=DriverSet1,ou=Servicios,o=Telecom","equivalentToMe=cn=$username,ou=Personas, ou=Usuarios, o=Telecom");
			   $count_ldapSA = @ldap_count_entries($ds, $searchSA);
			   // echo "count_ldapSA: ".$count_ldapSA."<br>";
			  // $infoSA = @ldap_get_entries($ds, $searchSA);
			   // print_r($infoSA);
				if($count_ldapSA>0)
				{
					 $infoSA = @ldap_get_entries($ds, $searchSA);
					 echo "Rol en SCANNET-SA: ".$infoSA[0]["tvalue"][0]."<br>";
				}else{
					echo "  ---  No se encontro el rol en scannet-sa para el usuario ".$username."<br>";
				}
				$searchSA = @ldap_search($ds, "cn=SCANNETSA,cn=TECO000407,cn=Level10,cn=RoleDefs,cn=RoleConfig,cn=AppConfig,cn=UserApplication,cn=DriverSet1,ou=Servicios,o=Telecom","equivalentToMe=cn=*,ou=Personas, ou=Usuarios, o=Telecom");
			   // echo "count_ldapSA: ".$count_ldapSA."<br>";
			   $infoSA = @ldap_get_entries($ds, $searchSA);
			    print_r($infoSA);
				$search = @ldap_search($ds, "cn=SVPN, cn=41981, cn=Level10, cn=RoleDefs, cn=RoleConfig, cn=AppConfig, cn=UserApplication, cn=DriverSet1, ou=Servicios, o=Telecom","equivalentToMe=cn=$username,ou=Personas, ou=Usuarios, o=Telecom");
			   $count_ldap = @ldap_count_entries($ds, $search);
				if($count_ldap>0)
				{
					 $info = @ldap_get_entries($ds, $search);
					 echo "Rol en SCANNET: ".$info[0]["tvalue"][0]."<br>";
				}else{
					echo "  ---  No se encontro el rol en scannet para el usuario ".$username."<br>";
				}
	   }else{
			die("  ---  No se encontro el usuario ".$username."<br>");
	   }
		
	   
	
	
	
	
	}
} else {
echo "Unable to connect to LDAP server";
}

exit;
?>