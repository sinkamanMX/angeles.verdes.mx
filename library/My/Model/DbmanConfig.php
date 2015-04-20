<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_DbmanConfig extends My_Db_Table
{
    public $_schema 	= 'SIMA';
	public $_name 		= 'DB_MODULOS';
	public $_primary 	= 'ID_DB_MODULO';
	
	public function getData($keyModule){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT *
				FROM DB_MODULOS
				WHERE CLAVE_MODULO = '$keyModule'";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}        
		return $result;			
	}	
	
    public function deleteRow($data){
		$result = false;    	
    	try{    	
			$sql  	= "DELETE FROM $this->_name	 WHERE $this->_primary = ".$data['catId']." LIMIT 1";
    		$query   = $this->query($sql,false);
			if($query){
				$result = true;				
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;    	
    } 	
    
	public function getFieldsForm($idModule){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT C.*, V.DESCRIPCION AS V_DESCRIPCION, V.OPCIONES
				FROM DB_MODULOS_CAMPOS C
				LEFT JOIN DB_VALIDACIONES  V ON V.ID_VALIDACION = C.ID_VALIDACION
				WHERE C.ID_DB_MODULO = $idModule";  
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}        
		return $result;			
	}   
}