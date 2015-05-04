<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Solicitudes extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_CITAS_SOLICITUD';
	protected $_primary = 'ID_SOLICITUD';
	
    public function getDataTablebyClient($idCliente,$iStatus=0){
      	$this->query("SET NAMES utf8",false);         
		$result= Array();
		$sFilter = ($iStatus==0) ?  ' = 1' : ' IN ('.$iStatus.') ';
    	$sql ="SELECT S.*, T.DESCRIPCION AS N_TIPO, C.NOMBRE AS N_CLIENTE, E.DESCRIPCION AS N_ESTATUS, CONCAT(H.HORA_INICIO,'-',H.HORA_FIN) AS N_HORARIO,
				CONCAT(R.HORA_INICIO,'-',R.HORA_FIN) AS N_HORARIO2 , U.IDENTIFICADOR AS N_UNIDAD
				FROM PROD_CITAS_SOLICITUD S
				INNER JOIN PROD_TPO_CITA  T ON S.ID_TIPO = T.ID_TPO
				INNER JOIN PROD_CLIENTES  C ON S.ID_CLIENTE = C.ID_CLIENTE
				INNER JOIN PROD_ESTATUS_SOLICITUD E ON S.ID_ESTATUS = E.ID_ESTATUS
				INNER JOIN PROD_HORARIOS_CITA H ON S.ID_HORARIO     = H.ID_HORARIO_CITA
				INNER JOIN PROD_UNIDADES      U ON S.ID_UNIDAD		= U.ID_UNIDAD
				LEFT JOIN PROD_HORARIOS_CITA  R ON S.ID_HORARIO2    = R.ID_HORARIO_CITA	
				WHERE S.ID_CLIENTE = $idCliente AND S.ID_ESTATUS $sFilter ";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query;			
		}	
        
		return $result;	        
    }
    
    public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT S.*, T.DESCRIPCION AS N_TIPO, C.RAZON_SOCIAL AS N_CLIENTE, E.DESCRIPCION AS N_ESTATUS,
				CONCAT(Q.NOMBRE,' ',Q.APELLIDOS) AS N_CONTACTO , Q.EMAIL, CONCAT(H.HORA_INICIO,'-',H.HORA_FIN) AS N_HORARIO,
				CONCAT(R.HORA_INICIO,'-',R.HORA_FIN) AS N_HORARIO2 , U.IDENTIFICADOR AS N_UNIDAD
				FROM PROD_CITAS_SOLICITUD S
				INNER JOIN PROD_TPO_CITA  T ON S.ID_TIPO = T.ID_TPO
				INNER JOIN PROD_CLIENTES  C ON S.ID_CLIENTE = C.ID_CLIENTE
				INNER JOIN PROD_ESTATUS_SOLICITUD E ON S.ID_ESTATUS = E.ID_ESTATUS
				INNER JOIN PROD_QR_CONTACTOS  Q ON S.ID_CONTACTO_QR = Q.ID_CONTACTO_QR
				INNER JOIN PROD_HORARIOS_CITA H ON S.ID_HORARIO     = H.ID_HORARIO_CITA
				INNER JOIN PROD_UNIDADES      U ON S.ID_UNIDAD		= U.ID_UNIDAD
				LEFT JOIN PROD_HORARIOS_CITA  R ON S.ID_HORARIO2    = R.ID_HORARIO_CITA		
                WHERE S.$this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }

    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sFilter = (isset($data['inputHorario2']) && $data['inputHorario2']!="") ? 'ID_HORARIO2 	=  '.$data['inputHorario2'].',' : '';
        
        $sql=" INSERT INTO $this->_name SET 
        		ID_CLIENTE		= ".$data['inputCliente'].",
				ID_TIPO			= ".$data['inputTipo'].",
				ID_ESTATUS		= 1,
				ID_CONTACTO_QR  =  ".$data['inputUserQr']." ,
				ID_UNIDAD		=  ".$data['inputUnidad']." ,
				ID_HORARIO		=  ".$data['inputHorario']." ,
				$sFilter
				INFORMACION_UNIDAD= '".$data['inputInfo']."',					
				COMENTARIO		= '".$data['inputComment']."',		
				FECHA_CITA		= '".$data['inputFechaIn']."',
				FECHA_CREADO 	=  CURRENT_TIMESTAMP";
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

    public function updateRow($data){
       $result     = Array();
        $result['status']  = false;
        $sFilter = '';
		$sFilter = (isset($data['inputHorario2']) && $data['inputHorario2']!="") ? 'ID_HORARIO2 	=  '.$data['inputHorario2'].',' : '';
		        
        if(isset($data['bOperation']) && $data['bOperation']=='accept'){
        	$data['inputEstatus'] = 2;
        	$sFilter = "ID_ESTATUS		=  ".$data['inputEstatus']." ,";	
        }else if(isset($data['bOperation']) && $data['bOperation']=='modify'){
        	$data['inputEstatus'] = 4;	
        	$sFilter .= "ID_ESTATUS		=  ".$data['inputEstatus']." ,
        				 ID_HORARIO		=  ".$data['inputHorario']." ,
        				 FECHA_CITA		= '".$data['inputFechaIn']."',";
        }else{
        	$sFilter .= "ID_UNIDAD		=  ".$data['inputUnidad']." ,
						 ID_HORARIO		=  ".$data['inputHorario']." ,
						 INFORMACION_UNIDAD= '".$data['inputInfo']."',								
						 COMENTARIO		= '".$data['inputComment']."',
						 FECHA_CITA		= '".$data['inputFechaIn']."'";
        }
        
        $sql="UPDATE $this->_name SET
        		$sFilter
        		WHERE $this->_primary =".$data['catId']." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;
    }    
    
    
    public function getDataTable($sOptions=0){
      	$this->query("SET NAMES utf8",false);         
		$result= Array();
		$sFilter = ($sOptions==0) ?  ' = 1' : ' IN ('.$sOptions.') ';
    	$sql ="SELECT S.*, T.DESCRIPCION AS N_TIPO, C.RAZON_SOCIAL AS N_CLIENTE, E.DESCRIPCION AS N_ESTATUS, CONCAT(H.HORA_INICIO,'-',H.HORA_FIN) AS N_HORARIO,
				CONCAT(R.HORA_INICIO,'-',R.HORA_FIN) AS N_HORARIO2 , U.IDENTIFICADOR AS N_UNIDAD
				FROM PROD_CITAS_SOLICITUD S
				INNER JOIN PROD_TPO_CITA  T ON S.ID_TIPO = T.ID_TPO
				INNER JOIN PROD_CLIENTES  C ON S.ID_CLIENTE = C.ID_CLIENTE
				INNER JOIN PROD_ESTATUS_SOLICITUD E ON S.ID_ESTATUS = E.ID_ESTATUS
				INNER JOIN PROD_HORARIOS_CITA H ON S.ID_HORARIO     = H.ID_HORARIO_CITA
				INNER JOIN PROD_UNIDADES      U ON S.ID_UNIDAD		= U.ID_UNIDAD
				LEFT JOIN PROD_HORARIOS_CITA  R ON S.ID_HORARIO2    = R.ID_HORARIO_CITA						
				WHERE S.ID_ESTATUS $sFilter ";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query;			
		}	
        
		return $result;	      	
    }
    
    public function updateAtencion($data){
       	$result     = Array();
        $result['status']  = false;
        $sFilter = '';
        if(isset($data['bOperation']) && $data['bOperation']=='accept'){
        	$data['inputEstatus'] = 2;
        	$sFilter = '';	
        }else{
        	$sFilter .= (isset($data['inputHorario2']) && $data['inputHorario2']!="") ? 'ID_HORARIO2 	=  '.$data['inputHorario2'].',' : '';
        	$data['inputEstatus'] = 5;	
        	$sFilter .= "ID_TIPO		= ".$data['inputTipo'].",        				
						ID_HORARIO		=  ".$data['inputHorario']." ,
        				FECHA_CITA		= '".$data['inputFechaIn']."',";
        }
        
        $sql="UPDATE $this->_name SET
        		ID_ESTATUS		=  ".$data['inputEstatus']." ,
        		$sFilter
        		REVISION		= '".$data['inputRevision']."'
        		WHERE $this->_primary =".$data['catId']." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;
    }     
}