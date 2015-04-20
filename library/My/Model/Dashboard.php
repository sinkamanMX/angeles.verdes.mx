<?php
/**
 * Archivo de definición de usuarios
 * 
 * @author EPENA
 * @package library.My.Models
 */

/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Dashboard extends My_Db_Table
{
    protected $_schema 	= 'PRODUCTIVIDAPP';
	protected $_name 	= 'EMP_CONFIGURACION';
	protected $_primary = 'ID_PARAMETRO';
	
	public function dibuja($idEmpresa){
		$result= Array();		
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT * FROM EMP_CONFIGURACION
			   WHERE ID_EMPRESA = ".$idEmpresa;
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;				
	} 
    
   
}