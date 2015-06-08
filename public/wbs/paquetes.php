<?php
   /* Script para copiar paquetes traducidos a la BD de SIMA*/
   
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

   function dame_equipo($con,$imei,$ip){
     $res = -1;
     $sql = "SELECT ID_EQUIPO
             FROM AVL_EQUIPOS
             WHERE IMEI = '".$imei."' ";
     if ($qry = mysqli_query($con,$sql)){
      $row = mysqli_fetch_object($qry);
      if (strlen($row->ID_EQUIPO) > 0){
        $res = $row->ID_EQUIPO;
      } 
      mysqli_free_result($qry); 	
     }
     return $res;	
   }

   function dame_activo($con,$id_equipo){
     $res = -1;
     $sql = "SELECT ID_ACTIVO
             FROM AVL_EQUIPO_ACTIVO
             WHERE ID_EQUIPO = ".$id_equipo;
     if ($qry = mysqli_query($con,$sql)){
      $row = mysqli_fetch_object($qry);
      if (strlen($row->ID_ACTIVO) > 0){
        $res = $row->ID_ACTIVO;
      } 
      mysqli_free_result($qry); 	
     }
     //echo $sql;
     return $res;	
   }

   function busca_evento($con,$evento,$id_equipo){
     $res = -1;
     $sql = "SELECT B.ID_EVENTO
             FROM AVL_EVENTOS_HW A
                  INNER JOIN AVL_EVENTOS_EQUIPO B ON B.ID_EVENTO_HW = A.ID_EVENTO_HW
             WHERE A.CODIGO = '".$evento."' AND
                   B.ID_EQUIPO = ".$id_equipo;
     if ($qry = mysqli_query($con,$sql)){
      $row = mysqli_fetch_object($qry);
      if (strlen($row->ID_EVENTO) > 0){
        $res = $row->ID_EVENTO;
      } 
      mysqli_free_result($qry); 	
     }
     return $res;	
   }

   function escribe_paquete($evento,
   	                        $imei,
   	                        $ip,
   	                        $latitud,
   	                        $longitud,
   	                        $fecha_gps,
   	                        $angulo,
   	                        $velocidad,
   	                        $motor,
   	                        $bateria,
   	                        $fecha_server,
   	                        $ubicacion){
     $con = mysqli_connect("192.168.6.23","dba","t3cnod8A!","SIMA");
     if ($con){
       $id_equipo = dame_equipo($con,$imei,$ip);
       if ($id_equipo > 0){
         $id_evento = busca_evento($con,$evento,$id_equipo);
         if ($id_evento > 0){
   	       $id_activo = dame_activo($con,$id_equipo);                    
	       $sql = "INSERT INTO AVL_HISTORICO(
	        	     ID_EVENTO,
	     	         ID_ACTIVO,
	     	         ID_EQUIPO,
		     	     LATITUD,
		     	     LONGITUD,
		     	     FECHA_GPS,
		     	     ANGULO,
		     	     VELOCIDAD,
		     	     MOTOR,
		     	     BATERIA,
		     	     FECHA_SERVER,
		     	     UBICACION
		     	   ) VALUES (
		     	     ".$id_evento.",
		     	     ".$id_activo.",
		     	     ".$id_equipo.",
		     	     ".$latitud.",
		     	     ".$longitud.",
		     	     '".$fecha_gps."',
		     	     ".$angulo.",
		     	     ".$velocidad.",
		     	     '".$motor."',
		     	     ".$bateria.",
		     	     '".$fecha_server."',
		     	     '".$ubicacion."'
		     	   )";
           if ($qry = mysqli_query($con,$sql)){
             $res = "OK";
           } else {
             $res = "No se logro ejecutar el qry: ".$sql;
           }
         } else {
           $res = "El evento ".$evento." del IMEI ".$imei." con IP ".$ip." no fue encontrado";  
         }
	   } else {
	     $res = "El IMEI ".$imei." no esta registrado";
	   } 
	   mysqli_close($con);
	 } else {
	   $res = "No se logro establecer conexión con la Base de Datos";	
	 }
	 return $res;
   }

   

   

   function marca_paquete($con1,$instancia){
     $sql = "UPDATE DATA_TRADUCTOR
             SET STATUS = ".$instancia."
             WHERE STATUS = 0
             ORDER BY FECHA DESC
             LIMIT 100";
     $qry = mysqli_query($con1,$sql); 
   }

   function borra_paquete($con1,$id){
     $sql = "DELETE FROM DATA_TRADUCTOR
             WHERE ID = ".$id;
     $qry = mysqli_query($con1,$sql); 
   }
   
   function lee_paquetes(){
     $res = -1;
     $con1 = mysqli_connect("192.168.6.23","dba","t3cnod8A!","COMUNICADOR");
     if ($con1){
       $instancia = rand(10,100);
       marca_paquete($con1,$instancia);	
       $sql = "SELECT ID,
                      EVENTO,
                      MASK_IO,
                      DIG_INPUT,
                      DIG_OUTPUT,
                      EANA1,
                      FECHA,
                      STATUS_GPS,
                      LATITUD,
                      LONGITUD,
                      VELOCIDAD,
                      ANGULO,
                      ALTITUD,
                      NUM_SAT,
                      FECHA_SAVE,
                      MODEM_ID,
                      IP,
                      BATERIA
                FROM DATA_TRADUCTOR
                WHERE STATUS = ".$instancia;
       if ($qry = mysqli_query($con1,$sql)){
       	 if (mysqli_num_rows($qry) > 0)
         while ($row = mysqli_fetch_object($qry)){   
           $ubicacion = dame_google ($row->LATITUD,$row->LONGITUD);
           $motor = 0;
           $bateria = 0;
           $res = escribe_paquete($row->EVENTO,
   	                        $row->MODEM_ID,
   	                        $row->IP,
   	                        $row->LATITUD,
   	                        $row->LONGITUD,
   	                        $row->FECHA,
   	                        $row->ANGULO,
   	                        $row->VELOCIDAD,
   	                        $motor,
   	                        $bateria,
   	                        $row->FECHA_SAVE,
   	                        $ubicacion);	
           echo $res;
           if ($res == 'OK'){
             borra_paquete($con1,$row->ID);	
           } else {
             $msg = $msg.$res;
           }
         }
       }
       mysqli_close($con1);
     } 
     return $res;	
   }

   lee_paquetes();
?>
