<?php
class dbman_SearchController extends My_Controller_Action
{
	protected $_clase 	  = 'dbman';
	protected $_keyModule = '';
	public 	  $aDbManInfo = Array();
	
    public function init()
    {
    	try{   		
    		$cPerfiles	= new My_Model_Perfiles();
    		$this->validateSession();
				
    	    if(isset($this->_dataIn['ssIdource']) && $this->_dataIn['ssIdource']){    	    	
    			$this->_keyModule		= $this->_dataIn['ssIdource'];
    			$this->view->moduleInfo = $cPerfiles->getDataModule($this->_keyModule);

				$cDbman 	   	  = new My_Model_DbmanConfig();
    			$this->aDbManInfo = $cDbman->getData($this->_keyModule);
				$this->view->DbmanInfo   = $this->aDbManInfo;		    							    			
    		}else{
    			$this->_redirect("/");
    		}    
    				
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }

    public function indexAction(){
    	try{
    		$this->view->layout()->setLayout('blank');
    		$cFunctions   	  = new My_Model_Functions();
    		    	
    		$inputEmpresa     = $this->_dataUser['ID_EMPRESA'];    		    		
    		$sQuery 		  = str_ireplace('$inputEmpresa',$inputEmpresa,$this->aDbManInfo['BUSQUEDA_QUERY']);
    		
    		$this->view->aTittles    = explode(',',$this->aDbManInfo['BUSQUEDA_TITULOS']);
    		$this->view->aDataTable  = $cFunctions->executeQuery($sQuery);
    		$this->view->aNamesTable = $cFunctions->getFieldsName($this->view->aDataTable);    		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
    }    
	
}
