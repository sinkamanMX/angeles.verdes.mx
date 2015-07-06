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
	protected $_primary = 'ID_GEOREFERENCIA';
	
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

	public function getCboTipos(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_TIPO_GEO AS ID, DESCRIPCION AS NAME
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
				G.DESCRIPCION AS N_DESC,
				C.DESCRIPCION,C.HTML_CODE,
				G.LATITUD,
				G.LONGITUD,
				G.RADIO,				
				ASTEXT(D.MAP_OBJECT) AS MAP_OBJECT,
				T.ICONO,
				IF(G.ESTATUS = 0,'Inactivo','Activo') AS ESTATUS,
				IF(TIPO_OBJECTO='G','Punto',  IF(TIPO_OBJECTO='C','Area', 'Ruta') ) AS TIPO_REFERENCIA,
				T.DESCRIPCION AS N_TIPO,
				S.DESCRIPCION AS N_SUCURSAL,
				G.CLAVE_UNICA
				FROM PROD_GEOREFERENCIAS G
				INNER JOIN SUCURSALES     S ON G.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN PROD_TIPO_GEOS T ON G.ID_TIPO     = T.ID_TIPO_GEO
				 LEFT JOIN PROD_COLORES   C ON G.ID_COLOR    = C.ID_COLOR
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
	
	function getDataRow($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT *
				FROM $this->_name	
				WHERE $this->_primary = $idObject
				LIMIT 1";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}
	
	/**
	 * 
	 * Inserta un nuevo registro en la tabla de formularios
	 * @param Array $aDataIn
	 * @return Array Id, Estatus de la operacion.
	 */
    public function insertRowPoint($aDataIn){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name			 
					SET ID_EMPRESA 		=  ".$aDataIn['inputEmpresa'].",
						ID_SUCURSAL		=  ".$aDataIn['inputSucursal'].",
						ID_TIPO			=  ".$aDataIn['inputTipo'].",
						ID_COLOR		=  NULL,
						DESCRIPCION		= '".$aDataIn['inputDescripcion']."',
						CLAVE_UNICA		= '".$aDataIn['inputClave']."',
						LATITUD			=  ".$aDataIn['inputLatOrigen'].",
						LONGITUD		=  ".$aDataIn['inputLonOrigen'].",
						RADIO			=  ".$aDataIn['inputRadio'].",
						TIPO_OBJECTO	=  'G',
						ESTATUS			=  ".$aDataIn['inputEstatus'].",
						CREADO			=  CURRENT_TIMESTAMP";  
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
    
	/**
	 * 
	 * Actualiza un nuevo registro en la tabla de formularios
	 * @param Array $aDataIn
	 * @return Boolean Estatus de la operacion
	 */
    public function updateRow($aDataIn){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE $this->_name			 
				SET ID_EMPRESA 		=  ".$aDataIn['inputEmpresa'].",
					ID_SUCURSAL		=  ".$aDataIn['inputSucursal'].",
					ID_TIPO			=  ".$aDataIn['inputTipo'].",
					DESCRIPCION		= '".$aDataIn['inputDescripcion']."',
					CLAVE_UNICA		= '".$aDataIn['inputClave']."',
					LATITUD			=  ".$aDataIn['inputLatOrigen'].",
					LONGITUD		=  ".$aDataIn['inputLonOrigen'].",
					RADIO			=  ".$aDataIn['inputRadio'].",
					ESTATUS			=  ".$aDataIn['inputEstatus']."
				WHERE $this->_primary =".$aDataIn['catId']." LIMIT 1";
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

	public function getFilterUp(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_TIPO_GEO AS ID, CLAVE_TIPO
				FROM PROD_TIPO_GEOS	
				WHERE ESTATUS = 1			
				ORDER BY DESCRIPCION DESC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			foreach($query as $key => $items){
				$result[$items['CLAVE_TIPO']] = $items['ID'];
			}	
		}	
		return $result;		
	}   
	
	public function getFilterPuntos(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_GEOREFERENCIA AS ID, CLAVE_UNICA
				FROM PROD_GEOREFERENCIAS	
				ORDER BY  CLAVE_UNICA ASC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			foreach($query as $key => $items){
				$result[$items['CLAVE_UNICA']] = $items['ID'];
			}	
		}	
		return $result;		
	}

	public function getFilterSucursales(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_SUCURSAL AS ID				
				FROM SUCURSALES	";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			foreach($query as $key => $items){
				$result[$items['ID']] = $items['ID'];
			}	
		}	
		return $result;		
	}  	

	public function getFilterColores(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_COLOR AS ID, DESCRIPCION
				FROM PROD_COLORES	
				ORDER BY DESCRIPCION ASC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			foreach($query as $key => $items){
				$result[$items['DESCRIPCION']] = $items['ID'];
			}	
		}	
		return $result;		
	}	
	
	/**
	 * 
	 * Inserta un nuevo registro en la tabla de formularios
	 * @param Array $aDataIn
	 * @return Array Id, Estatus de la operacion.
	 */
    public function insertRowGeo($aDataIn){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name			 
					SET ID_EMPRESA 		=  ".$aDataIn['inputEmpresa'].",
						ID_SUCURSAL		=  ".$aDataIn['inputSucursal'].",
						ID_TIPO			=  ".$aDataIn['inputTipo'].",
						ID_COLOR		=  ".$aDataIn['inputColor'].",
						DESCRIPCION		= '".$aDataIn['inputDescripcion']."',
						CLAVE_UNICA		= '".$aDataIn['inputClave']."',
						LATITUD			=  NULL,
						LONGITUD		=  NULL,
						RADIO			=  NULL,
						TIPO_OBJECTO	= '".$aDataIn['inputTypeObj']."',
						ESTATUS			=  ".$aDataIn['inputEstatus'].",
						CREADO			=  CURRENT_TIMESTAMP";  
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
    
	/**
	 * 
	 * Inserta un nuevo registro en la tabla de formularios
	 * @param Array $aDataIn
	 * @return Array Id, Estatus de la operacion.
	 */
    public function insertSpatialRow($aDataIn){
        $result     = Array();
        $result['status']  = false;
        
        $sql=" INSERT INTO PROD_GEOREFERENCIAS_DETALLE (ID_GEOREFERENCIA,MAP_OBJECT)
				VALUES (".$aDataIn['id']." ,GEOMFROMTEXT('".$aDataIn['object']."'))";   		 
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
}	