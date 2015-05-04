<?php

class admin_PhonesController extends My_Controller_Action
{
	protected $_clase 	  = 'mphones';
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
    
	public function formlisteventsAction(){
		try{	
			$tabSelected	= 1;		
    		$this->view->layout()->setLayout('blank');			
			$cEventosTel = new My_Model_EventosTel();
			$cTelefonos	 = new My_Model_Telefonos();
			$aEventos 	 = Array();
			
			if($this->_idUpdate){
				$tabSelected = (isset($this->_dataIn['strTabSelected']) &&
    						    $this->_dataIn['strTabSelected'] !="") ? $this->_dataIn['strTabSelected'] : 1;
    						    				
				$dataRow	= $cTelefonos->getDataRow($this->_idUpdate);
				$aEventos   = $cEventosTel->getDataTables($this->_idUpdate,$dataRow['ID_MODELO']);	
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
					$bDeleteRows	= $cTelefonos->deleteRelEvent($this->_idUpdate);
					if(@$this->_dataIn['checkAllItems']!=''){
						$bInserts = $cTelefonos->setAllEventos($this->_idUpdate,$dataRow['ID_MODELO']);
						if(!$bInserts['status']){
							Zend_Debug::dump("error al insertar el formulario completo");
							$aErrorsModules++;
						}
					}else{
						for($i=0;$i<count($aValuesForm);$i++){
							$aDataForm['catId']      = $this->_idUpdate;
							$aDataForm['inputEvento']= $aValuesForm[$i];
							
							$insertForm = $cTelefonos->setRelEventos($aDataForm);
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
    				$aEventos   = $cEventosTel->getDataTables($this->_idUpdate,$dataRow['ID_MODELO']);
					$sUrl		= '/dbman/main/getdatainfo?ssIdource='.$this->aDbManInfo['CLAVE_MODULO'].'&catId='.$this->_idUpdate.'&strTabSelected='.$tabSelected.'&optResult=okOperation';
    				$this->_redirect($sUrl);
				}else{
					$this->_aErrors['errorModules']   = '1';
				}				
			}
			 		
			$this->view->aDataEventos = $aEventos;
			$this->view->tabSelected  = $tabSelected;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
	}
	     
}