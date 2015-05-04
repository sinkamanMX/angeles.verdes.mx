<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_EstatusSolicitud extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_ESTATUS_SOLICITUD';
	protected $_primary = 'ID_ESTATUS';
		
	public function getCbo($idStatus=-1){
		try{
			$sFilter = ($idStatus==-1) ? ' ': ' WHERE ID_ESTATUS NOT IN ('.$idStatus.')';
			$result= Array();
			$this->query("SET NAMES utf8",false); 		
	    	$sql ="SELECT $this->_primary AS ID, DESCRIPCION AS NAME 
	    			FROM $this->_name $sFilter ORDER BY NAME ASC";
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