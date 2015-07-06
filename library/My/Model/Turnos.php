<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Turnos extends My_Db_Table
{
    protected $_schema 	= 'BD_SIAMES';
	protected $_name 	= 'PROD_HIST_TURNOS';
	protected $_primary = 'PROD_HIST_TURNOS';

	public function getTurnos($iSucursal,$sFecha,$sFechaEnd){		
		$result= Array();
		$sFilter  = ($iSucursal!=-1) ? "AND U.ID_SUCURSAL = ".$iSucursal : '';
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT T.ID_HIST_TURNO AS ID, T.FECHA_INICIO,T.FECHA_FIN, U.USUARIO,S.DESCRIPCION, T.ID_USUARIO
				FROM PROD_HIST_TURNOS T
				INNER JOIN USUARIOS U ON T.ID_USUARIO = U.ID_USUARIO
				INNER JOIN SUCURSALES S ON U.ID_SUCURSAL = S.ID_SUCURSAL
				WHERE ( (T.FECHA_INICIO BETWEEN '$sFecha' AND '$sFechaEnd') OR 
					    (T.FECHA_FIN    BETWEEN '$sFecha' AND '$sFechaEnd') )".$sFilter;    
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;		
	}	
	
	public function getDataInfoResult($idObject,$idForm,$bLimit=true){
		$result= Array();
		$this->query("SET NAMES utf8",false); 	
		$filter = ($bLimit) ? 'LIMIT 1': '';	
    	$sql ="SELECT R.*,S.DESCRIPCION AS N_SUCURSAL, SUBSTRING(T.TELEFONO, -4) AS N_PHONE,T.ID_TELEFONO,CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS N_USUARIO
					FROM PROD_FORM_RESULTADO R
					LEFT JOIN USUARIOS   U ON R.ID_USUARIO_CONTESTO = U.ID_USUARIO
					LEFT JOIN SUCURSALES S ON U.ID_SUCURSAL         = S.ID_SUCURSAL
					LEFT JOIN PROD_TELEFONOS   T ON R.ID_EQUIPO           = T.ID_TELEFONO
					WHERE R.ID_FORMULARIO    = $idForm
						 AND R.ID_HIST_TURNO = $idObject
					  ORDER BY R.FECHA_CAPTURA_EQUIPO DESC
					  $filter"; 	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = ($bLimit) ? $query[0] : $query ;			
		}
        
		return $result;		
	}
	
	public function getElementos($idObject,$bFilter=false){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
		$sFilter = (!$bFilter) ? 'AND E.ID_TIPO NOT IN (8)': '';		
    	$sql ="SELECT R.ID_RESULTADO, R.ID_ELEMENTO, IF( E.TITULO IS NULL ,E.DESCIPCION,E.TITULO) AS DESCIPCION  , R.CONTESTACION,E.ID_TIPO
					FROM PROD_FORM_DETALLE_RESULTADO R
					INNER JOIN PROD_ELEMENTOS E ON R.ID_ELEMENTO = E.ID_ELEMENTO
					WHERE R.ID_RESULTADO = $idObject
					$sFilter 
					AND E.ID_ELEMENTO NOT IN (10,11)";
    	$query   = $this->query($sql);   
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;		
	}	
	
	public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 	
    	$sql ="SELECT H.ID_HIST_TURNO AS  ID, H.FECHA_INICIO, H.FECHA_FIN, S.DESCRIPCION AS CAMPAMENTO,H.ID_USUARIO,
				U.USUARIO 
				FROM  PROD_HIST_TURNOS H
				INNER JOIN USUARIOS U ON H.ID_USUARIO = U.ID_USUARIO
				INNER JOIN SUCURSALES S ON U.ID_SUCURSAL = S.ID_SUCURSAL
				WHERE H.ID_HIST_TURNO = $idObject LIMIT 1";    	    		
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query[0];			
		}
        
		return $result;		
	}	
	
	public function getServicios($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 	
    	$sql ="SELECT  S.DESCRIPCION AS N_SUCURSAL,
					  U.USUARIO,
					  R.FECHA_CAPTURA_EQUIPO,
					  IF(P.ID_POSICION IS NULL, 'Sin Ubicacion',P.UBICACION) AS UBICACION,
  						R.ID_RESULTADO
					FROM PROD_FORM_RESULTADO R
					INNER JOIN USUARIOS   U ON R.ID_USUARIO_CONTESTO = U.ID_USUARIO
					INNER JOIN SUCURSALES S ON U.ID_SUCURSAL = S.ID_SUCURSAL
					 LEFT JOIN PROD_HIST_RESULTADO I ON R.ID_RESULTADO = I.ID_RESULTADO
					 LEFT JOIN PROD_HISTORICO_POSICION P ON I.ID_POSICION = P.ID_POSICION 
					WHERE R.ID_FORMULARIO = 2
                      AND R.ID_HIST_TURNO = $idObject
					  ORDER BY R.FECHA_CAPTURA_EQUIPO DESC	";    
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
        
		return $result;		
	}		
	
	public function getActividades($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 	
    	$sql ="SELECT  S.DESCRIPCION AS N_SUCURSAL,
					  U.USUARIO,
					  R.FECHA_CAPTURA_EQUIPO,
					  IF(P.ID_POSICION IS NULL, 'Sin Ubicacion',P.UBICACION) AS UBICACION,
  						R.ID_RESULTADO
					FROM PROD_FORM_RESULTADO R
					INNER JOIN USUARIOS   U ON R.ID_USUARIO_CONTESTO = U.ID_USUARIO
					INNER JOIN SUCURSALES S ON U.ID_SUCURSAL = S.ID_SUCURSAL
					 LEFT JOIN PROD_HIST_RESULTADO I ON R.ID_RESULTADO = I.ID_RESULTADO
					 LEFT JOIN PROD_HISTORICO_POSICION P ON I.ID_POSICION = P.ID_POSICION 
					WHERE R.ID_FORMULARIO = 3
                      AND R.ID_HIST_TURNO = $idObject
					  ORDER BY R.FECHA_CAPTURA_EQUIPO DESC	";    	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
        
		return $result;		
	}	
	
	public function getActividadesResume($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 	
    	$sql ="SELECT S.`FECHA_CAPTURA_EQUIPO`, P.UBICACION,  IF( E.TITULO IS NULL ,E.DESCIPCION,E.TITULO) AS DESCIPCION , R.CONTESTACION, R.ID_RESULTADO,E.ID_ELEMENTO
					FROM PROD_FORM_DETALLE_RESULTADO R
					INNER JOIN PROD_FORM_RESULTADO S ON R.`ID_RESULTADO` = S.`ID_RESULTADO`
					INNER JOIN PROD_ELEMENTOS E ON R.ID_ELEMENTO = E.ID_ELEMENTO
					LEFT JOIN PROD_HIST_RESULTADO     L ON R.ID_RESULTADO = L.ID_RESULTADO
					LEFT JOIN PROD_HISTORICO_POSICION P ON L.`ID_POSICION` = P.`ID_POSICION`
					WHERE R.ID_RESULTADO IN (
						SELECT R.`ID_RESULTADO`
						FROM PROD_FORM_RESULTADO R	
						WHERE R.ID_FORMULARIO = 3
	                      AND R.ID_HIST_TURNO = $idObject	
					)
					 AND E.ID_ELEMENTO   IN (37,42,38,39)
					 ORDER BY R.ID_RESULTADO, S.FECHA_CAPTURA_EQUIPO ASC, E.ID_ELEMENTO ASC";    	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
        
		return $result;		
	}
	
	public function getResumeBitacora($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 	
    	$sql ="SELECT S.`FECHA_CAPTURA_EQUIPO`,  IF( E.TITULO IS NULL ,E.DESCIPCION,E.TITULO) AS DESCIPCION , R.CONTESTACION, R.ID_RESULTADO,E.ID_ELEMENTO,
					S.ID_HIST_TURNO
					FROM PROD_FORM_DETALLE_RESULTADO R
					INNER JOIN PROD_FORM_RESULTADO S ON R.`ID_RESULTADO` = S.`ID_RESULTADO`
					INNER JOIN PROD_ELEMENTOS E ON R.ID_ELEMENTO = E.ID_ELEMENTO
					WHERE R.ID_RESULTADO IN (
					SELECT R.`ID_RESULTADO`
					FROM PROD_FORM_RESULTADO R	
						WHERE R.ID_FORMULARIO = 2
	                      AND R.ID_HIST_TURNO = $idObject	
					)
					 AND E.ID_ELEMENTO   IN (13,31,32,34)
					 ORDER BY R.ID_RESULTADO, S.FECHA_CAPTURA_EQUIPO ASC, E.ID_ELEMENTO ASC";    	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
        
		return $result;			
	}
	
	public function getPhoneResult($idResult){
		$result= Array();
		$this->query("SET NAMES utf8",false); 	
    	$sql ="SELECT R.ID_USUARIO, T.ID_TELEFONO,T.IDENTIFICADOR, R.FECHA_INICIO, R.FECHA_FIN
				FROM PROD_HIST_TURNOS R
				LEFT JOIN PROD_TELEFONOS T ON R.ID_USUARIO = T.ID_USUARIO_ACTUAL
				WHERE R.ID_HIST_TURNO = $idResult LIMIT 1";    	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query[0];			
		}
        
		return $result;			
	}
	
	public function getValuesInend($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 	
    	$sql ="SELECT S.`FECHA_CAPTURA_EQUIPO`,  IF( E.TITULO IS NULL ,E.DESCIPCION,E.TITULO) AS DESCIPCION , R.CONTESTACION, R.ID_RESULTADO,E.ID_ELEMENTO,
					S.ID_HIST_TURNO
					FROM PROD_FORM_DETALLE_RESULTADO R
					INNER JOIN PROD_FORM_RESULTADO S ON R.`ID_RESULTADO` = S.`ID_RESULTADO`
					INNER JOIN PROD_ELEMENTOS E ON R.ID_ELEMENTO = E.ID_ELEMENTO
					WHERE R.ID_RESULTADO IN (
					SELECT R.`ID_RESULTADO`
					FROM PROD_FORM_RESULTADO R	
						WHERE R.ID_FORMULARIO IN(1,4)
						  AND R.ID_HIST_TURNO = $idObject
					)
					 AND E.ID_ELEMENTO   IN (10,11,44,45)
					 ORDER BY R.ID_RESULTADO, S.FECHA_CAPTURA_EQUIPO ASC, E.ID_ELEMENTO ASC";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query;			
		}
        
		return $result;				
	}
	
	public function getNotasTurno($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 	
    	$sql ="SELECT S.`FECHA_CAPTURA_EQUIPO`, P.UBICACION,  IF( E.TITULO IS NULL ,E.DESCIPCION,E.TITULO) AS DESCIPCION , R.CONTESTACION, R.ID_RESULTADO,E.ID_ELEMENTO
					FROM PROD_FORM_DETALLE_RESULTADO R
					INNER JOIN PROD_FORM_RESULTADO S ON R.`ID_RESULTADO` = S.`ID_RESULTADO`
					INNER JOIN PROD_ELEMENTOS E ON R.ID_ELEMENTO = E.ID_ELEMENTO
					LEFT JOIN PROD_HIST_RESULTADO     L ON R.ID_RESULTADO = L.ID_RESULTADO
					LEFT JOIN PROD_HISTORICO_POSICION P ON L.`ID_POSICION` = P.`ID_POSICION`
					WHERE R.ID_RESULTADO IN (
						SELECT R.`ID_RESULTADO`
						FROM PROD_FORM_RESULTADO R	
						WHERE R.ID_FORMULARIO = 4
	                      AND R.ID_HIST_TURNO = $idObject	
					)
					 AND E.ID_ELEMENTO   IN (46)
					 ORDER BY R.ID_RESULTADO, S.FECHA_CAPTURA_EQUIPO ASC, E.ID_ELEMENTO ASC LIMIT 1";    	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result = $query[0];			
		}
        
		return $result;		
	}
}