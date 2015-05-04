<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Citas extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_CITAS';
	protected $_primary = 'ID_CITA';
	
	public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name
				SET ID_TPO				= ".$data['inputTipo'].",
					ID_EMPRESA  		= ".$data['ID_EMPRESA'].",
					ID_ESTATUS  		= 2,
					ID_CLIENTE          = ".$data['ID_CLIENTE'].",
					ID_USUARIO_CREO 	= ".$data['ID_USUARIO'].",
					FECHA_CITA			= '".$data['inputDate']."',
					HORA_CITA			= '".$data['inputHora']."',
					CONTACTO 			= '".$data['inputContacto']."',
					TELEFONO_CONTACTO   = '".$data['inputTelContacto']."', 		 					 
					FECHA_MODIFICACION 	= CURRENT_TIMESTAMP";
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']	   = $query_id[0]['ID_LAST'];
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	   		
	}
	
	public function insertDomCita($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO PROD_CITA_DOMICILIO
				SET ID_CITA		=  ".$data['idCita'].",
					CALLE		= '".$data['inputStreet']."',
					COLONIA		= '".$data['scolonia']."',
					NO_EXT		= '".$data['inputNoExt']."',
					NO_INT		= '".$data['inputNoInt']."',
					MUNICIPIO	= '".$data['sMunicipio']."',
					CP			= '".$data['inputCP']."',
					ESTADO		= '".$data['sEstado']."',
					REFERENCIAS	= '".$data['inputRefs']."',
					LATITUD		=  ".$data['sLatitud'].",
					LONGITUD	=  ".$data['sLongitud'];
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']	   = $query_id[0]['ID_LAST'];
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;			
	}
	
	public function insertExtraCitas($data){
        $result     = false;            
        $sql = "INSERT INTO PROD_CITA_EXTRAS 
				VALUES 
				(".$data['idCita'].",'".utf8_encode('Tarjeta Circulaci—n')."','".$data['inputTdc']."','V'),
				(".$data['idCita'].",'Licencia de Manejo','".$data['inputLicencia']."','V'),
				(".$data['idCita'].",'Vigencia de la Licencia','".$data['inputVigencia']."','V'),
				(".$data['idCita'].",'".utf8_encode('Lugar de emisi—n')."','".$data['inputEmision']."','V'),
				(".$data['idCita'].",'Marca','".$data['sMarca']."','V'),
				(".$data['idCita'].",'Modelo','".$data['sModelo']."','V'),
				(".$data['idCita'].",'".utf8_encode('A–o')."','".$data['inputAno']."','V'),
				(".$data['idCita'].",'Color','".$data['sColor']."','V'),
				(".$data['idCita'].",'Placas','".$data['inputPlacas']."','V'),
				(".$data['idCita'].",'No. de Serie','".$data['inputSerie']."','V'),
				(".$data['idCita'].",'No. de Motor','".$data['inputMotor']."','V')";  
        try{
    		$query   = $this->query($sql,false);
    		if($query){
    			$result= true;	
    		}
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;			
	}

	public function insertaFormCita($data){
        $result	= true;
        /*$sql="INSERT INTO PROD_CITA_FORMULARIO
				SET ID_CITA			=  ".$data['idCita'].",
					ID_FORMULARIO 	= 1";
        try{
    		$query   = $this->query($sql,false);
    		if($query){
    			$result= true;	
    		}
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	*/
        return $result;	
	}
	
	public function insertDomCitaOther($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO PROD_CITA_DOMICILIO
				SET ID_CITA		=  ".$data['idCita'].",
					CALLE		= '".$data['inputStreetO']."',
					COLONIA		= '".$data['scolonia']."',
					NO_EXT		= '".$data['inputNoExtO']."',
					NO_INT		= '".$data['inputNoIntO']."',
					MUNICIPIO	= '".$data['sMunicipio']."',
					CP			= '".$data['inputCPO']."',
					ESTADO		= '".$data['sEstado']."',
					REFERENCIAS	= '".$data['inputRefsO']."',
					LATITUD		=  ".$data['sLatitud'].",
					LONGITUD	=  ".$data['sLongitud'];
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']	   = $query_id[0]['ID_LAST'];
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;			
	}	
	
	public function getCitasPendientes($iType=1){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		 	
		if($iType==1){
			$sql	= "SELECT 'false' AS allday ,
					T.COLOR AS borderColor,
					T.COLOR AS color,			
					CONCAT(C.FECHA_CITA) AS start,
					CONCAT(C.FECHA_CITA) AS end ,	
					CONCAT(
						T.DESCRIPCION,': ',COUNT(T.ID_TPO) 
					)  AS title,
					GROUP_CONCAT(DISTINCT C.ID_CITA ORDER BY T.DESCRIPCION SEPARATOR ',') AS IDS
	    			FROM PROD_CITAS C
	    			INNER JOIN PROD_CLIENTES       L ON C.ID_CLIENTE  = L.ID_CLIENTE
	    			INNER JOIN PROD_CITA_DOMICILIO D ON C.ID_CITA     = D.ID_CITA
	    			INNER JOIN PROD_ESTATUS_CITA   E ON C.ID_ESTATUS  = E.ID_ESTATUS
	    			INNER JOIN PROD_CITA_USR       R ON C.ID_CITA     = R.ID_CITA
	    			INNER JOIN USUARIOS            U ON R.ID_USUARIO  = U.ID_USUARIO
	    			INNER JOIN PROD_TPO_CITA	   T ON C.ID_TPO	  = T.ID_TPO
	    			WHERE C.ID_ESTATUS IN (1,2,5)
	    			GROUP BY T.ID_TPO, C.FECHA_CITA
	    			ORDER BY C.FECHA_CITA ASC";
		}else{
			$sql = "SELECT 'false' AS allday ,
				'#438eb9' AS borderColor,
				'#438eb9' AS color,
				CONCAT(C.FECHA_CITA,'T',C.HORA_CITA,'+06:00') AS start,
				CONCAT(C.FECHA_CITA,'T',(DATE_ADD(C.HORA_CITA, INTERVAL 1 HOUR)),'+06:00') AS end ,						
				GROUP_CONCAT(CONCAT(T.DESCRIPCION, ':@' ) ORDER BY T.DESCRIPCION SEPARATOR '<br/> ')   AS title,
				C.FECHA_CITA,
				C.HORA_CITA,
				GROUP_CONCAT(DISTINCT C.ID_CITA ORDER BY T.DESCRIPCION SEPARATOR ',') AS IDS
    			FROM PROD_CITAS C
    			INNER JOIN PROD_CLIENTES       L ON C.ID_CLIENTE  = L.ID_CLIENTE
    			INNER JOIN PROD_CITA_DOMICILIO D ON C.ID_CITA     = D.ID_CITA
    			INNER JOIN PROD_ESTATUS_CITA   E ON C.ID_ESTATUS  = E.ID_ESTATUS
    			INNER JOIN PROD_CITA_USR       R ON C.ID_CITA     = R.ID_CITA
    			INNER JOIN USUARIOS            U ON R.ID_USUARIO  = U.ID_USUARIO
    			INNER JOIN PROD_TPO_CITA	   T ON C.ID_TPO	  = T.ID_TPO
    			WHERE C.ID_ESTATUS IN (1,2,5)
    			GROUP BY C.FECHA_CITA,C.HORA_CITA
    			ORDER BY C.FECHA_CITA ASC";
		}


		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getResume($date,$hours){
		$result= Array();
		$this->query("SET NAMES utf8",false);		
		$sql= "SELECT COUNT(C.ID_CITA) AS TOTAL, T.DESCRIPCION AS N_TITTLE, 
					GROUP_CONCAT(DISTINCT C.ID_CITA ORDER BY T.DESCRIPCION SEPARATOR ',') AS IDS
					FROM PROD_CITAS C
					LEFT JOIN PROD_TPO_CITA	   T ON C.ID_TPO	  = T.ID_TPO
					WHERE FECHA_CITA = '$date' 
					  AND HORA_CITA  = '$hours'    		    	
					  GROUP BY C.ID_TPO	";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	
	public function getCboTipoServicio($showUser=false){
		$result	= Array();
		$sFilter= ($showUser) ? ' WHERE MOSTRAR_CLIENTE = 1' : ''; 
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_TPO AS ID, DESCRIPCION AS NAME
				FROM PROD_TPO_CITA $sFilter ORDER BY ORDEN ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getCboStatus(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_ESTATUS AS ID, DESCRIPCION AS NAME
				FROM PROD_ESTATUS_CITA
				WHERE ACTIVO = 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getCitasDet($idCita){
		$filter = '';
		$result= Array();
		
		if($idCita>-1){
			$filter .= ($filter=='') ? ' WHERE ': ' AND ';
			$filter .= ' A.ID_CITA = '.$idCita;
		}		

    	$sql ="SELECT A.ID_CITA,
  	               A.FECHA_CITA,
  	               A.HORA_CITA,
  	               A.CONTACTO,
  	               A.TELEFONO_CONTACTO,
  	               A.FOLIO,
  	               IF(B.ID_CITA  IS NULL,'Sin Direccion',CONCAT(B.CALLE,' ',B.COLONIA,' ',B.NO_EXT,' ',B.NO_INT,' ',B.MUNICIPIO,' ',B.ESTADO,',CP:',B.CP))  AS DIRECCION_CITA,
  	               IF(B.ID_CITA  IS NULL,'Sin Direccion',CONCAT(B.ESTADO,',',B.MUNICIPIO,',',B.COLONIA,',',B.CALLE,',',B.NO_EXT,',',B.NO_INT,',','CP:',B.CP)) AS DIRECCION_MAPS,
  	               /*CONCAT(E.CALLE,' ',E.NUMERO_INT,' ',E.NUMERO_EXT,' ',E.COLONIA,' ',E.MUNICIPIO,' ',E.ESTADO,' ',E.CP) AS DIRECCION_CITA,*/
  	               B.REFERENCIAS AS REF_CITA,
  	               B.CP AS CP_CITA,
  	               B.LATITUD AS LAT_CITA,
  	               B.LONGITUD AS LON_CITA,
  	               D.COD_CLIENTE,
  	               CONCAT(D.NOMBRE,' ',D.APELLIDOS) AS NOMBRE_CLIENTE,
                   D.TELEFONO_FIJO,
                   D.TELEFONO_MOVIL,
                   D.EMAIL,
                   IF(E.ID_CLIENTE IS NULL,'Sin Direccion',CONCAT(E.CALLE,' ',E.NUMERO_INT,' ',E.NUMERO_EXT,' ',E.COLONIA,' ',E.MUNICIPIO,' ',E.ESTADO,' ',E.CP))  AS DIRECCION_CLIENTE,
                   E.REFERENCIAS,
                   E.LATITUD,
                   E.LONGITUD ,
                   S.DESCRIPCION AS ESTATUS ,
                   A.ID_ESTATUS,
                   IF(U.NOMBRE IS NULL ,'No asignado' ,CONCAT(U.NOMBRE,' ',U.APELLIDOS)) AS OPERADOR,
                   U.ID_USUARIO AS ID_OPERADOR,
                   A.OPERADOR_AUTORIZO,
                   A.FOLIO_AUTORIZACION,
                   D.RAZON_SOCIAL,
                   T.DESCRIPCION AS TIPO_CITA
  	        FROM PROD_CITAS A
  	           LEFT JOIN PROD_CITA_DOMICILIO     B ON B.ID_CITA    = A.ID_CITA
  	           LEFT JOIN PROD_CLIENTES           D ON D.ID_CLIENTE = A.ID_CLIENTE
  	           LEFT JOIN PROD_DOMICILIOS_CLIENTE E ON E.ID_CLIENTE = D.ID_CLIENTE
  	           INNER JOIN PROD_ESTATUS_CITA       S ON A.ID_ESTATUS = S.ID_ESTATUS	  	
  	           INNER JOIN PROD_TPO_CITA           T ON A.ID_TPO     = T.ID_TPO           
  	           LEFT JOIN PROD_CITA_USR            C ON C.ID_CITA    = A.ID_CITA
  	           LEFT JOIN USUARIOS            	  U ON C.ID_USUARIO = U.ID_USUARIO
  	           ".$filter;  
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}		
	
	public function getCitasSearch($dateIn,$dateFin,$keySearch,$Status,$idCita=-1){
		$filter = '';
		$result= Array();
		$this->query("SET NAMES utf8",false); 
		
		if($keySearch!=''){
			$filter .= ' WHERE A.CONTACTO LIKE "%'.$keySearch.'%" OR A.FOLIO LIKE"%'.$keySearch.'%" OR  D.NOMBRE LIKE "%'.$keySearch.'%"';	
		}
		
		if($Status!=''){
			$filter .= ($filter=='') ? ' WHERE ': ' AND ';
			$filter .= ' A.ID_ESTATUS IN ('.$Status.') ';
		}
		
		if($idCita>-1){
			$filter .= ($filter=='') ? ' WHERE ': ' AND ';
			$filter .= ' A.ID_CITA = '.$idCita;
		}		
		
		if($dateIn!='' && $dateFin!=""){
			$filter .= ($filter=='') ? ' WHERE ': ' OR ';
			$filter .= 'A.FECHA_CITA BETWEEN "'.$dateIn.'" AND "'.$dateFin.'"';
		}
    	$sql ="SELECT A.ID_CITA,
  	               A.FECHA_CITA,
  	               A.HORA_CITA,
  	               A.CONTACTO,
  	               A.TELEFONO_CONTACTO,
  	               A.FOLIO,
  	               IF(E.ID_CLIENTE IS NULL, CONCAT(E.CALLE,' ',E.NUMERO_INT,' ',E.NUMERO_EXT,' ',E.COLONIA,' ',E.MUNICIPIO,' ',E.ESTADO,' ',E.CP),'sin direccion') AS DIRECCION_CITA,
  	               B.REFERENCIAS AS REF_CITA,
  	               B.CP AS CP_CITA,
  	               B.LATITUD AS LAT_CITA,
  	               B.LONGITUD AS LON_CITA,
  	               D.COD_CLIENTE,
  	               CONCAT(D.NOMBRE,' ',D.APELLIDOS) AS NOMBRE_CLIENTE,
                   D.TELEFONO_FIJO,
                   D.TELEFONO_MOVIL,
                   D.EMAIL,
                   IF(E.ID_CLIENTE IS NULL, CONCAT(E.CALLE,' ',E.NUMERO_INT,' ',E.NUMERO_EXT,' ',E.COLONIA,' ',E.MUNICIPIO,' ',E.ESTADO,' ',E.CP),'sin direccion') AS DIRECCION_CLIENTE,
                   E.REFERENCIAS,
                   E.LATITUD,
                   E.LONGITUD ,
                   S.DESCRIPCION AS ESTATUS ,
                   A.ID_ESTATUS,
                   IF(U.NOMBRE IS NULL ,'No asignado' ,CONCAT(U.NOMBRE,' ',U.APELLIDOS)) AS OPERADOR,
                   U.ID_USUARIO AS ID_OPERADOR
  	        FROM PROD_CITAS A
  	           LEFT JOIN PROD_CITA_DOMICILIO     B ON B.ID_CITA    = A.ID_CITA
  	           INNER JOIN PROD_CLIENTES           D ON D.ID_CLIENTE = A.ID_CLIENTE
  	           LEFT JOIN PROD_DOMICILIOS_CLIENTE E ON E.ID_CLIENTE = D.ID_CLIENTE
  	           INNER JOIN PROD_ESTATUS_CITA       S ON A.ID_ESTATUS = S.ID_ESTATUS	  	           
  	           LEFT JOIN PROD_CITA_USR            C ON C.ID_CITA    = A.ID_CITA
  	           LEFT JOIN USUARIOS            	  U ON C.ID_USUARIO = U.ID_USUARIO
  	           ".$filter;    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	
	public function setRow($data){
        $result  = false;
        $options = '';
        $idInput		= $data['strInput'];
		$inputfechaCita = explode(" ", $data['inputFecha'] );
		$inputEstatus   = $data['inputEstatus']; 
		$changeDate     = $data['inputChangeDate'];
		
		if($changeDate==1){
			$date = $inputfechaCita[0]; 
			$time = $inputfechaCita[1];			
			$options = "FECHA_CITA			= '".$date."',
					   HORA_CITA			= '".$time.":00',";
		}
		       
        $sql="UPDATE $this->_name
				SET  ID_ESTATUS 			= ".$inputEstatus.",
					".$options."
					 ID_USUARIO_MODIFICO	= ".$data['ID_USUARIO']." , 
					 FECHA_MODIFICACION		= CURRENT_TIMESTAMP  
				WHERE ID_CITA =	$idInput";
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;			
	}
	
	public function changePersonal($data){
        $result = false;
        $idInput		= $data['strInput'];        
	
        if($data['ID_OPERADOR']!= NULL && $data['ID_OPERADOR']!=""){
        	$sql="UPDATE PROD_CITA_USR
				SET  ID_USUARIO =  ".$data['inputPersonal']."
					 WHERE ID_CITA = $idInput LIMIT 1";	
        }if($data['ID_OPERADOR'] == NULL){
        	$sql="INSERT INTO PROD_CITA_USR
				SET  ID_USUARIO = ".$data['inputPersonal'].",
				     ID_CITA    = $idInput";        	
        }else{
        	$sql="DELETE FROM PROD_CITA_USR
					 WHERE ID_CITA = $idInput LIMIT 1";        	
        }
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	   		
	}
	
	public function insertActPrev($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO AVL_ACTIVOS_PREVIO
				SET ID_CLIENTE		= ".$data['idCliente'].",
					DESCRIPCION		= '".$data['nCliente']."-".$data['inputPlacas']."',
					IDENTIFICADOR1	= '".$data['inputPlacas']."',
					SERIE1			= '".$data['inputSerie']."',
					MODELO			= ".$data['idCliente'].",
					TIPO_VEHICULO	= 1,
					COLOR			= ".$data['inputColor']; 
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']	   = $query_id[0]['ID_LAST'];
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;			
	}	

	public function assignUser($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO PROD_CITA_USR
				SET ID_CITA		= ".$data['idCita'].",
					ID_USUARIO	= ".$data['uAssign']; 
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']	   = $query_id[0]['ID_LAST'];
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;			
	}

	public function getResumeByDay($idSucursal,$dFechaIn,$dFechaFin,$idTecnico,$typeSearch=1){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$sFilter 	 = ($idTecnico!="") ? ' C.ID_USUARIO = '.$idTecnico: ' E.ID_SUCURSAL IN ('.$idSucursal.')';
		$sFilterDate = ($typeSearch==1)  ? "AND C.FECHA_CITA BETWEEN '$dFechaIn' AND '$dFechaFin'" : "AND CAST(C.FECHA_INICIO  AS DATE) BETWEEN'$dFechaIn' AND '$dFechaFin'";
				 		
    	$sql ="SELECT C.ID_CITA AS ID, C.ID_ESTATUS AS IDE, S.DESCRIPCION, S.COLOR,				
				P.RAZON_SOCIAL AS NOMBRE_CLIENTE,C.FOLIO,
				C.FECHA_CITA AS F_PROGRAMADA,
				C.HORA_CITA  AS H_PROGRAMADA,
				IF(C.FECHA_INICIO  IS NULL ,'--',C.FECHA_INICIO) AS FECHA_INICIO,
				IF(C.FECHA_TERMINO IS NULL ,'--',C.FECHA_TERMINO) AS FECHA_TERMINO,
				IF(U.ID_USUARIO    IS NULL ,'Sin Asignar', CONCAT(U.NOMBRE,' ',U.APELLIDOS)) AS NOMBRE_TECNICO,
				IF(C.FECHA_CITA<'2015-01-19 00:00:00','A','N') AS NEW_FORM,
				T.DESCRIPCION AS N_TIPO,
				IF(D.ID_CITA  IS NULL,'Sin Direccion',CONCAT(D.CALLE,' ',D.COLONIA,' ',D.NO_EXT,' ',D.NO_INT,' ',D.MUNICIPIO,' ',D.ESTADO,',CP:',D.CP)) AS DIRECCION,
				IF(U.ID_USUARIO IS NULL,'0','1') AS TEC_ASIGNADO,
				A.ID_USUARIO AS ID_USER,
				IF(D.MUNICIPIO  IS NULL,'Sin Direccion',D.MUNICIPIO) AS DIR_MUN,
				IF(D.CP  IS NULL,'Sin Direccion',D.CP) AS DIR_CP,
				IF(D.ESTADO  IS NULL,'Sin Direccion',D.ESTADO) AS DIR_ESTADO
				FROM PROD_CITAS C
				LEFT JOIN PROD_CITA_DOMICILIO D ON C.ID_CITA 	 = D.ID_CITA
				INNER JOIN PROD_ESTATUS_CITA   S ON C.ID_ESTATUS = S.ID_ESTATUS
				INNER JOIN PROD_CLIENTES       P ON C.ID_CLIENTE = P.ID_CLIENTE
				 LEFT JOIN PROD_CITA_USR       A ON C.ID_CITA	 = A.ID_CITA
				 LEFT JOIN USUARIOS			   U ON A.ID_USUARIO = U.ID_USUARIO 
				INNER JOIN PROD_TPO_CITA       T ON C.ID_TPO     = T.ID_TPO
				WHERE C.ID_CITA IN (
					SELECT C.ID_CITA
					FROM PROD_CITA_USR C 
					INNER JOIN USR_EMPRESA E ON C.ID_USUARIO = E.ID_USUARIO 
					WHERE $sFilter
					)
				$sFilterDate
				ORDER BY S.ID_ESTATUS";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;	
	}
	
	
	public function getDataRep($idOject){
		$filter = '';
		$result= Array();
		$this->query("SET NAMES utf8",false);		
    	$sql ="SELECT   C.ID_CITA AS ID, 
						C.FECHA_CITA,
						C.HORA_CITA,
						C.FOLIO,
						C.CONTACTO,
						C.TELEFONO_CONTACTO,
						CONCAT(R.NOMBRE,' ',R.APELLIDOS) AS USR_REGISTRADO,
						P.RAZON_SOCIAL AS NOMBRE_CLIENTE,		
						IF(M.ID_CLIENTE IS NULL, 'sin direccion',CONCAT(M.CALLE,' ',M.NUMERO_EXT,' ',M.NUMERO_INT,' ',M.COLONIA)) AS DIRECCION_CLIENTE1,
						IF(M.ID_CLIENTE IS NULL, 'sin direccion',CONCAT(M.MUNICIPIO,' ',M.ESTADO,' ',M.CP)) AS DIRECCION_CLIENTE2,						 
						IF(D.ID_CITA IS NULL,'Sin direccion',CONCAT(D.CALLE,' ',D.NO_EXT,' ',D.NO_INT,' ',D.COLONIA)) AS DIRECCION_CITA1,						
						IF(D.ID_CITA IS NULL,'Sin direccion',CONCAT(' ',D.MUNICIPIO,' ',D.ESTADO,' ',D.CP)) AS DIRECCION_CITA2,	
						IF(U.ID_USUARIO    IS NULL ,'Sin Asignar', CONCAT(U.NOMBRE,' ',U.APELLIDOS)) AS NOMBRE_TECNICO,
						IF(C.FECHA_INICIO  IS NULL ,'--',C.FECHA_INICIO) AS FECHA_INICIO,
						IF(C.FECHA_TERMINO IS NULL ,'--',C.FECHA_TERMINO) AS FECHA_TERMINO,
						L.DESCRIPCION AS SUCURSAL,
						T.DESCRIPCION AS TIPO_CITA,
						IF(D.MUNICIPIO  IS NULL,'Sin Direccion',D.MUNICIPIO) AS DIR_MUN,
						IF(D.CP  IS NULL,'Sin Direccion',D.CP) AS DIR_CP,
						IF(D.ESTADO  IS NULL,'Sin Direccion',D.ESTADO) AS DIR_ESTADO
				FROM PROD_CITAS C
					INNER JOIN PROD_TPO_CITA       T ON C.ID_TPO = T.ID_TPO
					INNER JOIN USUARIOS			   R ON C.ID_USUARIO_CREO = R.ID_USUARIO
					 LEFT JOIN PROD_CITA_DOMICILIO D ON C.ID_CITA 	 = D.ID_CITA
					INNER JOIN PROD_ESTATUS_CITA   S ON C.ID_ESTATUS = S.ID_ESTATUS
					LEFT JOIN PROD_CLIENTES       P ON C.ID_CLIENTE = P.ID_CLIENTE
					LEFT JOIN PROD_DOMICILIOS_CLIENTE M ON P.ID_CLIENTE = M.ID_CLIENTE
					INNER JOIN PROD_CITA_USR       A ON C.ID_CITA	 = A.ID_CITA
					INNER JOIN USUARIOS			   U ON A.ID_USUARIO = U.ID_USUARIO 
					INNER JOIN USR_EMPRESA         E ON E.ID_USUARIO  = U.ID_USUARIO
					INNER JOIN SUCURSALES          L ON L.ID_SUCURSAL = E.ID_SUCURSAL
				WHERE C.ID_CITA =".$idOject;  
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}	
	
	public function getFormsCita($idOject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT B.ID_FORMULARIO,			
	               B.TITULO,		
			       B.FOTOS_EXTRAS,
			       B.QRS_EXTRAS,
			       B.FIRMAS_EXTRAS,
			       B.LOCALIZACION
				FROM PROD_CITA_FORMULARIO    A
				  INNER JOIN PROD_FORMULARIO B ON A.ID_FORMULARIO = B.ID_FORMULARIO
				WHERE A.ID_CITA = ".$idOject;
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;
		}	
        
		return $result;			
	}
	
	public function getDataSendbyForms($idOject,$idForm){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT E.ID_ELEMENTO,
    					E.ID_TIPO,		
				       IF (E.ID_TIPO = 8, 'ENCABEZADO','RESPUESTA') AS TIPO,			
				       E.DESCIPCION AS DESCRIPCION,			
				       B.CONTESTACION,			
				       A.FECHA_CAPTURA_EQUIPO,
				       L.ID_TIPO AS T_ELEMENTO		
				FROM PROD_FORM_RESULTADO A			
				  INNER JOIN PROD_FORM_DETALLE_RESULTADO B ON A.ID_RESULTADO = B.ID_RESULTADO			
				  INNER JOIN PROD_FORMULARIO_ELEMENTOS C ON C.ID_ELEMENTO = B.ID_ELEMENTO			
				  INNER JOIN PROD_CITA_FORMULARIO D ON D.ID_RESULTADO = A.ID_RESULTADO			
				  INNER JOIN PROD_ELEMENTOS E ON E.ID_ELEMENTO = C.ID_ELEMENTO
				  INNER JOIN PROD_TPO_ELEMENTO L ON E.ID_TIPO = L.ID_TIPO		
				WHERE A.ID_FORMULARIO = $idForm AND			
				      D.ID_CITA 	  = $idOject			
				ORDER BY C.ORDEN ASC";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;
		}	
        
		return $result;			
	}
	
	public function getPendientesbyEmpresa($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT A.ID_CITA,
					S.DESCRIPCION AS ESTATUS ,
					S.ID_ESTATUS,
 					CONCAT(D.NOMBRE,' ',D.APELLIDOS) AS NOMBRE_CLIENTE,                   
  	               	A.FECHA_CITA,
  	               	A.HORA_CITA,                   
                   	A.ID_ESTATUS,
                   	IF(U.NOMBRE IS NULL ,'No asignado' ,CONCAT(U.NOMBRE,' ',U.APELLIDOS)) AS OPERADOR,
                   	U.ID_USUARIO AS ID_OPERADOR,
                   	A.FOLIO_AUTORIZACION,
                   	A.OPERADOR_AUTORIZO,
                   	A.FOLIO                  
	  	        FROM PROD_CITAS A
	  	           LEFT JOIN PROD_CITA_DOMICILIO     B ON B.ID_CITA    = A.ID_CITA
	  	           INNER JOIN PROD_CLIENTES           D ON D.ID_CLIENTE = A.ID_CLIENTE
	  	           LEFT JOIN PROD_DOMICILIOS_CLIENTE E ON E.ID_CLIENTE = D.ID_CLIENTE
	  	           INNER JOIN PROD_ESTATUS_CITA       S ON A.ID_ESTATUS = S.ID_ESTATUS	  	           
	  	           LEFT JOIN PROD_CITA_USR            C ON C.ID_CITA    = A.ID_CITA
	  	           LEFT JOIN USUARIOS            	  U ON C.ID_USUARIO = U.ID_USUARIO
	  	       WHERE A.ID_ESTATUS NOT IN (1,4,6)
	  	         AND FOLIO_AUTORIZACION IS NULL
	  	         AND A.ID_EMPRESA = ".$idEmpresa."
	  	         ORDER BY A.FECHA_CITA DESC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getValidFolioAut($codeValidation,$idEmpresa){
		$result= false;
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT FOLIO_AUTORIZACION            
	  	        FROM PROD_CITAS  
	  	       WHERE FOLIO_AUTORIZACION = '$codeValidation'
	  	         AND ID_EMPRESA = ".$idEmpresa;
		$query   = $this->query($sql);
		if(count($query)>0){
			if($query[0]['FOLIO_AUTORIZACION']!=""){
				$result = true;
			}		  		
		}	
        
		return $result;			
	}	
	
	public function validateDate($data){
        $result = false;
        $idInput		= $data['strInput'];        
	
		$sql="UPDATE PROD_CITAS
				SET  FOLIO_AUTORIZACION =  '".$data['codeValidate']."',
				     OPERADOR_AUTORIZO 	=   ".$data['ID_USUARIO']."
					 WHERE ID_CITA = $idInput LIMIT 1";        
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	   		
	}	
		
	public function getResumeContact($idCliente,$dFechaIn,$dFechaFin){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT C.ID_CITA AS ID, C.ID_ESTATUS AS IDE, S.DESCRIPCION, S.COLOR,				
				P.RAZON_SOCIAL AS NOMBRE_CLIENTE,C.FOLIO,
				C.FECHA_CITA AS F_PROGRAMADA,
				C.HORA_CITA  AS H_PROGRAMADA,
				IF(C.FECHA_INICIO  IS NULL ,'--',C.FECHA_INICIO) AS FECHA_INICIO,
				IF(C.FECHA_TERMINO IS NULL ,'--',C.FECHA_TERMINO) AS FECHA_TERMINO,
				IF(U.ID_USUARIO    IS NULL ,'Sin Asignar', CONCAT(U.NOMBRE,' ',U.APELLIDOS)) AS NOMBRE_TECNICO,
				IF(C.FECHA_CITA<'2015-01-19 00:00:00','A','N') AS NEW_FORM,
				CONCAT(D.CALLE,' ',D.COLONIA,' ',D.NO_EXT,' ',D.NO_INT,' ',D.MUNICIPIO,' ',D.ESTADO,',CP:',D.CP) AS DIRECCION
				FROM PROD_CITAS C
				INNER JOIN PROD_CITA_DOMICILIO D ON C.ID_CITA 	 = D.ID_CITA
				INNER JOIN PROD_ESTATUS_CITA   S ON C.ID_ESTATUS = S.ID_ESTATUS
				INNER JOIN PROD_CLIENTES       P ON C.ID_CLIENTE = P.ID_CLIENTE
				 LEFT JOIN PROD_CITA_USR       A ON C.ID_CITA	 = A.ID_CITA
				 LEFT JOIN USUARIOS			   U ON A.ID_USUARIO = U.ID_USUARIO 
				WHERE C.ID_CLIENTE = $idCliente
				AND C.FECHA_CITA BETWEEN '$dFechaIn' AND '$dFechaFin'
				ORDER BY S.ID_ESTATUS";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;	
	}	
	
	
	public function getDateByList($sIdDates){
		$filter = '';
		$result= Array();
		$this->query("SET NAMES utf8",false); 
		
    	$sql ="SELECT C.ID_CITA AS ID, C.ID_ESTATUS AS IDE, S.DESCRIPCION, S.COLOR,				
				P.RAZON_SOCIAL AS NOMBRE_CLIENTE,C.FOLIO,
				C.FECHA_CITA AS F_PROGRAMADA,
				C.HORA_CITA  AS H_PROGRAMADA,
				IF(C.FECHA_INICIO  IS NULL ,'--',C.FECHA_INICIO) AS FECHA_INICIO,
				IF(C.FECHA_TERMINO IS NULL ,'--',C.FECHA_TERMINO) AS FECHA_TERMINO,
				IF(U.ID_USUARIO    IS NULL ,'Sin Asignar', CONCAT(U.NOMBRE,' ',U.APELLIDOS)) AS NOMBRE_TECNICO,
				IF(C.FECHA_CITA<'2015-01-19 00:00:00','A','N') AS NEW_FORM,
				T.DESCRIPCION AS N_TIPO,
				CONCAT(D.CALLE,' ',D.COLONIA,' ',D.NO_EXT,' ',D.NO_INT,' ',D.MUNICIPIO,' ',D.ESTADO,',CP:',D.CP) AS DIRECCION,
				IF(U.ID_USUARIO IS NULL,'0','1') AS TEC_ASIGNADO,
				A.ID_USUARIO AS ID_USER
				FROM PROD_CITAS C
				INNER JOIN PROD_CITA_DOMICILIO D ON C.ID_CITA 	 = D.ID_CITA
				INNER JOIN PROD_ESTATUS_CITA   S ON C.ID_ESTATUS = S.ID_ESTATUS
				INNER JOIN PROD_CLIENTES       P ON C.ID_CLIENTE = P.ID_CLIENTE
				 LEFT JOIN PROD_CITA_USR       A ON C.ID_CITA	 = A.ID_CITA
				 LEFT JOIN USUARIOS			   U ON A.ID_USUARIO = U.ID_USUARIO 
				INNER JOIN PROD_TPO_CITA       T ON C.ID_TPO     = T.ID_TPO
  	           WHERE A.ID_CITA IN ($sIdDates)    
				ORDER BY S.ID_ESTATUS";  
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}		
	
}