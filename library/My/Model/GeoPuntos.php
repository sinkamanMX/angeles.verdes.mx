<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_GeoPuntos extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_GEOREFERENCIAS';
	protected $_primary = 'ID_REFERENCIAS';
	
	public function getTipos(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT *
				FROM PROD_TIPO_GEOS	
				WHERE ESTATUS = 1			
				ORDER BY DESCRIPCION DESC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	

	public function getDataGeo($idObject,$iSeach=0){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$sFilter = ($iSeach==0) ? 'G.ID_SUCURSAL = '.$idObject : 'G.ID_EMPRESA = '.$idObject; 		
    	$sql ="SELECT G.ID_GEOREFERENCIA AS ID, 
				TIPO_OBJECTO,
				T.ID_TIPO_GEO,
				G.DESCRIPCION,
				C.DESCRIPCION,C.HTML_CODE,
				G.LATITUD,
				G.LONGITUD,
				G.RADIO,				
				ASTEXT(D.MAP_OBJECT) AS MAP_OBJECT,
				T.ICONO
				FROM PROD_GEOREFERENCIAS G
				INNER JOIN SUCURSALES     S ON G.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN PROD_TIPO_GEOS T ON G.ID_TIPO     = T.ID_TIPO_GEO
				INNER JOIN PROD_COLORES   C ON G.ID_COLOR    = C.ID_COLOR
				 LEFT JOIN PROD_GEOREFERENCIAS_DETALLE D ON G.`ID_GEOREFERENCIA` = D.ID_GEOREFERENCIA
				WHERE $sFilter
				  AND G.ESTATUS     = 1";  
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}		
	
	public function getPoints($idObject,$iSeach=0){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
		$sFilter = ($iSeach==0) ? 'ID_SUCURSAL = '.$idObject : 'ID_EMPRESA = '.$idObject;
    	$sql ="SELECT *
				FROM $this->_name
				WHERE TIPO_OBJECTO = 'P'
				AND  $sFilter
				ORDER BY DESCRIPCION DESC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getCercas($idObject,$iSeach=0){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
		$sFilter = ($iSeach==0) ? 'ID_SUCURSAL = '.$idObject : 'ID_EMPRESA = '.$idObject;
    	$sql ="SELECT *
				FROM $this->_name
				WHERE TIPO_OBJECTO = 'C'
				AND  $sFilter
				ORDER BY DESCRIPCION DESC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	
	public function getRutas($idObject,$iSeach=0){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
		$sFilter = ($iSeach==0) ? 'ID_SUCURSAL = '.$idObject : 'ID_EMPRESA = '.$idObject;
    	$sql ="SELECT *
				FROM $this->_name
				WHERE TIPO_OBJECTO = 'R'
				AND  $sFilter
				ORDER BY DESCRIPCION DESC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	
	function getCampamentos($idObject,$iTypeuser=0){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$sFilter = ($iTypeuser==0) ? " ID_SUCURSAL = $idObject" : " ID_EMPRESA = $idObject" ;	 		
    	$sql ="SELECT *     			
    			FROM PROD_CAMPAMENTOS 
    			WHERE $sFilter";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	
	function getPuntosAsistencia($idObject,$iTypeuser=0){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$sFilter = ($iTypeuser==0) ? " ID_SUCURSAL = $idObject" : " ID_EMPRESA = $idObject" ;	 		
    	$sql ="SELECT *     			
    			FROM PROD_PUNTOS_ASISTENCIA 
    			WHERE $sFilter";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
}	