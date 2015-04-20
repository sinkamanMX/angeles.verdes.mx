<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Empresas extends My_Db_Table
{
    protected $_schema 	= 'PRODUCTIVIDAPP';
	protected $_name 	= 'EMPRESAS';
	protected $_primary = 'ID_EMPRESA';

	public function getDataInfo($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT *
				FROM  $this->_name				
				WHERE $this->_primary = $idEmpresa LIMIT 1";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];
		}
        
		return $result;			
	}    

	public function getConsumido($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT C.*,P.TOTAL_FOTOS AS D_FOTOS, P.`TOTAL_ALTA_ACTIVIDADES` AS D_ACTIVIDADES , P.`TOTAL_CAPTURA_ACTIVIDADES` AS D_CAPTURA, P.`TOTAL_FORMULARIOS` AS D_FORMULARIOS, P.`TITULO` AS N_PLAN
				FROM  EMPRESA_PLAN_CONSUMO C
				INNER JOIN ADMIN_PLANES P ON C.ID_PLAN = P.ID_PLAN
				WHERE C.ID_EMPRESA = $idEmpresa LIMIT 1";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}
        
		return $result;		
	}

	public function getConfiguracion($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT *
				FROM  EMP_CONFIGURACION
				WHERE $this->_primary = $idEmpresa LIMIT 1";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}
        
		return $result;		
	}	
	
	public function getModulos($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT E.*,N.DESCRIPCION AS MENU,M.DESCRIPCION AS MODULO
				FROM  EMPRESAS_MODULOS E
				INNER JOIN MODULOS M ON M.ID_MODULO = E.ID_MODULO
				INNER JOIN MENU    N ON N.ID_MENU   = M.ID_MENU
				WHERE E.$this->_primary = $idEmpresa";    
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;				
		}
        
		return $result;		
	}		
}