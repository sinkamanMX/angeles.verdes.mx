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
class My_Model_Contactos extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_QR_CONTACTOS';
	protected $_primary = 'ID_CONTACTO_QR';
	
	public function validateUser($datauser){
		$result= Array();		
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT $this->_primary
	    		FROM $this->_name 
				WHERE EMAIL    = '".$datauser['usuario']."'
                 AND  PASSWORD = SHA1('".$datauser['contrasena']."')
                 AND  ID_USUARIO_SISTEMA = 1";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];
		}
        
		return $result;			
	} 
	
    public function getDataUser($idObject){
      	$this->query("SET NAMES utf8",false); 
        
		$result= Array();
    	$sql ="SELECT 6 AS ID_PERFIL,  O.*,Q.*,C.*, O.EMAIL AS S_MAIL
				FROM PROD_QR_CONTACTOS O
				INNER JOIN PROD_CLIENTES_QR Q ON O.ID_QR = Q.ID_QR
				INNER JOIN PROD_CLIENTES    C ON Q.COD_CLIENTE = C.COD_CLIENTE
				WHERE O.ID_CONTACTO_QR = $idObject";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}	
        
		return $result;	        
    } 
     
    public function setLastAccess($datauser){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
				SET  ULTIMO_ACCESO = CURRENT_TIMESTAMP			 
					 WHERE $this->_primary =".$datauser['ID_CONTACTO_QR']." LIMIT 1";
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

    public function getCbo($idObject){
      	$this->query("SET NAMES utf8",false); 
        
		$result= Array();
    	$sql ="SELECT ID_CONTACTO_QR AS ID, CONCAT(NOMBRE,' ',APELLIDOS) AS NAME
				FROM PROD_QR_CONTACTOS 
				WHERE COD_CLIENTE = '$idObject'";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query;			
		}	
        
		return $result;	        
    }     
}