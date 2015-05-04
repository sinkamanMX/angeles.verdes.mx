<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Formularios extends My_Db_Table
{
	protected $_schema 	= 'BD_SIAMES';
	protected $_name 	= 'PROD_FORMULARIO';
	protected $_primary = 'ID_FORMULARIO';

	/**
	 * 
	 * Devuelve la informacion de un unformulario.
	 * @param int $idObject
	 */
	public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT *
				FROM $this->_name
				WHERE $this->_primary = $idObject LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}
	/**
	 * 
	 * * Se valida que no existe el mismo titulo.
	 * @param String $stringSearch
	 * @param String $idObject
	 * @param String $idEmpresa
	 * @return Array Resultado del query
	 */	
	public function validateDataBy($stringSearch="", $idObject="",$idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		
    	$sql ="SELECT *
				FROM $this->_name
				WHERE 	TITULO 	   = '".$stringSearch."'
				  AND   ID_EMPRESA = $idEmpresa  
				  AND   $this->_primary <> $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	/**
	 * 
	 * Inserta un nuevo registro en la tabla de formularios
	 * @param Array $aDataIn
	 * @return Array Id, Estatus de la operacion.
	 */
    public function insertRow($aDataIn){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name			 
					SET ID_EMPRESA		=  ".$aDataIn['inputEmpresa'].",
						TITULO			= '".$aDataIn['inputTitulo']."',
						DESCRIPCION		= '".$aDataIn['inputDescripcion']."',
						ORDEN			= '".$aDataIn['inputOrden']."',
						ID_USUARIO_CREO	= '".$aDataIn['userCreate']."',
						FECHA_CREACION	= CURRENT_TIMESTAMP,
						ACTIVO			= '".$aDataIn['inputEstatus']."',
						FOTOS_EXTRAS	= '".$aDataIn['inputPhotos']."',
						QRS_EXTRAS		= '".$aDataIn['inputQrs']."',
						FIRMAS_EXTRAS	= '".$aDataIn['inputFirma']."',
						LOCALIZACION	= '".$aDataIn['inputLocate']."'";
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
				SET TITULO			= '".$aDataIn['inputTitulo']."',
					DESCRIPCION		= '".$aDataIn['inputDescripcion']."',
					ORDEN			= '".$aDataIn['inputOrden']."',
					ID_USUARIO_MODIFICO	= '".$aDataIn['userCreate']."',
					FECHA_MODIFICACION	= CURRENT_TIMESTAMP,
					ACTIVO			= '".$aDataIn['inputEstatus']."',
					FOTOS_EXTRAS	= '".$aDataIn['inputPhotos']."',
					QRS_EXTRAS		= '".$aDataIn['inputQrs']."',
					FIRMAS_EXTRAS	= '".$aDataIn['inputFirma']."',
					LOCALIZACION	= '".$aDataIn['inputLocate']."'
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

	/**
	 * 
	 * Obtiene los elementos de un formulario
	 * @param int $idObject
	 * @param int $idEmpresa
	 */
    public function getElementos($idObject,$idEmpresa){
    	$result     = Array();    	
    	try{ 
    		$sql = "SELECT R.ID_ELEMENTO, R.ORDEN, E.DESCIPCION AS N_ELEMENTO, E.ACTIVO,E.VALORES_CONFIG, E.REQUERIDO, T.`DESCRIPCION` AS TIPO, E.`DEPENDE`, E.`ESPERA`, E.`VALIDAR_LOCAL`,
    				E.ID_TIPO
					FROM PROD_FORMULARIO_ELEMENTOS R
					INNER JOIN PROD_ELEMENTOS 	   E ON R.ID_ELEMENTO = E.ID_ELEMENTO
					INNER JOIN PROD_TPO_ELEMENTO   T ON E.`ID_TIPO`   = T.ID_TIPO
					WHERE ID_FORMULARIO = $idObject
					  AND ID_EMPRESA    = $idEmpresa
					ORDER BY R.ORDEN ASC";
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
    
	/**
	 * 
	 * Inserta un nuevo elemento y lo relaciona con un formulario
	 * @param Array $aDataIn
	 * @return Array Id, Estatus de la operacion.
	 */
    public function insertElement($aDataElement,$idObject,$idEmpresa){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO PROD_ELEMENTOS
				SET ID_TIPO			= ".$aDataElement['tipo'].",
					DESCIPCION		='".@$aDataElement['desc']."',
					ACTIVO			='".@$aDataElement['status']."',
					VALORES_CONFIG	='".@$aDataElement['options']."',
					REQUERIDO		='".@$aDataElement['requerido']."',
					VALIDAR_LOCAL	='".@$aDataElement['validacion']."',
					DEPENDE			= ".(($aDataElement['depend']=="") ? 'NULL': $aDataElement['depend']).",
					ESPERA 			='".$aDataElement['when']."'";
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$sqlRel = "INSERT INTO PROD_FORMULARIO_ELEMENTOS
							SET ID_FORMULARIO	= ".$idObject.",
								ID_EMPRESA		= ".$idEmpresa.",
								ID_ELEMENTO		= ".$query_id[0]['ID_LAST'].",
								ORDEN			= ".$aDataElement['orden'];
				$queryRel   = $this->query($sqlRel,false);
				if(count($queryRel)>0){
					$result['status']  = true;		
				}
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    }   

    /**
     * 
     * Elimina un elemento de un formulario
     * @param Array $aDataElement
     * @param int $idObject
     */
    public function deleteRowRel($aDataElement,$idObject){
        $result     = Array();
        $result['status']  = false;  
        
		$sqlDel  = "DELETE FROM PROD_FORMULARIO_ELEMENTOS 
					WHERE ID_ELEMENTO   = ".$aDataElement['id']."
					  AND ID_FORMULARIO = ".$idObject." LIMIT 1";
	    $queryDel   = $this->query($sqlDel,false);    

        $sql="DELETE FROM  PROD_ELEMENTOS
					 WHERE ID_ELEMENTO = ".$aDataElement['id']." LIMIT 1";
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

	/**
	 * 
	 * Actualiza un elemento del formulario
	 * @param Array $aDataIn
	 * @return Boolean Estatus de la operacion
	 */
    public function updateRowRel($aDataElement){
        $result     = Array();
        $result['status']  = false;
        
       $sql="UPDATE PROD_ELEMENTOS
				SET ID_TIPO			= ".$aDataElement['tipo'].",
					DESCIPCION		='".@$aDataElement['desc']."',
					ACTIVO			='".@$aDataElement['status']."',
					VALORES_CONFIG	='".@$aDataElement['options']."',
					REQUERIDO		='".@$aDataElement['requerido']."',
					VALIDAR_LOCAL	='".@$aDataElement['validacion']."',
					DEPENDE			= ".(($aDataElement['depend']=="") ? 'NULL': $aDataElement['depend']).",
					ESPERA 			='".$aDataElement['when']."'
			WHERE ID_ELEMENTO = ".$aDataElement['id']." LIMIT 1";				        
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