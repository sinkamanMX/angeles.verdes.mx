<?php

class admin_UsersController extends My_Controller_Action
{
	protected $_clase 	  = 'musers';
	protected $_keyModule = '';
	public 	  $aDbManInfo = Array();
	
    public function init()
    {
    	try{   					
    		$cPerfiles	= new My_Model_Perfiles();
    		$this->validateSession();
    		
    		$this->_keyModule		= $this->_clase;
    		$this->view->moduleInfo = $cPerfiles->getDataModule($this->_keyModule);

			$cDbman 	   	  = new My_Model_DbmanConfig();
    		$this->aDbManInfo = $cDbman->getData($this->_keyModule);
			$this->view->DbmanInfo   = $this->aDbManInfo;		    					    	    
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }
    
   
	public function listhorariosAction(){
		try{					
    		$this->view->layout()->setLayout('blank');
    		$cUsuarios	 = new My_Model_Usuarios();
    		$cHorarios 	 = new My_Model_Horarios();
			$aProfiles 	 = Array();
			$tabSelected = 1;
			$aHorarios   = Array();
			
			if($this->_idUpdate){
				$tabSelected = (isset($this->_dataIn['strTabSelected']) &&
    						    $this->_dataIn['strTabSelected'] !="") ? $this->_dataIn['strTabSelected'] : 1;
    						    				
				$dataRow	 = $cUsuarios->getData($this->_idUpdate);
				$aHorarios   = $cHorarios->getAllDataByUser($dataRow['ID_SUCURSAL'],$this->_idUpdate);				
    		}else{
    			$this->_redirect($this->aDbManInfo['SCRIPT']);
    		}
    		
    		if($this->_dataOp=='updateModules'){
				$aDataForm = Array();
				$aValuesForm = $this->_dataIn['formsValues'];

    			$aErrorsModules = 0;					
				if(count($aValuesForm)>0){
					$bDeleteRows	= $cHorarios->deleteByUser($this->_idUpdate);
					for($i=0;$i<count($aValuesForm);$i++){
						$aDataForm['catId']      = $this->_idUpdate;
						$aDataForm['inputhorario']= $aValuesForm[$i];
						
						$insertForm = $cHorarios->insertHorarioUser($aDataForm);
						if(!$insertForm){
							Zend_Debug::dump("error al insertar el formulario ".$aValuesForm[$i]);
							$aErrorsModules++;
						}
					}
				}else{
					$aErrorsModules++;
				}	
				
    			if($aErrorsModules==0){
					$aHorarios   = $cHorarios->getAllDataByUser($dataRow['ID_SUCURSAL'],$this->_idUpdate);
					$sUrl		 = '/dbman/main/getdatainfo?ssIdource='.$this->aDbManInfo['CLAVE_MODULO'].'&catId='.$this->_idUpdate.'&strTabSelected='.$tabSelected.'&optResult=okOperation';
	    			$this->_redirect($sUrl);	
				}else{
					$this->_aErrors['errorModules']   = '1';
				}
    		}
    		
			$this->view->aDataModules = $aHorarios;
			$this->view->tabSelected  = $tabSelected;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
	}        
}