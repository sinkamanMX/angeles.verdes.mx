<?php   
   //$soap_client = new SoapClient("http://www.grupouda.com.mx/wbs/wbsAppTerceros.php?wsdl");
   
   $soap_client = new SoapClient("http://192.168.6.116/wbs_sima.php?wsdl");

//$id_usuario,$equipo, $push_token,$latitud, $longitud, $velocidad, $altitud, $angulo, $fecha, $proveedor,$error
   $param = array('usuario' => 'instalador',
                  'password' => '123456',
                  'tipo' => 'A',
                  'push_token' => '123');
    print_r($param);
    $result=$soap_client->__call('Login',$param);

    //echo '<br>:'.$result.'<br>';
    print_r($result);  
    //echo '<br>:'.$result.'<br>';
//   echo strlen($result);
   // print_r($result);  
?>
