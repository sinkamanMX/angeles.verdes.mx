<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Resume extends My_Db_Table{
    protected $_schema 	= '';
	protected $_name 	= '';
	protected $_primary = '';	
	public    $aResumeGral = Array();
	
	public function getResultsServices($idSucursal,$sFechaIn,$sFechaFin){
		$result= Array();
		$this->query("SET NAMES utf8",false);		
		$filter = ($idSucursal>-1) ? 'AND U.ID_SUCURSAL = '.$idSucursal : '';		
		
    	$sql ="SELECT R.ID_RESULTADO,S.ID_SUCURSAL, S.DESCRIPCION , GROUP_CONCAT(R.ID_RESULTADO SEPARATOR ',') AS RESULTADOS
				FROM PROD_FORM_RESULTADO R
				INNER JOIN USUARIOS U ON R.ID_USUARIO_CONTESTO = U.ID_USUARIO
				INNER JOIN SUCURSALES S ON U.ID_SUCURSAL = S.ID_SUCURSAL
				WHERE R.FECHA_CAPTURA_EQUIPO BETWEEN $sFechaIn AND $sFechaFin 
				  AND R.ID_FORMULARIO = 2
				 	".$filter."
				 GROUP BY U.ID_SUCURSAL ";  
		$query   = $this->query($sql);
		if(count($query)>0){	
			$queryResult = $query;
			$elements    = Array();	
			
			foreach($queryResult as $key => $items){
				$sIntResult = $items;
				$sIntResult['aServices'] = $this->getElementsByResult($items['RESULTADOS']);
				$result[] 				 = $sIntResult;
			}
			
			
 		}
		return $result;			
	}
	
	public function getElementsByResult($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false);			
		
    	$sql ="SELECT *
				FROM PROD_FORM_DETALLE_RESULTADO D
				INNER JOIN PROD_ELEMENTOS E ON D.ID_ELEMENTO = E.ID_ELEMENTO
				WHERE ID_RESULTADO IN( 
					".$idObject."
				) AND D.ID_ELEMENTO IN (29,13,32,54,34)
				ORDER BY D.ID_ELEMENTO";  
		$query   = $this->query($sql);
		if(count($query)>0){			
			$result = $query;
						
		}
		return $this->processServices($result);					
	}
	
	public function processServices($aDataResult){
		$aResult = Array();

		foreach($aDataResult as $key => $items){
			$valueInput = $items[ 'CONTESTACION'];
			if($valueInput!=""){
				if($items['ID_ELEMENTO']==13){
					@$aResult['servicios'][$valueInput]  = (isset($aResult['servicios'][$valueInput])) ? @$aResult['servicios'][$valueInput]+1 :1 ;
					
					@$this->aResumeGral['servicios'][$valueInput]  = (isset($this->aResumeGral['servicios'][$valueInput])) ? @$this->aResumeGral['servicios'][$valueInput]+1 :1 ;
				}else if($items['ID_ELEMENTO']==29){
					@$aResult['estado'][$valueInput]  = (isset($aResult['estado'][$valueInput])) ? @$aResult['estado'][$valueInput]+1 : 1 ;
					
					@$this->aResumeGral['estado'][$valueInput]  = (isset($this->aResumeGral['estado'][$valueInput])) ? @$this->aResumeGral['estado'][$valueInput]+1 : 1 ;
				}else if($items['ID_ELEMENTO']==32){
					@$aResult['total_turistas'] = (isset($aResult['total_turistas'])) ? @$aResult['total_turistas']+$valueInput : 1 ;
					
					@$this->aResumeGral['total_turistas'] = (isset($this->aResumeGral['total_turistas'])) ? @$this->aResumeGral['total_turistas']+$valueInput : 1 ;
				}else if($items['ID_ELEMENTO']==34){	
					@$aResult['nivel'][$valueInput]  = (isset($aResult['nivel'][$valueInput])) ? @$aResult['nivel'][$valueInput]+1 : 1 ;
					
					@$this->aResumeGral['nivel'][$valueInput]  = (isset($this->aResumeGral['nivel'][$valueInput])) ? @$this->aResumeGral['nivel'][$valueInput]+1 : 1 ;
				}else if($items['ID_ELEMENTO']==54){
					@$aResult['tipo_acc'][$valueInput]  = (isset($aResult['tipo_acc'][$valueInput])) ? @$aResult['tipo_acc'][$valueInput]+1 : 1 ;
						
					@$this->aResumeGral['tipo_acc'][$valueInput]  = (isset($this->aResumeGral['tipo_acc'][$valueInput])) ? @$this->aResumeGral['tipo_acc'][$valueInput]+1 : 1 ;
				}				
			}
		}		
		return $aResult;
	}
}