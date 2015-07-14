<?php

class atn_MainController extends My_Controller_Action
{	
	protected $_clase = 'mdashboard';
	public $dataIn;	
	public $aService;
		
    public function init()
    {
    	try{	
			$sessions = new My_Controller_Auth();
			$perfiles = new My_Model_Perfiles();
			$this->validateSession();
	        /*if(!$sessions->validateSession()){
	            $this->_redirect('/');		
			}*/
			
			$this->dataIn 			= $this->_request->getParams();
			$this->view->dataUser   = $sessions->getContentSession();
			$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
			$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);		
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }

    public function indexAction()
    {
		try{
			$this->view->dataUser['allwindow'] = true;   			
			$cInstalaciones = new My_Model_Cinstalaciones();
			$cFunciones		= new My_Controller_Functions();
			$cResume		= new My_Model_Resume();	
        	$typeSearch     = "auto";
        	$iTime          = "day";			
			
			$sSucursal		= (isset($this->dataIn['inputSucursal']) && $this->dataIn['inputSucursal']!="") ? $this->dataIn['inputSucursal'] : -1;
			$iFilter 		= ($this->view->dataUser['TIPO_USUARIO']==0) ? $this->view->dataUser['ID_SUCURSAL'] : $this->view->dataUser['ID_EMPRESA'];
			$dataCenter		= $cInstalaciones->getCbo($iFilter,$this->view->dataUser['TIPO_USUARIO']);
			
			if($this->_dataOp =="search"){
	        	if($this->_dataIn['typeSearch']=="manual"){	        					
					$typeSearch     = "manual";
					$sInputFechaIn  = $this->_dataIn['inputDatein'];
					$sInputFechaFin = $this->_dataIn['inputDatefin']; 
	        	}elseif($this->_dataIn['typeSearch']=="auto"){
	        		$iTime         = $this->_dataIn['iTime'];
	        		if($iTime=='day'){
	        			$sDaySearch = 1;
	        		}else if($iTime=='week'){
	        			$sDaySearch = 7;
	        		}else if($iTime=='month'){
	        			$sDaySearch = 30;
	        		}
	        		$sInputFechaIn  = ' DATE_SUB(CURRENT_DATE, INTERVAL '.$sDaySearch.' DAY)';
	        		$sInputFechaFin = ' CURRENT_DATE'; 
	        		$typeSearch    = "auto";
	        	}	        
	        }else{
	        	$sInputFechaIn  = ' DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)';
	        	$sInputFechaFin = ' CURRENT_DATE'; 
			}
			
			$aResults = $cResume->getResultsServices($sSucursal,$sInputFechaIn,$sInputFechaFin);

	
	        		
	        $this->_dataIn['iTime'] 	 = $iTime;
	        $this->_dataIn['typeSearch'] = $typeSearch;			
			$this->view->cInstalaciones = $cFunciones->selectDb($dataCenter,$sSucursal);
			$this->view->iTime		= $iTime;
			$this->view->typeSearch = $typeSearch;	

			$this->view->aResumen	= $cResume->aResumeGral;
			$this->view->aResumenDet= $aResults;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function getcitaspendientesAction(){
    	try{
    		/*
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();    
	                
			$cCitas = new My_Model_Citas();
			$type   = (isset($this->dataIn['iType']) && $this->dataIn['iType']!="") ? $this->dataIn['iType'] : -1;
			
			$dataCitas = $cCitas->getCitasPendientes($type);
				
			if($type==2){
				$dataCitas = $this->processData($dataCitas);	
			}
			
			echo Zend_Json::encode($dataCitas);
			*/
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }
    
    public function processData($aDataToProcess){
    	$aResult = Array();
    	$cCitas = new My_Model_Citas();
    	foreach($aDataToProcess as $key => $items){
    		$sTittle = '';
    		$sIds	 = '';
    		$aDataCount = $cCitas->getResume($items['FECHA_CITA'],$items['HORA_CITA']);
    		foreach($aDataCount as $key => $itemsCount){
    			$sTittle .= ($sTittle!="") ? '
    			' : '';
    			$sIds	 .= ($sTittle!="") ? ',':'';
    			$sTittle .= $itemsCount['N_TITTLE'].': '.$itemsCount['TOTAL'];
    			$sIds	 .= $itemsCount['IDS'];    			
    		}	
    		$items['title']	= $sTittle;
    		$items['IDS']	= $sIds;
    		$aResult[]	= $items;
    	}
    	
    	return $aResult;
    }
    
    public function getlistdatesAction(){
		try{
			$aList = Array();
			$this->view->layout()->setLayout('layout_blank');
			$cCitas   = new My_Model_Citas();
			if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']!=''){
				$sIds  = $this->dataIn['strInput'];
				$aList = $cCitas->getDateByList($sIds);
			}
			
			$this->view->dataSearch = $aList;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }		
    }
    
	public function searchcitasAction(){
		try{
			$this->view->layout()->setLayout('layout_blank'); 
			$formShow = 0;
			$cCitas   = new My_Model_Citas();
			$funtions = new My_Controller_Functions();
			$dataStatus = $cCitas->getCboStatus();
			
			if(isset($this->dataIn['opSearch']) && $this->dataIn['opSearch']=='search'){
				$fechaIn		= $this->dataIn['inputFechaIn'];
				$fechaFin		= $this->dataIn['inputFechaFin'];
				$status			= $this->dataIn['inputEstatus'];
				$stringSearch	= $this->dataIn['inputSearch'];
				
				$dataSearch     = $cCitas->getCitasSearch($fechaIn,$fechaFin,$stringSearch,$status);
				$this->view->dataSearch = $dataSearch;
				$formShow 		= 1;
			}
			
			$this->view->Status   = $funtions->selectDb($dataStatus);
			$this->view->showForm = $formShow;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }		
	} 

	public function citadetalleAction(){
		try{
			$this->view->layout()->setLayout('layout_blank');
			 			
			$cCitas     = new My_Model_Citas();
			$funtions   = new My_Controller_Functions();
			$cUsuarios  = new My_Model_Usuarios();
			
			$dataStatus = $cCitas->getCboStatus();
			$dataUsers  = $cUsuarios->getCbOperadores();
			$dataDate = Array();
			$dataStat = '';
			$opAsign  = '';
			$statusOpr = false;
			
			if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']!=""){
				$iIdCita	= $this->dataIn['strInput'];
				$dataDate   = $cCitas->getCitasDet($iIdCita);
				$dataStat	= $dataDate['ID_ESTATUS'];
				$opAsign	= $dataDate['ID_OPERADOR'];					
				if(isset($this->dataIn['opSearch']) && $this->dataIn['opSearch']=="opSearch"){
					$inputPersonal = $this->dataIn['inputPersonal'];
					$this->dataIn['ID_USUARIO']  = $this->view->dataUser['ID_USUARIO'];					
					$dataToChange  = $cCitas->setRow($this->dataIn);
					if($dataToChange){
						if($inputPersonal != $opAsign && isset($this->dataIn['inputPersonal'])){
							$this->dataIn['ID_OPERADOR'] = $opAsign;
							$updateRowOp = $cCitas->changePersonal($this->dataIn);	
						}else{
							$statusOpr = true;	
						}
					}
				}				
				$dataDate   = $cCitas->getCitasDet($iIdCita);
			}
			
			$this->view->Status   = $funtions->selectDb($dataStatus,$dataStat);
			$this->view->personal = $funtions->selectDb($dataUsers,$opAsign);
			$this->view->data     = $dataDate;
			$this->view->statusOpr= $statusOpr;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }		
	}
}
