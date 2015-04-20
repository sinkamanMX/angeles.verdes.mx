<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Functions extends My_Db_Table
{
	public function executeQuery($sql="",$getFirst=false){
		$result= Array();
		if($sql!=""){
			$this->query("SET NAMES utf8",false);	
			$query   = $this->query($sql);
			if(count($query)>0){
				if($getFirst){
					$result = $query[0];
				}else{
					$result = $query;
				}		  
			}	
		}		       
		return $result;			
	}
	
	public function getFieldsName($queryResult){
		$aNameFields = Array();
		if(count($queryResult)>0){
			foreach($queryResult as $key => $items){
				$aNameFields = array_keys($items);
				break;
			}
		}
		
		return $aNameFields;
	}
	
	public function getFieldsNameSimple($queryResult){
		$aNameFields = Array();			
		$aNameFields = array_keys($queryResult);		
		return $aNameFields;		
	}
	
	public function constructSqlActions($sNameTable,$sIdTable,$sAction='new',$queryResult,$idObject,$dataIn){
		$sSqlResult = '';
		$sSqlHeader = '';	
		$sSqlFooter	= '';		
		$bFieldadd	= false;
		
		if($sAction=='new'){
			$sSqlHeader .= 'INSERT INTO '.$sNameTable.' SET ';
			$sSqlHeader .= 'CREADO = CURRENT_TIMESTAMP, ';
		}else{
			$sSqlHeader .= 'UPDATE '.$sNameTable.' SET ';
			$sSqlFooter  = ' WHERE '.$sIdTable.' = '.$idObject.' LIMIT 1';
		}
				
		if(count($queryResult)>0){
			foreach($queryResult as $key => $items){
				$stringSeparator = '';
				$bFieldadd 		 = false; 
				
				if($items['ON_UPDATES']==1 && $sAction='update'){
					$bFieldadd = true;
				}elseif($items['ON_INSERTS']==1 && $sAction='new'){
					$bFieldadd = true;
				}

				if($bFieldadd){
					$sSqlResult 	 .= ($sSqlResult!='') ? ',' : '' ;
					if($items['TIPO']=='STRING'){
						$stringSeparator .= '"'.$dataIn['input'.$items['INPUT_NAME']].'"';
					}else if($items['TIPO']=='INT'){
						$stringSeparator .= $dataIn['input'.$items['INPUT_NAME']];
					}else if($items['TIPO']=='SHA1'){
						$stringSeparator .= 'SHA1("'.$dataIn['input'.$items['INPUT_NAME']].'")';
					}
					
					//$stringSeparator .= ($items['TIPO']=='STRING') ? '"'.$dataIn['input'.$items['INPUT_NAME']].'"': $dataIn['input'.$items['INPUT_NAME']];
					
					$sSqlResult .= ''.$items['NOMBRE_BD'].' = '.$stringSeparator;					
				}
			}
		}
		return $sSqlHeader.$sSqlResult.$sSqlFooter;
	}
	
    public function insertUpdateRow($sql,$sAction='new'){
        $result     = Array();
        $result['status']  = false;
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				if($sAction='new'){
					$result['id']	   = $query_id[0]['ID_LAST'];	
				}				
				$result['status']  = true;	
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    }

    public function countResult($sql){
        $resultExist = false;
        try{                    	
    		$query   = $this->query($sql,true);
    		if(count($query)>0){
    			$result = $query[0]['TOTAL'];    				
    			$resultExist = true;    			
    		}
    		return $resultExist;	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
    }
}