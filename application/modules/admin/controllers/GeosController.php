<?php

class admin_GeosController extends My_Controller_Action
{
	protected $_clase 	  = 'mgeos';
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
    
    public function indexAction(){
    	try{
    		$cGeos     = new My_Model_GeoPuntos();

    		$iFilter = ($this->view->dataUser['TIPO_USUARIO']==0) ? $this->view->dataUser['ID_SUCURSAL'] : $this->view->dataUser['ID_EMPRESA'];
    		$aGeosData = $cGeos->getDataGeo($iFilter,$this->view->dataUser['TIPO_USUARIO']);
    		
    		$this->view->aDataTable = $aGeosData;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
    }

    public function getinfoAction(){
    	
    }
    
    public function getinfopointAction(){
    	try{
    		$cGeos     	= new My_Model_GeoPuntos();
    		$cSucursales= new My_Model_Cinstalaciones();
    		$cFunctions = new My_Controller_Functions();
    		
    		$aDataInfo  = Array();
    		$aTipos	   	= $cGeos->getCboTipos();	
    		$iFilter 	= ($this->view->dataUser['TIPO_USUARIO']==0) ? $this->view->dataUser['ID_SUCURSAL'] : $this->view->dataUser['ID_EMPRESA'];
    		$aSucursales= $cSucursales->getCbo($iFilter,$this->view->dataUser['TIPO_USUARIO']);
    		$sEstatus	= '';
    		$sTipo		= '';
    		$sSucursal  = '';
    		
    		if($this->_idUpdate>0){
				$aDataInfo 	= $cGeos->getDataRow($this->_idUpdate);
	    		$sEstatus	= $aDataInfo['ESTATUS'];
	    		$sTipo		= $aDataInfo['ID_TIPO'];
	    		$sSucursal  = $aDataInfo['ID_SUCURSAL'];				
			}
			
			if($this->_dataOp=='new'){
				$resultOp = $cGeos->insertRowPoint($this->_dataIn);
				if($resultOp['status']){
					$this->_idUpdate = $resultOp['id'];
					$this->_resultOp = 'okRegister';
				}
			}else if($this->_dataOp=='update'){
				$resultOp = $cGeos->updateRowPoint($this->_dataIn);
				if($resultOp['status']){
					$this->_resultOp = 'okRegister';	
				}			
			} 
    		
    		$this->view->data		 = $aDataInfo;
    		$this->view->aTipos 	 = $cFunctions->selectDb($aTipos,$sTipo);
    		$this->view->aSucursales = $cFunctions->selectDb($aSucursales,$sTipo);
    		$this->view->aStatus 	 = $cFunctions->cboStatus($sEstatus);
    		
			$this->view->errors 	= $this->_aErrors;	
			$this->view->resultOp   = $this->_resultOp;
			$this->view->catId		= $this->_idUpdate;
			$this->view->idToUpdate = $this->_idUpdate;    		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
    }    
}