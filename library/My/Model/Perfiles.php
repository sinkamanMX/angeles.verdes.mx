<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Perfiles extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'PERFILES';
	protected $_primary = 'ID_PERFIL';
	
	public function getCbo($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, DESCRIPCION AS NAME 
    			FROM $this->_name 
    			WHERE ID_EMPRESA = $idEmpresa
    			ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}		
	
	public function getModuleDefault($idProfile){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM MODULOS_PERFIL MP
				INNER JOIN MODULOS M ON MP.ID_MODULO = M.ID_MODULO
				WHERE MP.ID_PERFIL = $idProfile
				 AND  MP.INICIO    = 1 AND M.ACTIVO = 1 LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	  		
	}   

	public function getModules($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT M.*, M.DESCRIPCION AS M_DESCRIPCION,N.DESCRIPCION AS N_DESCRIPCION,N.ID_MENU AS IDMENU, N.*, M.SCRIPT AS S_MODULE, M.ICONO AS M_ICONO
				FROM MODULOS_PERFIL MP
				INNER JOIN MODULOS M ON MP.ID_MODULO = M.ID_MODULO
				INNER JOIN MENU    N ON M.ID_MENU    = N.ID_MENU 
				WHERE MP.ID_PERFIL = ".$idObject." AND M.ACTIVO = 1
				ORDER BY N_DESCRIPCION ASC, M.DESCRIPCION ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getDataModule($classObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT MODULOS.*, MENU.DESCRIPCION AS N_MENU
				FROM  MODULOS
				INNER JOIN MENU ON MODULOS.ID_MENU = MENU.ID_MENU
				WHERE MODULOS.CLASE = '".$classObject."' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	 		
	}
	
	public function getDataMenu($classObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM  MENU
				WHERE CLASE = '".$classObject."' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	 		
	}	
	
	public function getDataTables($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT   ID_PERFIL,
						DESCRIPCION						
				FROM PERFILES
				WHERE ID_EMPRESA = $idEmpresa
				ORDER BY DESCRIPCION ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	        
		return $result;			
	} 	
	

    public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  *
				FROM PERFILES
				WHERE $this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }	
    	
    
    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;

        $agregar  = (isset($data['inputAgregar']) && $data['inputAgregar']=='on') ? "1": "0";
        $editar   = (isset($data['inputEditar'])  && $data['inputEditar']=='on')  ? "1": "0";
        $borrar	  = (isset($data['inputBorrar'])  && $data['inputBorrar']=='on')  ? "1": "0";
        $lectura  = (isset($data['inputLeer'])    && $data['inputLeer']=='on')    ? "1": "0";

        $sql="INSERT INTO $this->_name	
        			SET DESCRIPCION	= '".$data['inputDescripcion']."',
        			    ID_EMPRESA  = ".$data['idEmpresa'].",
						ACTIVO		= ".$data['inputEstatus'].",
						INSERTAR 	= ".$agregar.",
						EDITAR 		= ".$editar.",
						ELIMINAR 	= ".$borrar.",
						LECTURA  	= ".$lectura;
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
    
    public function updateRow($data){
       	$result     = Array();
        $result['status']  = false;

        $agregar  = (isset($data['inputAgregar']) && $data['inputAgregar']=='on') ? "1": "0";
        $editar   = (isset($data['inputEditar'])  && $data['inputEditar']=='on')  ? "1": "0";
        $borrar	  = (isset($data['inputBorrar'])  && $data['inputBorrar']=='on')  ? "1": "0";
        $lectura  = (isset($data['inputLeer'])    && $data['inputLeer']=='on')    ? "1": "0";
        
        $sql="UPDATE $this->_name	
        			SET DESCRIPCION	= '".$data['inputDescripcion']."',
						ACTIVO		= ".$data['inputEstatus'].",
						INSERTAR 	= ".$agregar.",
						EDITAR 		= ".$editar.",
						ELIMINAR 	= ".$borrar.",
						LECTURA  	= ".$lectura."
				WHERE $this->_primary =".$data['catId']." LIMIT 1";
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

	public function getModulesByProfile($idObject,$idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT E.ID_MODULO AS ID, N.DESCRIPCION AS MENU, M.DESCRIPCION AS N_MODULE, IF(P.ID_MODULO IS NULL ,'0' ,'1') AS ASIGNADO, INICIO
				FROM EMPRESAS_MODULOS E
				INNER JOIN MODULOS M ON E.ID_MODULO  = M.ID_MODULO
				INNER JOIN MENU    N ON M.ID_MENU 	 = N.ID_MENU
				 LEFT JOIN MODULOS_PERFIL P ON E.ID_MODULO = P.ID_MODULO AND P.ID_PERFIL = $idObject
				WHERE E.ID_EMPRESA = $idEmpresa
				ORDER BY ASIGNADO DESC, MENU ASC, N_MODULE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}   
	
	public function setAllEventos($idObject,$idEmpresa){
        $result     = Array();
        $result['status']  = false;        
		$sql = "INSERT INTO MODULOS_PERFIL(ID_PERFIL,ID_MODULO)
				(
				SELECT $idObject, ID_MODULO
				FROM EMPRESAS_MODULOS
				WHERE ID_EMPRESA = $idEmpresa 
				)";      
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

	public function deleteRelEvent($idObject){
        $result     = Array();
        $result['status']  = false;

        $sql="DELETE FROM  MODULOS_PERFIL
					 WHERE ID_PERFIL = $idObject";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result['status'];	 		
	}  	
	
	public function setRelEventos($data){
        $result     = Array();
        $result['status']  = false;
        $sql="INSERT INTO MODULOS_PERFIL		 
					SET ID_PERFIL 	=  ".$data['catId'].",
						ID_MODULO	=  ".$data['inputEvento'];
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
	
	public function setInitModule($data){
        $result     = Array();
        $result['status']  = false;
        $sql="UPDATE MODULOS_PERFIL
        			SET INICIO 	=  1		 
			  WHERE ID_PERFIL 	=  ".$data['catId']."
			 	AND ID_MODULO	=  ".$data['bRadioInit'];
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
}