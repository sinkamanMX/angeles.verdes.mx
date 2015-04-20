<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Extras extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_EXTRAS_EMPRESA';
	protected $_primary = 'ID_EXTRA';
		
	public function getAll($idEmpresa){
		try{
			$result= Array();
			$this->query("SET NAMES utf8",false); 	
				
	    	$sql ="SELECT *
	    			FROM $this->_name 
	    			WHERE ID_EMPRESA = $idEmpresa ORDER BY $this->_primary ASC";
			$query   = $this->query($sql);
			if(count($query)>0){		  
				$result = $query;
			}
		}catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
        
		return $result;			
	}
}