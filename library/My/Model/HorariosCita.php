<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_HorariosCita extends My_Db_Table
{
	protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'PROD_HORARIOS_CITA';
	protected $_primary = 'ID_HORARIO_CITA';
	
	public function getHorarios(){
 		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID,CONCAT(HORA_INICIO,'-',HORA_FIN ) AS NAME
			 	FROM $this->_name
			 	ORDER BY $this->_primary ASC ";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
		return $result;	
	}
	
}