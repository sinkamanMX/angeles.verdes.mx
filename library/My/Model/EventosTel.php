<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_EventosTel extends My_Db_Table
{
    //protected $_schema 	= 'PRODUCTIVIDAPP';
	protected $_name 	= 'PROD_EVENTOS';
	protected $_primary = 'ID_EVENTO';	
	
	public function getDataTables($idObject,$idModel){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT E.DESCRIPCION_EVENTO AS NAME, E.ID_EVENTO AS ID, IF(T.ID_EVENTO_TELEFONO IS NULL ,'0' ,'1') AS ASIGNADO
			FROM PROD_EVENTOS E
			 LEFT JOIN PROD_EVENTO_TELEFONO T ON E.ID_EVENTO = T.ID_EVENTO AND T.ID_TELEFONO =  $idObject
			ORDER BY ASIGNADO DESC,E.DESCRIPCION_EVENTO ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}   		
}	
