<?php

class admin_ProfilesController extends My_Controller_Action
{
	protected $_clase 	  = 'mprofiles';
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

	public function listmodulesAction(){
		try{					
    		$this->view->layout()->setLayout('blank');
    		$cPerfiles	 = new My_Model_Perfiles();
			$aProfiles 	 = Array();
			$tabSelected = 1;
			
			if($this->_idUpdate){
				$tabSelected = (isset($this->_dataIn['strTabSelected']) &&
    						    $this->_dataIn['strTabSelected'] !="") ? $this->_dataIn['strTabSelected'] : 1;
    						    				
				$dataRow	= $cPerfiles->getData($this->_idUpdate);
				$aProfiles   = $cPerfiles->getModulesByProfile($this->_idUpdate,$dataRow['ID_EMPRESA']);	
    		}else{
    			$this->_redirect($this->aDbManInfo['SCRIPT']);
    		}
			    		
			if($this->_dataOp=='updateModules'){
				$this->_helper->layout->disableLayout();
				$this->_helper->viewRenderer->setNoRender();
				
				$aDataForm = Array();
				$aValuesForm = $this->_dataIn['formsValues'];

				$aErrorsModules = 0;					
				if(count($aValuesForm)>0){
					$bDeleteRows	= $cPerfiles->deleteRelEvent($this->_idUpdate);
					if(@$this->_dataIn['checkAllItems']!=''){
						$bInserts = $cPerfiles->setAllEventos($this->_idUpdate,$dataRow['ID_EMPRESA']);
						if(!$bInserts['status']){
							Zend_Debug::dump("error al insertar el formulario completo");
							$aErrorsModules++;
						}
					}else{
						for($i=0;$i<count($aValuesForm);$i++){
							$aDataForm['catId']      = $this->_idUpdate;
							$aDataForm['inputEvento']= $aValuesForm[$i];
							
							$insertForm = $cPerfiles->setRelEventos($aDataForm);
							if(!$insertForm){
								Zend_Debug::dump("error al insertar el formulario ".$aValuesForm[$i]);
								$aErrorsModules++;
							}
						}						
					}
				}else{
					$aErrorsModules++;
				}
				
				if($aErrorsModules==0){
					$bSetInit	 = $cPerfiles->setInitModule($this->_dataIn);
					if($bSetInit['status']){
						$aProfiles   = $cPerfiles->getModulesByProfile($this->_idUpdate,$dataRow['ID_EMPRESA']);
						$sUrl		 = '/dbman/main/getdatainfo?ssIdource='.$this->aDbManInfo['CLAVE_MODULO'].'&catId='.$this->_idUpdate.'&strTabSelected='.$tabSelected.'&optResult=okOperation';
	    				$this->_redirect($sUrl);	
					}
				}else{
					$this->_aErrors['errorModules']   = '1';
				}				
			}
				 		
    		
			$this->view->aDataModules = $aProfiles;
			$this->view->tabSelected  = $tabSelected;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
	}    
}