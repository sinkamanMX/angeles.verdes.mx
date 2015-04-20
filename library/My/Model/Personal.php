<?php
/**
 * Archivo de definición de usuarios
 * 
 * @author EPENA
 * @package library.My.Models
 */

/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Personal extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'USUARIOS';
	protected $_primary = 'ID_USUARIO';
	
	public function getAll($idObject,$selectId=0){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$sIdSearch = ($selectId==0) ? 'T.ID_TELEFONO': 'U.ID_USUARIO'; 		
    	$sql ="SELECT $sIdSearch AS ID, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NAME, U.ID_SUCURSAL
				FROM USUARIOS  U   
				INNER JOIN SUCURSALES S 	   ON U.ID_SUCURSAL= S.ID_SUCURSAL
				INNER JOIN PROD_USR_TELEFONO T ON U.ID_USUARIO = T.ID_USUARIO
				WHERE S.ID_EMPRESA = $idObject
				  AND U.FLAG_OPERACIONES = 1
				ORDER BY U.ID_SUCURSAL ASC, NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        		
		return $result;			
	}	
	
	function getPositions($idObject,$option=-1){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$optionSuc = ($option!=-1) ? ' AND U.ID_SUCURSAL = '.$option: ''; 					
		$sql = "SELECT P.ID_TELEFONO AS ID,
				CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NAME,
				P.FECHA_GPS AS FECHA_GPS,
				E.DESCRIPCION_EVENTO AS EVENTO,
				P.LATITUD,
				P.LONGITUD,
				P.TIPO_GPS,
				P.VELOCIDAD,
				P.NIVEL_BATERIA,
				P.ANGULO,
				P.UBICACION,
				TIMESTAMPDIFF(MINUTE,FECHA_GPS,CURRENT_TIMESTAMP) AS UREPORTE				
				FROM USUARIOS  U   
				INNER JOIN SUCURSALES S 	   ON U.ID_SUCURSAL= S.ID_SUCURSAL
				INNER JOIN PROD_USR_TELEFONO T ON U.ID_USUARIO = T.ID_USUARIO
				INNER JOIN PROD_ULTIMA_POSICION P ON T.ID_TELEFONO = P.ID_TELEFONO
				INNER JOIN PROD_EVENTOS       E ON P.ID_EVENTO = E.ID_EVENTO
				WHERE S.ID_EMPRESA       = $idObject
				  $optionSuc
				  AND U.FLAG_OPERACIONES = 1
				ORDER BY U.ID_SUCURSAL ASC, NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;				
	}
	
	
	public function processInfo($aConfig,$aDataPositions){
		$aResult  = Array();
		
		foreach($aDataPositions as $items){	
			$items['STATREP']   = '';	
			$items['STATCOLOR'] = '';					
			/* Se valida que el equipo este encendido*/
			if($items['UREPORTE']<=$aConfig['TIEMPO_ENCENDIDO']){
				$items['STATREP']   = 'catEncendido';
				$items['STATCOLOR'] = 'btn-palegreen';
				$items['STATEXT']   = 'Encendido';
			/* Se valida que el equipo no halla reportado  */	
			}else if($items['UREPORTE']>$aConfig['TIEMPO_ENCENDIDO'] && $items['UREPORTE']<$aConfig['TIEMPO_APAGADO']){
				$items['STATREP']   = 'catNoReporte';
				$items['STATCOLOR'] = 'btn-darkorange';
				$items['STATEXT']   = 'Sin Reportar';
			/* Se valida que el equipo este apagado  */
			}else if($items['UREPORTE']>$aConfig['TIEMPO_APAGADO']){
				$items['STATREP']  = 'catApagado';
				$items['STATCOLOR'] = 'btn-purple';
				$items['STATEXT']   = 'Apagado';							
			}
			
			/* Se valida que el X tiempo sin reportar */
			if($items['UREPORTE']>$aConfig['TIEMPO_X_SIN_REPORTAR']){
				$items['STATREP']   = 'catXsinReporte';
				$items['STATCOLOR'] = 'btn-yellow';
				$items['STATEXT']   = $aConfig['TITULO_TIEMPO_X_SIN_REPORTAR'];
			}
					
			$aResult[] = $items;
		}
		return $aResult;
	}
	
	public function getLastPositions($values){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT U.ID_TELEFONO AS ID,
				U.FECHA_GPS AS FECHA_GPS,
				E.DESCRIPCION_EVENTO AS EVENTO,
				U.LATITUD,
				U.LONGITUD,
				U.TIPO_GPS,
				U.VELOCIDAD,
				U.NIVEL_BATERIA,
				U.ANGULO,
				U.UBICACION
				FROM PROD_ULTIMA_POSICION U
				INNER JOIN PROD_EVENTOS   E ON U.ID_EVENTO = E.ID_EVENTO
				WHERE U.ID_TELEFONO IN ($values)
				GROUP BY U.ID_TELEFONO"; 	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;				
	}	
	
	/*
	public function getCbo($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT U.ID_USUARIO AS ID, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NAME
				FROM USUARIOS U 										
				WHERE ID_SUCURSAL = $idObject
				  AND U.FLAG_OPERACIONES = 1
				ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	

	
	public function getTecnicosBySucursal($values,$idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT U.ID_USUARIO AS ID, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NAME, E.ID_SUCURSAL
				FROM USUARIOS   U  
				INNER JOIN SUCURSALES 		 S ON U.ID_SUCURSAL= S.ID_SUCURSAL
				INNER JOIN PROD_USR_TELEFONO T ON U.ID_USUARIO = T.ID_USUARIO
				WHERE E.ID_SUCURSAL IN ($values)
				 AND  S.ID_EMPRESA = $idEmpresa
				 AND  U.FLAG_OPERACIONES = 1
				ORDER BY E.ID_SUCURSAL ASC, NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}
        
		return $result;				
	}
	
	public function  getTecnicosByEmpresa($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT U.ID_USUARIO AS ID, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NAME
				FROM USUARIOS U   
				INNER JOIN SUCURSALES S ON U.ID_SUCURSAL  = S.ID_SUCURSAL
				INNER JOIN PROD_USR_TELEFONO T ON U.ID_USUARIO = T.ID_USUARIO
				WHERE S.ID_EMPRESA = $idEmpresa
				  AND U.FLAG_OPERACIONES = 1
				ORDER BY E.ID_SUCURSAL ASC, NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}
        
		return $result;			
	}*/
}