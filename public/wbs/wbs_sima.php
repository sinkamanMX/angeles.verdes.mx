<?php
  set_time_limit(60000);
  require_once('./lib/nusoap.php'); 
  //include('functions_solcat.php');
  $miURL = 'http://201.131.96.37/wbs_sima';
  $server = new soap_server(); 
  $server->configureWSDL('wbs_sima', $miURL); 
  $server->wsdl->schemaTargetNamespace=$miURL;

  $server->register('RecuperarPassword', // Nombre de la funcion 
                   array('usuario' => 'xsd:string'), // Parametros de entrada 
                   array('return' => 'xsd:string'), // Parametros de salida 
                   $miURL);

  $server->register('Login', // Nombre de la funcion 
                    array('usuario'     => 'xsd:string',
                          'password'    => 'xsd:string',
                          'tipo'        => 'xsd:string',
                          'push_token'  => 'xsd:string'), // 
                    array('return' => 'xsd:string'), // Parametros de salida 
                    $miURL); 
  $server->register('GetPosition', // Nombre de la funcion 
                    array('imei'     => 'xsd:string',
                          'evento'    => 'xsd:string',
                          'fecha_tel'        => 'xsd:string',
                          'bateria'        => 'xsd:string',
                          'dbm'        => 'xsd:string',
                          'gps_vel'        => 'xsd:string',
                          'gps_lon'        => 'xsd:string',
                          'gps_lat'        => 'xsd:string',
                          'gps_alt'        => 'xsd:string',
                          'gps_ang'        => 'xsd:string',
                          'gps_radio'        => 'xsd:string',
                          'gps_prov_gps'        => 'xsd:string',
                          'gps_fecha'        => 'xsd:string'), // 
                    array('return' => 'xsd:string'), // Parametros de salida 
                    $miURL); 
  $server->register('GetForm',
  	                array('usuario' => 'xsd:string',
  	                	  'password' => 'xsd:string',
  	                	  'id_cita' => 'xsd:string',
  	                	  'id_formulario' => 'xsd:string',
  	                	  'imei' => 'xsd:string',
  	                	  'latitud' => 'xsd:string',
  	                	  'longitud' => 'xsd:string',
  	                	  'altitud' => 'xsd:string',
  	                	  'angulo' => 'xsd:string',
  	                	  'velocidad' => 'xsd:string',
  	                	  'fecha_telefono' => 'xsd:string',
  	                	  'fecha_gps' => 'xsd:string',
  	                	  'bateria' => 'xsd:string',
  	                	  'gps_error' => 'xsd:string',
  	                	  'gps_proveedor' => 'xsd:string',
  	                	  'respuestas' => 'xsd:string',
  	                	  'fecha_inicio' => 'xsd:string'),
  	                array('return' => 'xsd:string'), // Parametros de salida 
                    $miURL); 

  function checkEmail($email) {  
    $reg = "#^(((([a-z\d][\.\-\+_]?)*)[a-z0-9])+)\@(((([a-z\d][\.\-_]?){0,62})[a-z\d])+)\.([a-z\d]{2,6})$#i";  
    return preg_match($reg, $email);  
} 

  function RecuperaPass($con,$usName){
    global $base;
    $result = 0;
  $sql = "SELECT EMAIL
          FROM USUARIOS 
        WHERE USUARIO = '".$usName."'"; 
  if ($qry = mysqli_query($con,$sql)){
    if (mysqli_num_rows($qry) > 0){
      $row = mysqli_fetch_object($qry);
      $email = $row->EMAIL;
      if (checkEmail($usName)) {
        $mensaje = "Buen día,

Usted ha solicitado la recuperación de su password desde nuestra Aplicación Móvil.

Su password es: ".$pass."

En caso de que no haya solicitado su password, le recomendamos tome las medidas necesarias.

Atentamente 
Grupo UDA";
        if (envia_mail('',$usName, utf8_decode('Recuperación de password'), utf8_decode($mensaje),'no-reply@petlocator.com.mx','Servicios Pet Locator')){
          $result = '<?xml version="1.0" encoding="UTF-8"?> 
                  <space>
                    <Response> 
                      <Status>
                        <code>1</code>
                        <msg>'.utf8_decode('Su contraseña ha sido enviada al e-mail que proporciono para su registro.').'</msg>
                      </Status>
                    </Response>
                  </space>'; 
        } else {
          $result = '<?xml version="1.0" encoding="UTF-8"?> 
                  <space>
                    <Response> 
                      <Status>
                        <code>-1</code>
                        <msg>No fue posible completar el proceso. Intente mas tarde.</msg>
                      </Status>
                    </Response>
                  </space>'; 
        }
      } else {
        $result = '<?xml version="1.0" encoding="UTF-8"?> 
                  <space>
                    <Response> 
                      <Status>
                        <code>-2</code>
                        <msg>'.utf8_decode('No dispone de una cuenta de correo para el envío.').'</msg>
                      </Status>
                    </Response>
                  </space>';
      }
    } else {
      $result = '<?xml version="1.0" encoding="UTF-8"?> 
                  <space>
                    <Response> 
                      <Status>
                        <code>-3</code>
                        <msg>El usuario no esta registrado</msg>
                      </Status>
                    </Response>
                  </space>';
    }
    mysqli_free_result($qry);
  }
  return $result;
  }

  function RecuperarPassword($usName){
   
      $con = mysqli_connect("192.168.6.23","dba","t3cnod8A!","SIMA");
      if ($con){
        $res = RecuperaPass($con,$usName);
        mysqli_close($con);
      } else {
        $res = '<?xml version="1.0" encoding="UTF-8"?> 
                  <space>
                    <Response> 
                      <Status>
                        <code>-2</code>
                        <msg>Sucedio un problema de comunicación</msg>
                      </Status>
                    </Response>
                  </space>';  
      }
    
    return new soapval('return', 'xsd:string', $res);
  }

  /*LOGEO DE USUARIO*/
  function valida_usuario($usuario,$password,$con){
  	$res = 0;
  	$sql = "SELECT ID_USUARIO
  	        FROM USUARIOS
  	        WHERE UPPER(USUARIO) = UPPER('".$usuario."') AND
  	              PASSWORD = SHA1('".$password."')";
    if ($qry = mysqli_query($con,$sql)){
      $row = mysqli_fetch_object($qry);
      $res = $row->ID_USUARIO;
      mysqli_free_result($qry);
    }
    return $res;
  }

  function dame_extras_citas($id_cita,$con){
    $res = "<msg>No hay Extras</msg>";
    $sql = "SELECT TITULO,
                   VALOR
            FROM PROD_CITA_EXTRAS
            WHERE ID_CITA = ".$id_cita;
    if ($qry = mysqli_query($con,$sql)){
      //$row = mysqli_fetch_object($qry);
      if (mysqli_num_rows($qry) > 0){
      	$res = '';
      	while ($row = mysqli_fetch_object($qry)){
          $res = $res. "<extra>
                          <etiqueta>".($row->TITULO)."</etiqueta>
                          <valor>".($row->VALOR)."</valor>
                        </extra>";  
      	}
      }
      mysqli_free_result($qry);
    }
    return $res;
  }

  function dame_elementos($id_form,$con){
    $res = "<msg>No hay elementos</msg>";
  	$sql = "SELECT A.ID_ELEMENTO,
			       B.ID_TIPO,
			       B.DESCIPCION,
			       B.REQUERIDO,
			       B.VALORES_CONFIG,
			       C.DESCRIPCION AS TIPO_ELEMENTO
			FROM PROD_FORMULARIO_ELEMENTOS A 
			  INNER JOIN PROD_ELEMENTOS    B ON A.ID_ELEMENTO = B.ID_ELEMENTO
			  INNER JOIN PROD_TPO_ELEMENTO C ON B.ID_TIPO = C.ID_TIPO
			WHERE A.ID_FORMULARIO = ".$id_form." AND 
			      B.ACTIVO = 'S'
			ORDER BY A.ORDEN ASC";
    if ($qry = mysqli_query($con,$sql)){
      //$row = mysqli_fetch_object($qry);
      if (mysqli_num_rows($qry) > 0){
      	$res = '';
      	while ($row = mysqli_fetch_object($qry)){
          $res = $res."<elemento>
                         <id_elemento>".$row->ID_ELEMENTO."</id_elemento>
		                 <id_tipo>".$row->ID_TIPO."</id_tipo>
		                 <descripcion>".$row->DESCIPCION."</descripcion>
		                 <requerido>".$row->REQUERIDO."</requerido>
		                 <valores_conf>".$row->VALORES_CONFIG."</valores_conf>
		                 <tipo_elemento>".$row->TIPO_ELEMENTO."</tipo_elemento>
		               </elemento>";  
        }
      }
      mysqli_free_result($qry);
    }
  	return $res;
  }

  function dame_formularios($id_cita,$con){
  	$res = "<msg>No hay cuestionarios</msg>";
  	$sql = "SELECT B.ID_FORMULARIO,
  	               B.TITULO,
			       B.FOTOS_EXTRAS,
			       B.QRS_EXTRAS,
			       B.FIRMAS_EXTRAS,
			       B.LOCALIZACION
			FROM PROD_CITA_FORMULARIO    A
			  INNER JOIN PROD_FORMULARIO B ON A.ID_FORMULARIO = B.ID_FORMULARIO
			WHERE A.ID_CITA = ".$id_cita." AND
			      B.ACTIVO = 'S'";
    if ($qry = mysqli_query($con,$sql)){
     // $row = mysqli_fetch_object($qry);
      if (mysqli_num_rows($qry) > 0){
      	while ($row = mysqli_fetch_object($qry)){
          $res = $res."<form>
                         <configuracion>
	                         <id>".$row->ID_FORMULARIO."</id>
	                         <nombre>".$row->TITULO."</nombre>
	                         <fotos_extras>".$row->FOTOS_EXTRAS."</fotos_extras>
	                         <qrs_extras>".$row->QRS_EXTRAS."</qrs_extras>
	                         <firmas_extras>".$row->FIRMAS_EXTRAS."</firmas_extras>
	                         <localizacion>".$row->LOCALIZACION."</localizacion>
                         </configuracion>
                         <elementos> ".dame_elementos($row->ID_FORMULARIO,$con)."</elementos>
                       </form>";  
                       /*
                         */
        }
      }
      mysqli_free_result($qry);
    }
  	return $res;
  }

  function dame_citas($id_usuario,$con){
  	/*por el momento no se usa*/
  	$res = 0;
  	$sql = "SELECT A.ID_CITA,
  	               F.DESCRIPCION AS ESTATUS,
  	               A.FECHA_CITA,
  	               A.HORA_CITA,
  	               A.CONTACTO,
  	               A.TELEFONO_CONTACTO,
  	               A.FOLIO,
  	               CONCAT(E.CALLE,' ',E.NUMERO_INT,' ',E.NUMERO_EXT,' ',E.COLONIA,' ',E.MUNICIPIO,' ',E.ESTADO,' ',E.CP) AS DIRECCION_CITA,
  	               B.REFERENCIAS AS REF_CITA,
  	               B.CP AS CP_CITA,
  	               B.LATITUD AS LAT_CITA,
  	               B.LONGITUD AS LON_CITA,
  	               D.COD_CLIENTE,
  	               CONCAT(D.NOMBRE,' ',D.APELLIDOS) AS NOMBRE_CLIENTE,
                   D.TELEFONO_FIJO,
                   D.TELEFONO_MOVIL,
                   D.EMAIL,
                   CONCAT(E.CALLE,' ',E.NUMERO_INT,' ',E.NUMERO_EXT,' ',E.COLONIA,' ',E.MUNICIPIO,' ',E.ESTADO,' ',E.CP) AS DIRECCION_CLIENTE,
                   E.REFERENCIAS,
                   E.LATITUD,
                   E.LONGITUD  
  	        FROM PROD_CITAS A
  	          INNER JOIN PROD_CITA_DOMICILIO     B ON B.ID_CITA    = A.ID_CITA
  	          INNER JOIN PROD_CITA_USR           C ON C.ID_CITA    = A.ID_CITA
  	          INNER JOIN PROD_CLIENTES           D ON D.ID_CLIENTE = A.ID_CLIENTE
  	          INNER JOIN PROD_DOMICILIOS_CLIENTE E ON E.ID_CLIENTE = D.ID_CLIENTE
  	          INNER JOIN PROD_ESTATUS_CITA       F ON A.ID_ESTATUS = F.ID_ESTATUS
  	        WHERE C.ID_USUARIO = ". $id_usuario;
    if ($qry = mysqli_query($con,$sql)){
      //$row = mysqli_fetch_object($qry);
      $res = '';
      while ($row = mysqli_fetch_object($qry)){
        $res = $res."<cita>
                <datos>
	                <folio>".$row->ID_CITA."</folio>
	                <estatus>".$row->ESTATUS."</estatus>
	                <fecha>".$row->FECHA_CITA."</fecha>
	                <hora>".$row->HORA_CITA."</hora>
	                <contacto>".$row->CONTACTO."</contacto>
	                <tel_contacto>".$row->TELEFONO_CONTACTO."</tel_contacto>
	                <direccion_cita>".$row->DIRECCION_CITA."</direccion_cita>
	                <ref_cita>".$row->REF_CITA."</ref_cita>
	                <cp_cita>".$row->CP_CITA."</cp_cita>
	                <lat_cita>".$row->LAT_CITA."</lat_cita>
	                <lon_cita>".$row->LON_CITA."</lon_cita>
	                <cod_cliente>".$row->COD_CLIENTE."</cod_cliente>
	                <nombre_cliente>".$row->NOMBRE_CLIENTE."</nombre_cliente>
	                <tel_fijo>".$row->TELEFONO_FIJO."</tel_fijo>
	                <tel_movil>".$row->TELEFONO_MOVIL."</tel_movil>
	                <email>".$row->EMAIL."</email>
	                <direccion_cliente>".$row->DIRECCION_CLIENTE."</direccion_cliente>
	                <ref_cliente>".$row->REFERENCIAS."</ref_cliente>
	                <lat_cliente>".$row->LATITUD."</lat_cliente>
	                <lon_cliente>".$row->LONGITUD."</lon_cliente>
                </datos>
                <extras> ".dame_extras_citas($row->ID_CITA,$con)." </extras>
                <formularios> ".dame_formularios($row->ID_CITA,$con)." </formularios>
              </cita>";
        $id_cita = $row->ID_CITA;
      }
      //obtiene los extras de la cita
      //obtiene los formularios de la cita
      mysqli_free_result($qry);
    }
    return $res;
  }

  function Login($usuario,$password,$tipo,$push_token){
    $dat = '';
    $msg = 'No hay conexión con el servicio';
    $idx = -1;
    $con = mysqli_connect("192.168.6.23","dba","t3cnod8A!","SIMA");
    if ($con){
      $id_usuario = valida_usuario($usuario,$password,$con);
      if ($id_usuario > 0){
        //valida que tenga acceso al modulo <- por el momento no se uso
          //devuelve las citas
        $dat = dame_citas($id_usuario,$con);
        if (strlen($dat) > 0){
          $msg = 'OK';
          $idx = 0;
        } else {
          $msg = 'No hay citas asignadas';
          $idx = -3; 
        }
        
      } else {
        $msg = 'El usuario y/o contraseña es incorrecto';
        $idx = -2;
      }
      mysqli_close($con);
    } else {
       $msg = 'No hay conexión con el servicio';
       $idx = -1;
    }
    $res =  '<?xml version="1.0" encoding="UTF-8"?>
               <space>
                 <Response> 
                   <Status>
                     <code>'.utf8_encode($idx).'</code>
                     <msg>'.utf8_encode($msg).'</msg>
                   </Status>
                   '.($dat).'
                 </Response>
               </space>';
    return new soapval('return', 'xsd:string', $res);
  }
  /**/
  function valida_imei($imei,$con){
  	$res = -1;
  	$sql = "SELECT ID_TELEFONO
  	        FROM PROD_TELEFONOS
  	        WHERE IDENTIFICADOR = '".$imei."'";
  	if ($qry = mysqli_query($con,$sql)){
      $row = mysqli_fetch_object($qry);
      if ($row->ID_TELEFONO > 0){
        $res = $row->ID_TELEFONO;
      }
      mysqli_free_result($qry);
    }
    return $res;
  }

   function valida_evento($evento,$con){
  	$res = -1;
  	$sql = "SELECT ID_EVENTO
  	        FROM PROD_EVENTOS
  	        WHERE ETIQUETA = '".$evento."'";
  	if ($qry = mysqli_query($con,$sql)){
      $row = mysqli_fetch_object($qry);
      if ($row->ID_EVENTO > 0){
        $res = $row->ID_EVENTO;
      }
      mysqli_free_result($qry);
    }
    return $res;
  }

  function dame_google ($lat,$lng){
     $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false'; 
     $json = @file_get_contents($url); 
     $data=json_decode($json); 
     $status = $data->status; 
     if($status=="OK"){ 
       $address = $data->results[0]->formatted_address; 
     } else { 
       $address=''; 
     }
     $address = str_replace(' State of',' ',$address);
     $address = str_replace(' Mexico City', 'Ciudad de México',$address);
     $address = str_replace('Federal District', 'Distrito Federal',$address);
     $address = utf8_decode($address);
     return  $address;
   }

  function registra_evento($tabla,
  	                       $id_telefono,
		                   $id_evento,
		                   $fecha_tel,
		                   $bateria,
		                   $dbm,
		                   $gps_vel,
		                   $gps_lon,
		                   $gps_lat,
		                   $gps_alt,
		                   $gps_ang,
		                   $gps_radio,
		                   $gps_prov_gps,
		                   $gps_fecha, $con){
  	$res = -1;
    $ubicacion = dame_google($gps_lat,$gps_lon);
  	$sql = "INSERT INTO ".$tabla." (
  	          ID_EVENTO,
  	          ID_TELEFONO,
  	          FECHA_TELEFONO,
  	          FECHA_SERVIDOR,
  	          FECHA_GPS,
  	          TIPO_GPS,
  	          LATITUD,
  	          LONGITUD,
  	          VELOCIDAD,
  	          ANGULO,
  	          RADIO_ERROR,
  	          NIVEL_BATERIA,
  	          NIVEL_SENAL_RED,
              UBICACION
  	        ) VALUES (
  	          ".$id_evento.",
  	          ".$id_telefono.",
  	          '".$fecha_tel."',
  	          CURRENT_TIMESTAMP,
  	          '".$gps_fecha."',
  	          '".$gps_prov_gps."',
  	          ".$gps_lat.",
  	          ".$gps_lon.",
  	          ".$gps_vel.",
  	          ".$gps_ang.",
  	          ".$gps_radio.",
  	          '".$bateria."',
  	          '".$dbm."',
              '".$ubicacion."'
  	        )";
    if ($qry = mysqli_query($con,$sql)){
      $res= mysqli_insert_id(); 	
    }
    return $res;
  }


  function GetPosition($imei,
                       $evento,
                       $fecha_tel,
                       $bateria,
                       $dbm,
                       $gps_vel,
                       $gps_lon,
                       $gps_lat,
                       $gps_alt,
                       $gps_ang,
                       $gps_radio,
                       $gps_prov_gps,
                       $gps_fecha){
  	$dat = '';
    $msg = 'No hay conexión con el servicio';
    $idx = -1;
    $con = mysqli_connect("192.168.6.23","dba","t3cnod8A!","SIMA");
    if ($con){
  	//valida que el imei exista
      $id_equipo = valida_imei($imei,$con);
      $id_evento = valida_evento($evento,$con);
	  if ($id_equipo > 0){
        if ($id_evento > 0){
           $x = registra_evento('PROD_HISTORICO_POSICION',
  	                       $id_equipo,
		                   $id_evento,
		                   $fecha_tel,
		                   $bateria,
		                   $dbm,
		                   $gps_vel,
		                   $gps_lon,
		                   $gps_lat,
		                   $gps_alt,
		                   $gps_ang,
		                   $gps_radio,
		                   $gps_prov_gps,
		                   $gps_fecha, $con);
            $msg = 'OK '.$x;
	        $idx = 0;
        } else {
          $msg = 'El evento no esta registrado';
	      $idx = -3;	
        }
	  } else {
	    $msg = 'El IMEI no esta registrado';
	    $idx = -2;	
	  }
	} else {
	  $msg = 'No hay conexión con el servicio';
      $idx = -1;	
	}
  	//valida que el evento existe
  	//obten el id del evento
  	//registra el evento
  	$res =  '<?xml version="1.0" encoding="UTF-8"?>
               <space>
                 <Response> 
                   <Status>
                     <code>'.utf8_encode($idx).'</code>
                     <msg>'.utf8_encode($msg).'</msg>
                   </Status>
                   '.utf8_encode($dat).'
                 </Response>
               </space>';
    return new soapval('return', 'xsd:string', $res);
  } 

  /*recibe formulario*/

  function inserta_respuestas($id_form,$id_equipo,$id_usuario,$fecha_captura,$con){
    $res = -1;
    $sql="INSERT INTO PROD_FORM_RESULTADO (
    	      ID_RESULTADO,
            ID_FORMULARIO,
            ID_EQUIPO,
            ID_USUARIO_CONTESTO,
            FECHA_CAPTURA_EQUIPO,
            FECHA_CAPTURA_SERVIDOR
          ) VALUES (0,
            ".$id_form.",
            ".$id_equipo.",
            ".$id_usuario.",
            '".$fecha_captura."',
            CURRENT_TIMESTAMP)";
    if ($qry=mysqli_query($con,$sql)){
      $res= mysqli_insert_id($con);
    } else {
      $res = $sql; 	
    }
    return $res;
  }

  function inserta_historico_respuesta($id_resultado,$id_posicion,$con){
    $res = -1;
    $sql="INSERT INTO PROD_HIST_RESULTADO (
    	    ID_RESULTADO,
            ID_POSICION
          ) VALUES (0,
            ".$id_resultado.",
            ".$id_posicion.")";
    if ($qry=mysqli_query($con,$sql)){
      $res= 0;
    }
    return $res;
  }

  function inserta_valores_respuesta($id_res,$array,$con){
    $res = -1;
    foreach ($array as $respuesta){
      $array_resultados= explode("||", $respuesta);
      $id_elemento = trim($array_resultados[0]);
      $respuesta   = trim($array_resultados[1]);
      $sql="INSERT INTO PROD_FORM_DETALLE_RESULTADO (
    	        ID_RESULTADO,
              ID_ELEMENTO,
              CONTESTACION
          ) VALUES (
            ".$id_res.",
            ".$id_elemento.",
            '".$respuesta."')";
      $qry=mysqli_query($con,$sql);
      $res = $sql."-------".$res;
    }
    return $res;
  }

  function update_cita($con,$id_cita,$id_formulario,$id_resultado){
    $res = -1;
    $sql = "UPDATE PROD_CITA_FORMULARIO
            SET ID_RESULTADO = ".$id_resultado."
            WHERE ID_FORMULARIO = ".$id_formulario." AND
                  ID_CITA = ".$id_cita;
    $qry=mysqli_query($con,$sql);            
  }

  function cierra_cita($con,$id_cita){
    $res = -1;
    $sql = "UPDATE PROD_CITAS
            SET ID_ESTATUS = 4
            WHERE ID_CITA = ".$id_cita;
    $qry=mysqli_query($con,$sql);            
  }
  
  function cita_terminada($con,$id_cita){
    $res = false;
    $sql = "SELECT COUNT(*) AS PENDIENTES
            FROM PROD_CITA_FORMULARIO
            WHERE ID_RESULTADO IS NULL AND
                  ID_CITA = ".$id_cita;
    if (mysqli_query($con,$sql)){
      $row = mysqli_fetch_object($qry);
      if ($row->PENDIENTES == 0){
        $res = true;
      }
    }
    return $res;
  }

  function GetForm($usuario,
  	               $password,
  	               $id_cita,
  	               $id_formulario,
  	               $imei,
  	               $latitud,
  	               $longitud,
  	               $altitud,
  	               $angulo,
  	               $velocidad,
  	               $fecha_telefono,
  	               $fecha_gps,
  	               $bateria,
  	               $gps_error,
  	               $gps_proveedor,
  	               $respuestas,
  	               $fecha_inicio){
    $dat = '';
    $msg = 'No hay conexión con el servicio';
    $idx = -1;
    $con = mysqli_connect("192.168.6.23","dba","t3cnod8A!","SIMA");
    if ($con){
      $id_usuario = valida_usuario($usuario,$password,$con);
      $id_telefono = valida_imei($imei,$con);
      if ($id_usuario > 0){
        if ($id_telefono > 0){
	      $id_res = inserta_respuestas($id_formulario,$id_telefono,$id_usuario,$fecha_telefono,$con);
	      if ($id_res > 0){
	      	//REGISTRA LAS RESPUESTAS
            $array_valores = explode("|||", $respuestas);
            $res = inserta_valores_respuesta($id_res,$array_valores,$con);
            update_cita($con,$id_cita,$id_formulario,$id_res);
            //revisa si ya no hay formularios pendientes
            if (cita_terminada($con,$id_cita)){
              cierra_cita($con,$id_cita);
            }
	      	//REGISTRA EL EVENTO
            $id_hist = registra_evento('PROD_HISTORICO_POSICION',
  	                       $id_telefono,
		                   15, //ENVIO DE FORMULARIO
		                   $fecha_telefono,
		                   $bateria,
		                   0,
		                   $velocidad,
		                   $longitud,
		                   $latitud,
		                   $altitud,
		                   $angulo,
		                   $gps_error,
		                   $gps_proveedor,
		                   $fecha_gps, $con);
            //registra el resultado con el historico
            inserta_historico_respuesta($id_res,$id_hist,$con);
            $msg = '0';
            $idx = 0;
	      } else {
            $msg = 'No se registro la respuesta del formulario';
            $idx = -3;
	      }
	    } else {
          $msg = 'El IMEI es invalido';
          $idx = -3;
	    }
	  } else {
	    $msg = 'Usuario o password incorrecto';
        $idx = -2;	
	  }
      mysqli_close($con);
    } else {
      $msg = 'No hay conexión con el servicio';
      $idx = -1;	
    }
    $res =  '<?xml version="1.0" encoding="UTF-8"?>
               <space>
                 <Response> 
                   <Status>
                     <code>'.utf8_encode($idx).'</code>
                     <msg>'.utf8_encode($msg).'</msg>
                   </Status>
                   '.utf8_encode($dat).'
                 </Response>
               </space>';
    return new soapval('return', 'xsd:string', $res);
  }



  //if ( !isset( $HTTP_RAW_POST_DATA ) ) $HTTP_RAW_POST_DATA =file_get_contents( 'php://input' );
  $server->service($HTTP_RAW_POST_DATA);
?>
