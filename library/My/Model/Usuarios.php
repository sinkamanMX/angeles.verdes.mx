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
class My_Model_Usuarios extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'USUARIOS';
	protected $_primary = 'ID_USUARIO';
	
	public function validateUser($datauser){
		$result= Array();		
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT $this->_primary
	    		FROM USUARIOS U
				WHERE U.USUARIO  = '".$datauser['inputUsuario']."'
                 AND  U.PASSWORD = SHA1('".$datauser['inputPassword']."')
                 AND  U.ACTIVO = 1";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];
		}
        
		return $result;			
	} 
    
    public function getDataUser($idObject){
      	$this->query("SET NAMES utf8",false); 
        
		$result= Array();
    	$sql ="SELECT U.* ,P.*, S.*, E.* ,E.NOMBRE AS N_EMPRESA,S.DESCRIPCION AS N_SUCURSAL
				FROM USUARIOS U
				INNER JOIN PERFILES    P  ON U.ID_PERFIL     = P.ID_PERFIL
				INNER JOIN SUCURSALES  S  ON U.ID_SUCURSAL   = S.ID_SUCURSAL
				INNER JOIN EMPRESAS    E  ON S.ID_EMPRESA    = E.ID_EMPRESA
                WHERE U.ID_USUARIO = $idObject";			         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}	
        
		return $result;	        
    }  
    
    public function validatePassword($datauser){
		$result= Array();		
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT $this->_primary
	    		FROM USUARIOS U
				WHERE U.PASSWORD   = SHA1('".$datauser['VPASSWORD']."')
				  AND U.ID_USUARIO = ".$datauser['ID_USUARIO'];
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];
		}
        
		return $result;		    	
    }
    
    public function changePass($datauser){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
				SET  PASSWORD 	=  SHA1('".$datauser['NPASSWORD']."')					 
					 WHERE $this->_primary =".$datauser['ID_USUARIO']." LIMIT 1";
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
    
	
	public function getCbOperadores(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, CONCAT(NOMBRE,' ',APELLIDOS) AS NAME 
    			FROM $this->_name 
    			WHERE FLAG_OPERACIONES = 1 ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}	
	
    public function setLastAccess($datauser){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
				SET  ULTIMO_ACCESO = CURRENT_TIMESTAMP			 
					 WHERE $this->_primary =".$datauser['ID_USUARIO']." LIMIT 1";
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

	
	public function getDataTables($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT   U.ID_USUARIO, 
						U.ID_PERFIL,
						P.DESCRIPCION AS PERFIL,
						U.USUARIO,
						CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NOMBRE,
						U.EMAIL,
						U.ULTIMO_ACCESO,
						U.FLAG_OPERACIONES,
						U.ACTIVO
				FROM USUARIOS U
				INNER JOIN PERFILES    P ON P.ID_PERFIL  = U.ID_PERFIL
				INNER JOIN SUCURSALES  S ON U.ID_SUCURSAL = S.ID_SUCURSAL
				WHERE S.ID_EMPRESA = ".$idEmpresa."
				ORDER BY NOMBRE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	        
		return $result;			
	} 

    public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  U.*,P.*,S.*, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS N_USER
				FROM USUARIOS U
				INNER JOIN PERFILES    P ON P.ID_PERFIL  = U.ID_PERFIL
				INNER JOIN SUCURSALES  S ON U.ID_SUCURSAL = S.ID_SUCURSAL
				WHERE U.$this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }	
    
    public function validateData($dataSearch,$idObject,$optionSearch){
		$result=true;		
		$this->query("SET NAMES utf8",false);
		$filter = ($optionSearch=='user') ? ' USUARIO = "'.$dataSearch.'"': ' IP = "'.$dataSearch.'"';
    	$sql ="SELECT $this->_primary
	    		FROM $this->_name
				WHERE ID_USUARIO <> $idObject
                 AND  $filter";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = false;
		}
        
		return $result;		    	
    } 

    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name	
        			SET ID_PERFIL	=   ".$data['inputPerfil'].",
        				ID_SUCURSAL =   ".$data['inputSucursal'].",
						USUARIO		=  '".$data['inputUsuario']."',
						PASSWORD	=  SHA1('".$data['inputPassword']."'),
						NOMBRE		=  '".$data['inputNombre']."',
						APELLIDOS	=  '".$data['inputApps']."',
						EMAIL		=  '".$data['inputEmail']."',
						TEL_MOVIL	=  '".$data['inputMovil']."',
						TEL_FIJO	=  '".$data['inputTelFijo']."',
						FLAG_OPERACIONES=  ".$data['inputOperaciones'].",
						ACTIVO		=  ".$data['inputEstatus'];
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
        
        $sPassword = '';
        if($data['inputPassword']!=""){
        	$sPassword = " PASSWORD	=  SHA1('".$data['inputPassword']."'),";
        }

        $sql="UPDATE $this->_name	
        			SET ID_PERFIL	=   ".$data['inputPerfil'].",
        				ID_SUCURSAL =   ".$data['inputSucursal'].",
						USUARIO		=  '".$data['inputUsuario']."',
						$sPassword			
						NOMBRE		=  '".$data['inputNombre']."',
						APELLIDOS	=  '".$data['inputApps']."',
						EMAIL		=  '".$data['inputEmail']."',
						TEL_MOVIL	=  '".$data['inputMovil']."',
						TEL_FIJO	=  '".$data['inputTelFijo']."',
						FLAG_OPERACIONES=  ".$data['inputOperaciones'].",
						ACTIVO		=  ".$data['inputEstatus']."
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
			$sql  	= "DELETE FROM USR_EMPRESA WHERE $this->_primary = ".$data['catId']." LIMIT 1";
    		$query   = $this->query($sql,false);
			if($query){
        		$sqlInsert="DELETE FROM $this->_name
        			WHERE $this->_primary = ".$data['catId']." LIMIT 1";
    			$queryInsert   = $this->query($sqlInsert,false);				
				if($queryInsert){
					$result = true;	
				}					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;    	
    }
    
	public function getUserOperation($idEmpresa,$idSucursal=-1){
		$result= Array();		
		$this->query("SET NAMES utf8",false);
		$sFilter = ($idSucursal>-1) ? " AND U.ID_SUCURSAL = $idSucursal" : "";  		
    	$sql ="SELECT   U.ID_USUARIO, 
						U.ID_PERFIL,
						P.DESCRIPCION AS PERFIL,
						U.USUARIO,
						CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NOMBRE,
						U.EMAIL,
						U.ULTIMO_ACCESO,
						U.FLAG_OPERACIONES,
						U.ACTIVO,
						T.IDENTIFICADOR,
						T.DESCRIPCION,
						T.TELEFONO
				FROM USUARIOS U
				INNER JOIN PERFILES    P ON P.ID_PERFIL  = U.ID_PERFIL
				INNER JOIN SUCURSALES  S ON U.ID_SUCURSAL = S.ID_SUCURSAL				
				 LEFT JOIN PROD_USR_TELEFONO R ON U.ID_USUARIO =  R.ID_USUARIO
				 LEFT JOIN PROD_TELEFONOS T ON T.ID_TELEFONO = R.ID_TELEFONO
				WHERE S.ID_EMPRESA = ".$idEmpresa."
					$sFilter
				ORDER BY NOMBRE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	        
		return $result;			
	}     
}