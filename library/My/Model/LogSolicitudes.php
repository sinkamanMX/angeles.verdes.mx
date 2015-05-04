<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_LogSolicitudes extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_SOLICITUDES_LOG';
	protected $_primary = 'ID_LOG';
	
    public function insertRow($data){
        $result     	   = Array();
        $result['status']  = false;
                
        $sql="INSERT INTO $this->_name SET
					ID_SOLICITUD 	=  ".$data['idSolicitud'].",
  					ACTION			= '".$data['sAction']."', 
  					DESCRIPCION 	= '".$data['sDescripcion']."',
  					ORIGEN			= '".$data['sOrigen']."',
  					FECHA_CREADO 	= CURRENT_TIMESTAMP";        
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
    
    public function getDataTable($idSolicitud){
      	$this->query("SET NAMES utf8",false);         
		$result= Array();
    	$sql ="SELECT *
				FROM $this->_name				
				WHERE ID_SOLICITUD = $idSolicitud 
				ORDER BY FECHA_CREADO DESC";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query;			
		}	
        
		return $result;	        
    }    
	
}
