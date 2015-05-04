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
				WHERE U.USUARIO  = '".$datauser['usuario']."'
                 AND  U.PASSWORD = SHA1('".$datauser['contrasena']."')
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
				INNER JOIN USR_EMPRESA UE ON U.ID_USUARIO    = UE.ID_USUARIO
				INNER JOIN SUCURSALES  S  ON UE.ID_SUCURSAL  = S.ID_SUCURSAL
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

	
	public function getDataTables($dataUser){
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
						U.ACTIVO,
						IF(A.ID_TELEFONO IS NULL,'Sin Asignar', IF(T.ID_TELEFONO IS NULL,'Sin Asignar',CONCAT(T.IDENTIFICADOR))) AS N_IMEI
				FROM USUARIOS U
				INNER JOIN PERFILES    P ON P.ID_PERFIL  = U.ID_PERFIL
				INNER JOIN USR_EMPRESA R ON U.ID_USUARIO = R.ID_USUARIO
				INNER JOIN SUCURSALES  S ON R.ID_SUCURSAL = S.ID_SUCURSAL
				 LEFT JOIN PROD_USR_TELEFONO    A ON U.ID_USUARIO = A.ID_USUARIO				
				 LEFT JOIN PROD_TELEFONOS		T ON A.ID_TELEFONO= T.ID_TELEFONO
				WHERE S.ID_EMPRESA = ".$dataUser['ID_EMPRESA']."
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
    	$sql ="SELECT  U.*,P.*,S.*
				FROM USUARIOS U
				INNER JOIN PERFILES    P ON P.ID_PERFIL  = U.ID_PERFIL
				INNER JOIN USR_EMPRESA R ON U.ID_USUARIO = R.ID_USUARIO
				INNER JOIN SUCURSALES  S ON R.ID_SUCURSAL = S.ID_SUCURSAL
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
        
        $sFilter = '';
        if($data['inputIdSap']!=""){
        	$sFilter = " ID_SAP	= ".$data['inputIdSap'].",";
        }  

        if($data['inputIdAlm']!=""){
        	$sFilter .= " CVE_SAP_ALMACEN	= '".$data['inputIdAlm']."',";
        }          
        
        $sql="INSERT INTO $this->_name	
        			SET ID_PERFIL	=   ".$data['inputPerfil'].",
						USUARIO		=  '".$data['inputUsuario']."',
						PASSWORD	=  SHA1('".$data['inputPassword']."'),
						$sFilter
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
				$data['catId'] = $query_id[0]['ID_LAST'];  			 	
				$insertRel = $this->setSucursal($data);
				if($insertRel){
					$result['id']	   = $query_id[0]['ID_LAST'];
					$result['status']  = true;	
				}					
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
        
        $sFilter = '';
        if($data['inputIdSap']!=""){
        	$sFilter = " ID_SAP	= ".$data['inputIdSap'].",";
        }  

        if($data['inputIdAlm']!=""){
        	$sFilter .= " CVE_SAP_ALMACEN	= '".$data['inputIdAlm']."',";
        }        

        $sql="UPDATE $this->_name	
        			SET ID_PERFIL	=   ".$data['inputPerfil'].",
						USUARIO		=  '".$data['inputUsuario']."',
						$sPassword			
						$sFilter
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
				$insertRel = $this->setSucursal($data);
				if($insertRel){
					$result['status']  = true;	
				}									
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;
    }  

    public function setSucursal($data){
		$result = false;    	
    	try{    	
			$sql  	= "DELETE FROM USR_EMPRESA WHERE ID_USUARIO = ".$data['catId']." LIMIT 1";
    		$query   = $this->query($sql,false);
			if($query){
        		$sqlInsert="INSERT INTO USR_EMPRESA
        			SET ID_SUCURSAL	=  ".$data['inputSucursal'].",
						ID_USUARIO	=  ".$data['catId'];
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
    
    public function setIdSap($data){
        $result  = false;

        $sql="UPDATE $this->_name	
        			SET ID_SAP	=   ".$data['idSAP']."
			WHERE $this->_primary =".$data['catId']." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result  = true;										
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;
    }    

    public function setIdAlmacen($data){
        $result  = false;

        $sql="UPDATE $this->_name	
        			SET CVE_SAP_ALMACEN	=   '".$data['idAlmacen']."'
			WHERE $this->_primary =".$data['catId']." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result  = true;										
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;
    }   

	public function getNoAsIdSap($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT *
					FROM SAP_UDA_USUARIOS 
					WHERE ID_SAP NOT IN
					(
						SELECT U.ID_SAP
						 FROM PROD_USR_TELEFONO T
						 INNER JOIN USUARIOS    U ON T.ID_USUARIO  = U.ID_USUARIO
						 INNER JOIN USR_EMPRESA E ON U.ID_USUARIO  = E.ID_USUARIO
						 INNER JOIN SUCURSALES  L ON E.ID_SUCURSAL = L.ID_SUCURSAL
						 INNER JOIN EMPRESAS    S ON L.ID_EMPRESA  = S.ID_EMPRESA
						WHERE S.ID_EMPRESA = $idEmpresa
						 AND U.ID_SAP IS NOT NULL
					)
					 ORDER BY NOMBRE ASC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;		
	}    
	
	public function getNoAsAlm($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT *
					FROM SAP_UDA_ALMACEN_CLAVES 
					WHERE CVE_ALMACEN NOT IN
					(
						SELECT U.CVE_SAP_ALMACEN
						 FROM PROD_USR_TELEFONO T
						 INNER JOIN USUARIOS    U ON T.ID_USUARIO  = U.ID_USUARIO
						 INNER JOIN USR_EMPRESA E ON U.ID_USUARIO  = E.ID_USUARIO
						 INNER JOIN SUCURSALES  L ON E.ID_SUCURSAL = L.ID_SUCURSAL
						 INNER JOIN EMPRESAS    S ON L.ID_EMPRESA  = S.ID_EMPRESA
						WHERE S.ID_EMPRESA = $idEmpresa
						 AND U.CVE_SAP_ALMACEN IS NOT NULL
					)	
					 ORDER BY CVE_ALMACEN ASC";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;		
	}  	
}