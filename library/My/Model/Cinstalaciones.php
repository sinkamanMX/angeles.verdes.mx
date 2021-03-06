<?php
/**
 * Archivo de definición de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Cinstalaciones extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'SUCURSALES';
	protected $_primary = 'ID_SUCURSAL';
		
	public function getAll($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT * 
    			FROM $this->_name ";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getDataFilter($idObject,$iTypeuser=0){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$sFilter = ($iTypeuser==0) ? " ID_SUCURSAL = $idObject" : " ID_EMPRESA = $idObject" ;	 		
    	$sql ="SELECT *     			
    			FROM $this->_name 
    			WHERE $sFilter";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	
	public function getCbo($idObject,$iTypeuser=0){
		$result= Array();
		$this->query("SET NAMES utf8",false); 	
		$sFilter = ($iTypeuser==0) ? " ID_SUCURSAL = $idObject" : " ID_EMPRESA = $idObject" ;	
    	$sql ="SELECT $this->_primary AS ID, DESCRIPCION AS NAME 
    			FROM $this->_name 
    			WHERE $sFilter
    			ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getCentroFromEdo($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT GROUP_CONCAT(ID_SUCURSAL SEPARATOR ',') AS SUCURSALES
				FROM SUCURSALES_COBERTURA
				WHERE ID_ESTADO = $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}
        
		return $result;		
	}
	
	public function getList($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT GROUP_CONCAT(DISTINCT ID_SUCURSAL ORDER BY ID_SUCURSAL) AS LIST_SUCURSALES
				FROM SUCURSALES 
				WHERE ID_EMPRESA = $idEmpresa";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0]['LIST_SUCURSALES'];			
		}
        
		return $result;				
		
		
	}
}