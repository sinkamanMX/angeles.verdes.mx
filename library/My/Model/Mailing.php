<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Mailing extends My_Db_Table
{
    protected $_schema 	= 'SIMA';
	protected $_name 	= 'SYS_MAILING';
	protected $_primary = 'ID_MAILING';
	
	public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
		$this->query("SET NAMES utf8",false);         
        $sql="INSERT INTO $this->_name
				SET ID_SOLICITUD			=  ".$data['inputIdSolicitud'].",
					NOMBRES_DESTINATARIOS	= '".$data['inputDestinatarios']."', 
					DESTINATARIOS			= '".$data['inputEmails']."',
					TITULO_MSG			 	= '".$data['inputTittle']."',
					CUERPO_MSG				= '".$data['inputBody']."',
					REMITENTE_NOMBRE		= '".$data['inputFromName']."',
					REMITENTE_EMAIL			= '".$data['inputFromEmail']."',
					LIVE_NOTIFICATION		=  ".$data['inputLiveNotif'].",
					FECHA_CREADO			= CURRENT_TIMESTAMP,
					ESTATUS 				= 0";
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
	
	public function getNotifications(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT C.COD_CLIENTE, M.ID_SOLICITUD AS ID,M.TITULO_MSG, M.FECHA_CREADO, M.ID_MAILING
				FROM SYS_MAILING M
				INNER JOIN PROD_CITAS_SOLICITUD S ON M.ID_SOLICITUD = S.ID_SOLICITUD
				INNER JOIN PROD_CLIENTES        C ON S.ID_CLIENTE   = C.ID_CLIENTE
				WHERE LIVE_NOTIFICATION = 1
				  AND LEIDO      = 0
				  ORDER BY M.FECHA_CREADO DESC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}
        
		return $result;			
	}
	
    public function readNotification($data){
       $result     = Array();
        $result['status']  = false;

        $sql="UPDATE SYS_MAILING			 
				  SET LEIDO    	   = 1			
				WHERE ID_MAILING = ".$data['strInput']." LIMIT 1";
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
}