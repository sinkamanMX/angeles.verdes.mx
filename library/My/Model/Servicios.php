<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Servicios extends My_Db_Table
{
    protected $_schema 	= 'BD_SIAMES';
	protected $_name 	= 'SERVICIOS';
	protected $_primary = 'ID_SERVICIOS';
	

	public function getServicios($iSucursal,$sFechaIn,$sFechaFin,$iUsuario){
		$result= Array();

		$sFilter  = ($iSucursal>-1) ? ' AND U.ID_SUCURSAL = '.$iSucursal.' ' : '';
		$sFilter .= ($iUsuario>-1) ? ' AND R.ID_USUARIO_CONTESTO = '.$iUsuario.' ': '';
 		
    	$sql ='SELECT  S.DESCRIPCION AS N_SUCURSAL,
					  CONCAT(U.NOMBRE," ",U.APELLIDOS) AS N_USUARIO,
					  R.FECHA_CAPTURA_EQUIPO,
					  IF(P.ID_POSICION IS NULL, "Sin Ubicacion",P.UBICACION) AS UBICACION,
  					  R.ID_RESULTADO,
  					  R.ID_HIST_TURNO,
  						(
							SELECT GROUP_CONCAT(CONTESTACION SEPARATOR "|") AS RESULT 
							  FROM PROD_FORM_DETALLE_RESULTADO
							 WHERE ID_ELEMENTO  IN (3,5)
							  AND  ID_RESULTADO IN ( 
							 	SELECT ID_RESULTADO
  								FROM PROD_FORM_RESULTADO
  								WHERE ID_HIST_TURNO = R.ID_HIST_TURNO
  						 		AND  ID_FORMULARIO = 1
  						 	)  						
  						) AS N_UNIDAD,  
						(
							SELECT GROUP_CONCAT(CONTESTACION SEPARATOR "|")
							  FROM PROD_FORM_DETALLE_RESULTADO
							 WHERE ID_ELEMENTO  IN (13)
							  AND  ID_RESULTADO IN ( 
 
							 	SELECT ID_RESULTADO
  								FROM PROD_FORM_RESULTADO
  								WHERE ID_HIST_TURNO = R.ID_HIST_TURNO
  						 		AND  ID_FORMULARIO = 2
  						 	)  						
  						) AS N_SERVICIO,    												
  						P.LATITUD, P.LONGITUD    										
					FROM PROD_FORM_RESULTADO R
					INNER JOIN USUARIOS   U ON R.ID_USUARIO_CONTESTO = U.ID_USUARIO
					INNER JOIN SUCURSALES S ON U.ID_SUCURSAL = S.ID_SUCURSAL
					 LEFT JOIN PROD_HIST_RESULTADO I ON R.ID_RESULTADO = I.ID_RESULTADO
					 LEFT JOIN PROD_HISTORICO_POSICION P ON I.ID_POSICION = P.ID_POSICION 
					WHERE CAST(R.FECHA_CAPTURA_EQUIPO AS DATE) BETWEEN "'.$sFechaIn.'" AND "'.$sFechaFin.'" 
					  AND R.ID_FORMULARIO = 2
					  '.$sFilter.'
					  ORDER BY R.FECHA_CAPTURA_EQUIPO DESC'; 
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;	
	}
	
	public function getActividades($iSucursal,$sFechaIn,$sFechaFin,$idUsuario){		
		$result= Array();
		$sFilter  = ($iSucursal>-1) ? ' AND U.ID_SUCURSAL = '.$iSucursal.' ' : '';
		$sFilter .= ($idUsuario>-1) ? ' AND R.ID_USUARIO_CONTESTO = '.$idUsuario.' ': '';
		//$this->query("SET NAMES utf8",false); 		
    	$sql ='SELECT  S.DESCRIPCION AS N_SUCURSAL,
					  CONCAT(U.NOMBRE," ",U.APELLIDOS) AS N_USUARIO,
					  R.FECHA_CAPTURA_EQUIPO,
					  IF(P.ID_POSICION IS NULL, "Sin Ubicacion",P.UBICACION) AS UBICACION,
  					  R.ID_RESULTADO,
  					  R.ID_HIST_TURNO,
  						(
							SELECT GROUP_CONCAT(CONTESTACION SEPARATOR "|") AS RESULT 
							  FROM PROD_FORM_DETALLE_RESULTADO
							 WHERE ID_ELEMENTO  IN (3,5)
							  AND  ID_RESULTADO IN ( 
							 	SELECT ID_RESULTADO
  								FROM PROD_FORM_RESULTADO
  								WHERE ID_HIST_TURNO = R.ID_HIST_TURNO
  						 		AND  ID_FORMULARIO = 1
  						 	)  						
  						) AS N_UNIDAD,
  						(
							SELECT GROUP_CONCAT(CONTESTACION SEPARATOR "|")
							  FROM PROD_FORM_DETALLE_RESULTADO
							 WHERE ID_ELEMENTO  IN (37)
							  AND  ID_RESULTADO IN ( 
 
							 	SELECT ID_RESULTADO
  								FROM PROD_FORM_RESULTADO
  								WHERE ID_HIST_TURNO = R.ID_HIST_TURNO
  						 		AND  ID_FORMULARIO = 3
  						 	)  						
  						) AS N_ACTIVIDAD, 
  						P.LATITUD, P.LONGITUD    										
					FROM PROD_FORM_RESULTADO R
					INNER JOIN USUARIOS   U ON R.ID_USUARIO_CONTESTO = U.ID_USUARIO
					INNER JOIN SUCURSALES S ON U.ID_SUCURSAL = S.ID_SUCURSAL
					 LEFT JOIN PROD_HIST_RESULTADO I ON R.ID_RESULTADO = I.ID_RESULTADO
					 LEFT JOIN PROD_HISTORICO_POSICION P ON I.ID_POSICION = P.ID_POSICION 
					WHERE CAST(R.FECHA_CAPTURA_EQUIPO AS DATE) BETWEEN "'.$sFechaIn.'" AND "'.$sFechaFin.'" 
					  AND R.ID_FORMULARIO = 3
					  '.$sFilter.'
					  ORDER BY R.FECHA_CAPTURA_EQUIPO DESC'; 
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;		
	}
	
    public function getElementos($idObject){
    	$result     = Array();    	
    	try{ 
    		$sql = "SELECT R.ID_ELEMENTO, E.DESCIPCION
					FROM   PROD_FORMULARIO_ELEMENTOS R
					INNER JOIN PROD_ELEMENTOS E ON R.ID_ELEMENTO = E.ID_ELEMENTO
					WHERE R.ID_FORMULARIO = $idObject
					  AND E.ID_TIPO NOT IN (8)
					ORDER BY R.ORDEN";
			$query   = $this->query($sql);
			if(count($query)>0){		  
				$result = $query;			
			}	
	        
			return $result;			
    	}catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
    }	
    
    public function getResultados($idObject){
    	$result     = Array();    	
    	try{ 
    		$sql = "SELECT R.ID_RESULTADO, R.ID_ELEMENTO, R.CONTESTACION, E.ID_TIPO
					FROM PROD_FORM_DETALLE_RESULTADO R
					INNER JOIN PROD_ELEMENTOS E ON R.ID_ELEMENTO = E.ID_ELEMENTO
					WHERE R.ID_RESULTADO = $idObject
					  AND E.ID_TIPO NOT IN (8)";
			$query   = $this->query($sql);
			if(count($query)>0){		  
				$result = $query;			
			}	
	        
			return $result;			
    	}catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
    }

	public function getInfoForm($idResult){
	    $result     = Array();    	
    	try{ 
    		$sql = "SELECT U.USUARIO, R.FECHA_CAPTURA_EQUIPO,F.`DESCRIPCION`, S.DESCRIPCION AS CAMPAMENTO , R.`FECHA_CAPTURA_SERVIDOR`,
					IF(P.ID_POSICION IS NULL, 'Sin Ubicacion',P.UBICACION) AS UBICACION
					FROM PROD_FORM_RESULTADO R
					INNER JOIN USUARIOS U ON R.ID_USUARIO_CONTESTO = U.ID_USUARIO
					INNER JOIN SUCURSALES S ON U.ID_SUCURSAL = S.ID_SUCURSAL
					INNER JOIN PROD_FORMULARIO F ON R.`ID_FORMULARIO`  = F.ID_FORMULARIO
					LEFT JOIN PROD_HIST_RESULTADO L ON R.ID_RESULTADO = L.ID_RESULTADO
					LEFT JOIN PROD_HISTORICO_POSICION P ON L.`ID_POSICION` = P.`ID_POSICION`
				WHERE R.ID_RESULTADO = $idResult LIMIT 1";
			$query   = $this->query($sql);
			if(count($query)>0){		  
				$result = $query[0];			
			}	
	        
			return $result;			
    	}catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }		
	}
	
	public function getDataActividad($idResult){		
		$result= Array();		
    	$sql ='SELECT  S.DESCRIPCION AS N_SUCURSAL,
					  CONCAT(U.NOMBRE," ",U.APELLIDOS) AS N_USUARIO,
					  R.FECHA_CAPTURA_EQUIPO,
					  IF(P.ID_POSICION IS NULL, "Sin Ubicacion",P.UBICACION) AS UBICACION,
  					  R.ID_RESULTADO,
  					  R.ID_HIST_TURNO,
  						(
							SELECT GROUP_CONCAT(CONTESTACION SEPARATOR "|") AS RESULT 
							  FROM PROD_FORM_DETALLE_RESULTADO
							 WHERE ID_ELEMENTO  IN (3,5)
							  AND  ID_RESULTADO IN ( 
							 	SELECT ID_RESULTADO
  								FROM PROD_FORM_RESULTADO
  								WHERE ID_HIST_TURNO = R.ID_HIST_TURNO
  						 		AND  ID_FORMULARIO = 1
  						 	)  						
  						) AS N_UNIDAD,
  						(
							SELECT GROUP_CONCAT(CONTESTACION SEPARATOR "|")
							  FROM PROD_FORM_DETALLE_RESULTADO
							 WHERE ID_ELEMENTO  IN (37)
							  AND  ID_RESULTADO IN ( 
 
							 	SELECT ID_RESULTADO
  								FROM PROD_FORM_RESULTADO
  								WHERE ID_HIST_TURNO = R.ID_HIST_TURNO
  						 		AND  ID_FORMULARIO = 3
  						 	)  						
  						) AS N_ACTIVIDAD, 
  						P.LATITUD, P.LONGITUD    										
					FROM PROD_FORM_RESULTADO R
					INNER JOIN USUARIOS   U ON R.ID_USUARIO_CONTESTO = U.ID_USUARIO
					INNER JOIN SUCURSALES S ON U.ID_SUCURSAL = S.ID_SUCURSAL
					 LEFT JOIN PROD_HIST_RESULTADO I ON R.ID_RESULTADO = I.ID_RESULTADO
					 LEFT JOIN PROD_HISTORICO_POSICION P ON I.ID_POSICION = P.ID_POSICION 
					WHERE R.ID_RESULTADO  = '.$idResult.'
					  AND R.ID_FORMULARIO = 3		 
					  ORDER BY R.FECHA_CAPTURA_EQUIPO DESC'; 
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;		
	}	
	
	public function getDataServicio($idResult){		
		$result= Array();		
    	$sql ='SELECT  S.DESCRIPCION AS N_SUCURSAL,
					  CONCAT(U.NOMBRE," ",U.APELLIDOS) AS N_USUARIO,
					  R.FECHA_CAPTURA_EQUIPO,
					  IF(P.ID_POSICION IS NULL, "Sin Ubicacion",P.UBICACION) AS UBICACION,
  					  R.ID_RESULTADO,
  					  R.ID_HIST_TURNO,
  						(
							SELECT GROUP_CONCAT(CONTESTACION SEPARATOR "|") AS RESULT 
							  FROM PROD_FORM_DETALLE_RESULTADO
							 WHERE ID_ELEMENTO  IN (3,5)
							  AND  ID_RESULTADO IN ( 
							 	SELECT ID_RESULTADO
  								FROM PROD_FORM_RESULTADO
  								WHERE ID_HIST_TURNO = R.ID_HIST_TURNO
  						 		AND  ID_FORMULARIO = 1
  						 	)  						
  						) AS N_UNIDAD,
						(
							SELECT GROUP_CONCAT(CONTESTACION SEPARATOR "|")
							  FROM PROD_FORM_DETALLE_RESULTADO
							 WHERE ID_ELEMENTO  IN (13)
							  AND  ID_RESULTADO IN ( 
 
							 	SELECT ID_RESULTADO
  								FROM PROD_FORM_RESULTADO
  								WHERE ID_HIST_TURNO = R.ID_HIST_TURNO
  						 		AND  ID_FORMULARIO = 2
  						 	)  						
  						) AS N_SERVICIO,    						
  						P.LATITUD, P.LONGITUD    										
					FROM PROD_FORM_RESULTADO R
					INNER JOIN USUARIOS   U ON R.ID_USUARIO_CONTESTO = U.ID_USUARIO
					INNER JOIN SUCURSALES S ON U.ID_SUCURSAL = S.ID_SUCURSAL
					 LEFT JOIN PROD_HIST_RESULTADO I ON R.ID_RESULTADO = I.ID_RESULTADO
					 LEFT JOIN PROD_HISTORICO_POSICION P ON I.ID_POSICION = P.ID_POSICION 
					WHERE R.ID_RESULTADO  = '.$idResult.'
					  AND R.ID_FORMULARIO = 2	 
					  ORDER BY R.FECHA_CAPTURA_EQUIPO DESC'; 
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;		
	}	
}