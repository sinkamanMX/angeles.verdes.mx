<?php
/**
 * Archivo de definici—n de Configuracion
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Configuracion extends My_Db_Table
{
    protected $_schema 	= '';
	protected $_name 	= 'PROD_CITAS';
	protected $_primary = '';

    public function updateConf($data){
       $result     = Array();
        $result['status']  = false;

        $sql="UPDATE EMPRESAS
				SET NOMBRE				=  '".$data['txtNameCompany']."',
					RAZON_SOCIAL		=  '".$data['txtNameRazon']."',
					DIRECCION			=  '".$data['txtNameDir']."',
					NOMBRE_RESPONSABLE	=  '".$data['txtNameResp']."',
					TELEFONO			=  '".$data['txtNameTel']."',
					EMAIL				=  '".$data['txtNameEMail']."'
				WHERE ID_EMPRESA        =   ".$data['idEmpresa']." LIMIT 1";        
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
    
    public function updatePhoneConf($data){
       $result     = Array();
        $result['status']  = false;
        
        $sql="UPDATE EMP_CONFIGURACION
				SET TIEMPO_REPORTE				=   ".$data['txtTimeReporte'].",
					TIEMPO_ENCENDIDO			=   ".$data['txtTimeEncendido'].",
					TIEMPO_APAGADO				=   ".$data['txtTimeApagado'].",
					TIEMPO_SIN_REPORTAR			=   ".$data['txtTimeSinRep'].",
					TITULO_TIEMPO_X_SIN_REPORTAR=  '".$data['txtTituloReporteX']."',
					TIEMPO_X_SIN_REPORTAR		=   ".$data['txtTimeReporteX'].",
					LOCALIZACION 				=   ".$data['cboLocalizar']."
				WHERE ID_EMPRESA        		=   ".$data['idEmpresa']." LIMIT 1";  
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
    
    public function modulesOff($idEmpresa){
       $result     = Array();
        $result['status']  = false;
        
        $sql="UPDATE EMPRESAS_MODULOS
				SET VISIBLE = 0
				WHERE ID_EMPRESA =   ".$idEmpresa;   
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

    public function updateModulos($data){
       $result     = Array();
        $result['status']  = false;
        
        $sql="UPDATE EMPRESAS_MODULOS
				SET VISIBLE 	 = 1
				WHERE ID_EMPRESA = ".$data['idEmpresa']."
				  AND ID_MODULO  = ".$data['idModulo']. " LIMIT 1";
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