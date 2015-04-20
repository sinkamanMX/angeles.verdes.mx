<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Formularios extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_FORMULARIO';
	protected $_primary = 'ID_FORMULARIO';
	
	public function getDataTables($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
		$sql ="SELECT * 
				FROM $this->_name
				WHERE ID_EMPRESA = $idEmpresa 
				ORDER BY TITULO ASC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	 
	public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT * 
				FROM $this->_name
				WHERE $this->_primary = $idObject 
				LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}	

    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name
				SET ID_EMPRESA		=   ".$data['inputEmpresa'].",
					TITULO			=  '".$data['inputTitulo']."',
					DESCRIPCION		=  '".$data['inputDesc']."',
					ACTIVO			=  '".$data['inputEstatus']."',
					ID_USUARIO_CREO =   ".$data['inputUser'].",
					FECHA_CREACION 	=  CURRENT_TIMESTAMP";
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
        
        $sql="UPDATE $this->_name
				SET ID_EMPRESA		=   ".$data['inputEmpresa'].",
					TITULO			=  '".$data['inputTitulo']."',
					DESCRIPCION		=  '".$data['inputDesc']."',
					ACTIVO			=  '".$data['inputEstatus']."',
					ID_USUARIO_MODIFICO =   ".$data['inputUser'].",
					FECHA_MODIFICACION 	=  CURRENT_TIMESTAMP 
				WHERE $this->_primary   = ".$data['catId']." LIMIT 1";        
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
        $result     = Array();
        $result['status']  = false;  
        
		$sqlDel  = "DELETE FROM PROD_FORMULARIO_ELEMENTOS WHERE ID_FORMULARIO = ".$data['catId']."  LIMIT 1";
	    $queryDel   = $this->query($sqlDel,false);    

		/*$sqlDel2  = "DELETE FROM PROD_EVENTO_TELEFONO WHERE ID_TELEFONO = ".$data['catId']."  LIMIT 1";
	    $queryDel2   = $this->query($sqlDel2,false);   */	    

        $sql="DELETE FROM  $this->_name
					 WHERE $this->_primary = ".$data['catId']."  LIMIT 1";
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

    public function getFormElements($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
		$sql ="SELECT E.ID_ELEMENTO, E.DESCIPCION AS ELEMENTO, E.VALORES_CONFIG, T.DESCRIPCION AS TIPO, 
				R.ORDEN,R.ID_FORM_ELEMENTO AS ID_REL,E.REQUERIDO , E.ID_ELEMENTO AS ID
				FROM PROD_ELEMENTOS E
				INNER JOIN PROD_TPO_ELEMENTO         T ON E.ID_TIPO     = T.ID_TIPO
				INNER JOIN PROD_FORMULARIO_ELEMENTOS R ON E.ID_ELEMENTO = R.ID_ELEMENTO
				WHERE ID_FORMULARIO = $idObject
				ORDER BY ORDEN ASC";  		  
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	

		return $result;	    	
    }
    
    public function getTypeElements(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
		$sql ="SELECT CONCAT(ID_TIPO,'|',REQ_OPCIONES) AS ID, DESCRIPCION AS NAME
				FROM PROD_TPO_ELEMENTO
				ORDER BY DESCRIPCION ASC;";	  		  
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	

		return $result;	    	
    }   

	public function getDataElement($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT E.*, CONCAT(T.ID_TIPO,'|',T.REQ_OPCIONES) AS ID ,T.*
				FROM PROD_ELEMENTOS E
				INNER JOIN PROD_TPO_ELEMENTO T ON E.ID_TIPO     = T.ID_TIPO
				WHERE E.ID_ELEMENTO = $idObject 
				LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}

    public function insertElement($data){
        $result     = Array();
        $result['status']  = false;   
        $tipo   = explode("|", $data['inputTipo']);   
        $sql="INSERT INTO PROD_ELEMENTOS
				SET ID_TIPO				=   ".$tipo[0].",
					DESCIPCION			=  '".$data['inputDesc']."',
					VALORES_CONFIG		=  '".$data['inputValores']."',
					ACTIVO				=  1,
					REQUERIDO 			=  '".$data['inputRequerido']."' ";
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
    
    public function updateElement($data){    	
        $result     = Array();
        $result['status']  = false;
        $tipo   = explode("|", $data['inputTipo']);   
        $sql="UPDATE PROD_ELEMENTOS
				SET ID_TIPO				=   ".$tipo[0].",
					DESCIPCION			=  '".$data['inputDesc']."',
					VALORES_CONFIG		=  '".$data['inputValores']."',
					ACTIVO				=  1,
					REQUERIDO 			=  '".$data['inputRequerido']."' 
				WHERE ID_ELEMENTO       = ".$data['dataElement']." LIMIT 1";   
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
    
    public function getLastElement($idObject){
		$result=-1;
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT (COUNT(ORDEN)+1) AS NEXT
				FROM PROD_FORMULARIO_ELEMENTOS
				WHERE ID_FORMULARIO = $idObject
				ORDER BY ORDEN DESC
				LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0]['NEXT'];			
		}	
        
		return $result;	    	
    }
    
    public function insertRelElement($data){
        $result     		= Array();
        $result['status']  	= false;
        $sOrden	= $this->getLastElement($data['catId']);        
        $sql="INSERT INTO PROD_FORMULARIO_ELEMENTOS
				SET ID_FORMULARIO	=  ".$data['catId'].",
					ID_EMPRESA		=  ".$data['inputEmpresa'].",
					ID_ELEMENTO		=  ".$data['dataElement'].",
					ORDEN			=   ".$sOrden;
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