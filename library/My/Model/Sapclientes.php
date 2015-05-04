<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Sapclientes extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'PROD_CLIENTES';
	protected $_primary = 'ID_CLIENTE';
	
	public function getDataTables(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT C.COD_CLIENTE, CONCAT(C.NOMBRE,' ',C.APELLIDOS) AS NAME, C.RAZON_SOCIAL, COUNT(Q.ID_QR) AS TOTAL_QR,
				COUNT(IF(Q.FECHA_ACTIVACION IS NOT NULL, Q.ID_QR, NULL)) AS ACTIVATE_QR,
				COUNT(IF(Q.FECHA_ACTIVACION IS NULL, Q.ID_QR, NULL)) AS INACTIVATE_QR
				FROM PROD_CLIENTES C
				LEFT JOIN PROD_CLIENTES_QR Q ON C.COD_CLIENTE = Q.COD_CLIENTE
				WHERE C.COD_CLIENTE IS NOT NULL AND C.COD_CLIENTE != ''
				GROUP BY C.ID_CLIENTE
				ORDER BY C.COD_CLIENTE";    	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;	
	}

	public function getDataTablesQr($codCliente){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT Q.*, CONCAT(C.NOMBRE,' ',C.APELLIDOS) AS N_CLIENTE
				FROM PROD_CLIENTES_QR Q 
				LEFT JOIN PROD_QR_CONTACTOS C ON Q.ID_QR = C.ID_QR
				WHERE Q.COD_CLIENTE =  '$codCliente'";    	
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
                WHERE COD_CLIENTE = '$idObject' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }
    
    public function getDataById($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT * 
    			FROM $this->_name
                WHERE $this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }	    
    
    public function getDataQr($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT * 
    			FROM PROD_CLIENTES_QR
                WHERE ID_QR = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }	    
    
    public function getTotalQrByClient($idObject){
    	$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT COUNT(ID_QR) AS TOTAL_QR
				FROM PROD_CLIENTES_QR
				WHERE COD_CLIENTE = '$idObject' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0]['TOTAL_QR'];			
		}	
        
		return $result;	 
    }
    
    public function insertRow($data){
        $result = false;
        
        $sql="INSERT INTO PROD_CLIENTES_QR	SET
			        COD_CLIENTE		=  '".$data['codCliente']."',
					FECHA_CREADO	=  CURRENT_TIMESTAMP,
					CADENA_QR		=  '".$data['CadenaQr']."'";
        try{            
    		$query   = $this->query($sql,false);
    		$result  = true;
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    } 

    public function getContactInfo($idObject,$codClient){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM PROD_QR_CONTACTOS 
				WHERE ID_QR 		= $idObject
				AND   COD_CLIENTE	= '$codClient' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }
    
    public function insertRowContact($data){
        $result = false;

        $sql="INSERT INTO PROD_QR_CONTACTOS SET
					ID_QR			=   ".$data['activationCode'].",
					COD_CLIENTE		=  '".$data['inputCodeClient']."',
					NOMBRE			=  '".$data['inputNombre']."',
					APELLIDOS		=  '".$data['inputAps']."',
					GENERO			=  '".$data['inputGenero']."',
					SUCURSAL		=  '".$data['inputSucursal']."',
					PUESTO			=  '".$data['inputPuesto']."',
					TEL_MOVIL		=  '".$data['inputTel']."',
					TEL_OFICINA		=  '".$data['inputTelOfna']."',
					EXTENSION		=  '".$data['inputExt']."',
					EMAIL			=  '".$data['inputEmail']."',
					RFC				=  '".$data['inputRFC']."',
					ID_USUARIO_SISTEMA= ".$data['inputAccessUser'].",
					PASSWORD		=  SHA1('".$data['inputPassword']."'), 
					PASSWORD_TEXT	=  '".$data['inputPassword']."',
					ACTIVO			= 1";
        try{            
    		$query   = $this->query($sql,false);
    		$result  = true;
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    }    

    public function updateActivation($idQr){
        $result = false;

        $sql="UPDATE PROD_CLIENTES_QR 
        		SET FECHA_ACTIVACION = CURRENT_TIMESTAMP 
        		WHERE ID_QR = ".$idQr;
        try{            
    		$query   = $this->query($sql,false);
    		$result  = true;
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    }      
}