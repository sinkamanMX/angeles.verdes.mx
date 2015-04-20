<?php
/**
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Mensajes extends My_Db_Table
{
	protected $_schema 	= 'PRODUCTIVIDAPP';
	protected $_name 	= 'PROD_MENSAJES';
	protected $_primary = 'ID_MENSAJE';
	
	public function getContactos($idObject,$idEmpresa,$filter = -1,$limitOption=false){
		$result= Array();
		$filtro = ($limitOption) ? 'LIMIT 1 ': '';
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT A.ID_USUARIO AS RECIBIO, I.ID_USUARIO AS ENVIO, M.CREADO, M.MENSAJE,
				  FLOOR(HOUR(TIMEDIFF(M.CREADO, CURRENT_TIMESTAMP)) / 24) AS HAGODAYS,
				  MOD(HOUR(TIMEDIFF(M.CREADO, CURRENT_TIMESTAMP)), 24)   AS HAGOHOURS,  
				  MINUTE(TIMEDIFF(M.CREADO, CURRENT_TIMESTAMP))  AS HAGOMINS,
				  CONCAT(A.NOMBRE,' ',A.APELLIDOS) AS N_RECIBE,
				  CONCAT(I.NOMBRE,' ',I.APELLIDOS) AS N_ENVIA,
				  M.PROCESADO,
				  M.LEIDO,
				  M.ID_MENSAJE,
				  A.CONECTADO AS CHAT_RECIBE,
				  I.CONECTADO AS CHAT_ENVIA
				FROM PROD_MENSAJES M
				INNER JOIN USUARIOS          A ON A.ID_USUARIO  = M.ID_USR_TO
				INNER JOIN USUARIOS          I ON I.ID_USUARIO  = M.ID_USR_SEND
				WHERE M.ID_USR_SEND  = $idObject
				   OR M.ID_USR_TO    = $idObject		
				/* GROUP BY M.ID_USR_SEND, M.ID_USR_TO */
				ORDER BY M.CREADO DESC $filtro";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
               
		return $result;        		
	}	
	
	public function processListContactos($aDataprocess,$idUsuario,$process=true){
		$aDataUnica = Array();
		
		if($process){
			foreach($aDataprocess as $items){
				$idContacto=0;
				if($items['RECIBIO']!=$idUsuario && $items['ENVIO']==$idUsuario){
					$idContacto =  $items['RECIBIO']; 					 							
				}else if($items['ENVIO']!=$idUsuario && $items['RECIBIO']==$idUsuario){
					$idContacto =  $items['ENVIO'];												
				}
				
				if(!isset($aDataUnica[$idContacto]) && $idContacto>0){
					if($items['RECIBIO']!=$idUsuario && $items['ENVIO']==$idUsuario){
						$aDataUnica[$idContacto]['ID'] = $idContacto;				
						$aDataUnica[$idContacto]['NOMBRE']      = $items['N_RECIBE'];
						$aDataUnica[$idContacto]['CONECTADO']   = $items['CHAT_RECIBE'];
						$aDataUnica[$idContacto]['OPTION']   	= 'OUT';										 							
					}else if($items['ENVIO']!=$idUsuario && $items['RECIBIO']==$idUsuario){
						$aDataUnica[$idContacto]['ID']          = $idContacto;
						$aDataUnica[$idContacto]['NOMBRE']      = $items['N_ENVIA'];
						$aDataUnica[$idContacto]['CONECTADO']   = $items['CHAT_ENVIA'];
						$aDataUnica[$idContacto]['OPTION']   	= 'IN';																			
					}
					
					$aDataUnica[$idContacto]['ID_MENSAJE'] 	= $items['ID_MENSAJE'];
					$aDataUnica[$idContacto]['MENSAJE'] 	= $items['MENSAJE'];
					$aDataUnica[$idContacto]['CREADO']  	= $items['CREADO'];
					$aDataUnica[$idContacto]['HAGODAYS']  	= $items['HAGODAYS'];
					$aDataUnica[$idContacto]['HAGOHOURS'] 	= $items['HAGOHOURS'];
					$aDataUnica[$idContacto]['HAGOMINS'] 	= $items['HAGOMINS'];			
					$aDataUnica[$idContacto]['PROCESADO'] 	= $items['PROCESADO'];
					$aDataUnica[$idContacto]['LEIDO'] 		= $items['LEIDO'];	
					
				}
			}			
		}else{
			foreach($aDataprocess as $items){
				$idContacto=0;
				if($items['RECIBIO']!=$idUsuario && $items['ENVIO']==$idUsuario){
					$idContacto =  $items['RECIBIO']; 					 							
				}else if($items['ENVIO']!=$idUsuario && $items['RECIBIO']==$idUsuario){
					$idContacto =  $items['ENVIO'];												
				}				
				
				if($items['RECIBIO']!=$idUsuario && $items['ENVIO']==$idUsuario){
					$aDataUnica['ID'] = $idContacto;				
					$aDataUnica['NOMBRE']      = $items['N_RECIBE'];
					$aDataUnica['CONECTADO']   = $items['CHAT_RECIBE'];									 							
				}else if($items['ENVIO']!=$idUsuario && $items['RECIBIO']==$idUsuario){
					$aDataUnica['ID']          = $idContacto;
					$aDataUnica['NOMBRE']      = $items['N_ENVIA'];
					$aDataUnica['CONECTADO']   = $items['CHAT_ENVIA'];																	
				}
				
				$aDataUnica['ID_MENSAJE'] 	= $items['ID_MENSAJE'];
				$aDataUnica['MENSAJE'] 		= $items['MENSAJE'];
				$aDataUnica['CREADO']  		= $items['CREADO'];
				$aDataUnica['HAGODAYS']  	= $items['HAGODAYS'];
				$aDataUnica['HAGOHOURS'] 	= $items['HAGOHOURS'];
				$aDataUnica['HAGOMINS'] 	= $items['HAGOMINS'];			
				$aDataUnica['PROCESADO'] 	= $items['PROCESADO'];
				$aDataUnica['LEIDO'] 		= $items['LEIDO'];		
			}			
		}

		return $aDataUnica;	
	}
	
	public function getConversacion($idUser,$idContacto,$iTime=1){
		$this->query("SET NAMES utf8",false); 
		$sql = "SELECT A.ID_USUARIO AS RECIBIO, I.ID_USUARIO AS ENVIO, M.CREADO, M.MENSAJE,
				  CONCAT(A.NOMBRE,' ',A.APELLIDOS) AS N_RECIBE,
				  T.DESCRIPCION AS TEL_RECIBE,
				  CONCAT(I.NOMBRE,' ',I.APELLIDOS) AS N_ENVIA,
				  U.DESCRIPCION AS TEL_ENVIA ,
				  M.PROCESADO,
				  M.LEIDO
				FROM  PROD_MENSAJES M
				INNER JOIN USUARIOS          A ON A.ID_USUARIO  = M.ID_USR_TO
				 LEFT JOIN PROD_USR_TELEFONO R ON R.ID_USUARIO  = A.ID_USUARIO
				 LEFT JOIN PROD_TELEFONOS    T ON R.ID_TELEFONO = T.ID_TELEFONO
				 
				INNER JOIN USUARIOS          I ON I.ID_USUARIO  = M.ID_USR_SEND
				 LEFT JOIN PROD_USR_TELEFONO S ON S.ID_USUARIO  = I.ID_USUARIO
				 LEFT JOIN PROD_TELEFONOS    U ON U.ID_TELEFONO = S.ID_TELEFONO
				WHERE DATE_ADD(NOW(), INTERVAL -$iTime DAY) < M.CREADO
				 AND  (M.ID_USR_TO 		= $idUser     AND  M.ID_USR_SEND = $idContacto) 
				 OR (M.ID_USR_TO = $idContacto AND  M.ID_USR_SEND = $idUser)
				ORDER BY M.ID_MENSAJE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;				
	}
	
	public function newMessage($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO PROD_MENSAJES
				SET ID_USR_SEND		=  ".$data['inputSend'].",
					ID_USR_TO		=  ".$data['inputTo'].",
					MENSAJE			=  '".$data['inputMsg']."',
					CREADO			= CURRENT_TIMESTAMP";
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
	
	public function getMessageById($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT A.ID_USUARIO AS RECIBIO, I.ID_USUARIO AS ENVIO, M.CREADO, M.MENSAJE,
				  FLOOR(HOUR(TIMEDIFF(M.CREADO, CURRENT_TIMESTAMP)) / 24) AS HAGODAYS,
				  MOD(HOUR(TIMEDIFF(M.CREADO, CURRENT_TIMESTAMP)), 24)   AS HAGOHOURS,  
				  MINUTE(TIMEDIFF(M.CREADO, CURRENT_TIMESTAMP))  AS HAGOMINS,
				  CONCAT(A.NOMBRE,' ',A.APELLIDOS) AS N_RECIBE,
				  CONCAT(I.NOMBRE,' ',I.APELLIDOS) AS N_ENVIA,
				  M.PROCESADO,
				  M.LEIDO,
				  M.ID_MENSAJE,
				  A.CONECTADO AS CHAT_RECIBE,
				  I.CONECTADO AS CHAT_ENVIA
				FROM PROD_MENSAJES M
				INNER JOIN USUARIOS          A ON A.ID_USUARIO  = M.ID_USR_TO
				INNER JOIN USUARIOS          I ON I.ID_USUARIO  = M.ID_USR_SEND
				WHERE M.ID_MENSAJE = $idObject
				ORDER BY M.CREADO DESC LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}		
}