<?php

class forms_MainController extends My_Controller_Action
{
	protected $_clase 	  = 'mforms';
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
			$this->view->dataUser['allwindow'] = true;     					    	    
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }
    
	public function getinfoAction(){
		try{	
			$aDataInfo	  = Array();
			$aEstatus	  = '';
			$aQrs  		  = ''; 
			$aFirms 	  = '';
			$aFotos		  = '';
			$aLocalizacion= '';			
			$cFormularios = new My_Model_Formularios();
			$cFunctions   = new My_Controller_Functions();
			$cTipos			= new My_Model_TipoFormularios();
			$aTipos			= $cTipos->getCbo();			
			$aElementos	  = Array();
			
    		$tabSelected = (isset($this->_dataIn['strTabSelected']) &&
    						    $this->_dataIn['strTabSelected'] !="") ? $this->_dataIn['strTabSelected'] : 1;			
			
			if($this->_idUpdate>0){
				$aDataInfo 	  = $cFormularios->getData($this->_idUpdate);
				$aEstatus	  = $aDataInfo['ACTIVO'];
				$aQrs  		  = $aDataInfo['QRS_EXTRAS'];
				$aFirms 	  = $aDataInfo['FIRMAS_EXTRAS'];
				$aFotos		  = $aDataInfo['FOTOS_EXTRAS'];
				$aLocalizacion= $aDataInfo['LOCALIZACION'];
				$aElementos	  = $cFormularios->getElementos($this->_idUpdate,$this->_dataUser['ID_EMPRESA']);
			}
			
			if($this->_dataOp=='new' || $this->_dataOp=='update'){
				$valdidate = $cFormularios->validateDataBy($this->_dataIn['inputTitulo'],$this->_idUpdate,$this->_dataUser['ID_EMPRESA']);
				if(count($valdidate)==0){
					$resultOp = Array();
					if($this->_dataOp=='new'){
						$resultOp = $cFormularios->insertRow($this->_dataIn);
						if($resultOp['status']){
							$this->_idUpdate = $resultOp['id'];
							$this->_resultOp = 'okRegister';
						}												
					}else if($this->_dataOp=='update'){
						$resultOp = $cFormularios->updateRow($this->_dataIn);
						$this->_resultOp = 'okRegister';
					}
					
					if($resultOp['status']){
						$this->_resultOp = 'okRegister';   
						$aDataInfo 	  = $cFormularios->getData($this->_idUpdate);
						$aEstatus	  = $aDataInfo['ACTIVO'];
						$aQrs  		  = $aDataInfo['QRS_EXTRAS'];
						$aFirms 	  = $aDataInfo['FIRMAS_EXTRAS'];
						$aFotos		  = $aDataInfo['FOTOS_EXTRAS'];
						$aLocalizacion= $aDataInfo['LOCALIZACION'];						
					}else{
						$aItemsError = Array();
						$aItemsError['inputName'] 	 = 'inputTitulo';
						$aItemsError['bValidate']	 = 'error';
						$aItemsError['MessageError'] = "Ha ocurrido un problema al registrar la informaci—n, favor de intentar mas tarde.";			
						$this->_aErrors['errorAction'] = 1;
						$this->_aErrorsFields[]		   = $aItemsError;
					}												
				}else{
					$aItemsError = Array();
					$aItemsError['inputName'] 	 = 'inputTitulo';
					$aItemsError['bValidate']	 = 'error';
					$aItemsError['MessageError'] = "El titulo ya se encuentra registrado, favor de ingresar una diferente.";			
					$this->_aErrors['errorAction'] = 1;
					$this->_aErrorsFields[]		   = $aItemsError;
				}
			}
			
			if($this->_dataOp=='updateElements'){
				$iControlE = 0;
				$aValuesForm = $this->_dataIn['aElements'];
				if(count($aValuesForm)>0){
					for($i=0;$i<count($aValuesForm);$i++){	
						$aResult = false;											
						$aElement = $aValuesForm[$i];
						if($aElement['op']=='new' && $aElement['id']==-1){
							$aResult = $cFormularios->insertElement($aElement,$this->_idUpdate,$this->_dataUser['ID_EMPRESA']);
						}else if($aElement['op']=='up' && $aElement['id']>-1){
							$aResult = $cFormularios->updateRowRel($aElement);
						}else if($aElement['op']=='del' && $aElement['id']>-1){
							$aResult = $cFormularios->deleteRowRel($aElement,$this->_idUpdate);
						}
						
						if($aResult){
							$iControlE++;
						}
					}
					
					if($iControlE==count($aValuesForm)){
						$this->_resultOp = 'okRegister';
						$aElementos	  = $cFormularios->getElementos($this->_idUpdate,$this->_dataUser['ID_EMPRESA']);
					}
				}
			}
			
			$this->view->aDataInfo 	= $aDataInfo;
			$this->view->aEstatus	= $cFunctions->cboStatusString($aEstatus);
			$this->view->aQrs		= $cFunctions->cboStatusYesNo($aQrs);
			$this->view->aFirms		= $cFunctions->cboStatusYesNo($aFirms);
			$this->view->aFotos		= $cFunctions->cboStatusYesNo($aFotos);
			$this->view->aLocal		= $cFunctions->cboStatusYesNo($aLocalizacion);			
			$this->view->errors 	= $this->_aErrors;	
			$this->view->resultOp   = $this->_resultOp;
			$this->view->catId		= $this->_idUpdate;
			$this->view->idToUpdate = $this->_idUpdate;
			$this->view->aErrorFields= $this->_aErrorsFields;    		
    		$this->view->tabSelected= $tabSelected;	
    		$this->view->aElements	= $this->processFields($aElementos);		
    		
    		$this->view->selectStatus  = $cFunctions->cboStatusString();
    		$this->view->selectOptions = $cFunctions->cboStatusYesNo();
    		$this->view->selectTypes   = $cFunctions->selectDb($aTipos,'');
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
	} 
	
	public function processFields($aElements){
		$cFunctions 	= new My_Controller_Functions();
		$cFormularios 	= new My_Model_Formularios();
		$cTipos			= new My_Model_TipoFormularios();
		$aTipos			= $cTipos->getCbo();
		$aResult = Array();
		
		foreach($aElements as $key => $items){
			$items['cboStatus'] = $cFunctions->cboStatusString($items['ACTIVO']);
			$items['cboReq']	= $cFunctions->cboStatusYesNo($items['REQUERIDO']);
			$items['cboVal']	= $cFunctions->cboStatusYesNo($items['VALIDAR_LOCAL']);
			$items['cboTipo']	= $cFunctions->selectDb($aTipos,$items['ID_TIPO']);
			$aResult[] = $items;
		}
		
		return $aResult;
	}
}