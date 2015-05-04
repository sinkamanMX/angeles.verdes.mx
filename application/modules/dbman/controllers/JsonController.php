<?php
class dbman_JsonController extends My_Controller_Action
{
	protected $_clase 	  = 'dbman';
    public function init()
    {    	
		$this->validateSession();
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();   		
    }   
    
    public function operationsAction(){
    	try{
			/**
			 * Para eliminar un registro se hace por medio de ajax.
			 *
			 * Con estas dos primeras lineas se deshabilita la vista del framework
			 */
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$answer = Array('answer' => 'no-data');
    		$cDbman 	   	  = new My_Model_DbmanConfig();
    		$cFunctions   	  = new My_Model_Functions();
    		$cPerfiles		  = new My_Model_Perfiles();								
    		
    	 	if($this->_dataOp=='delete' && isset($this->_dataIn['ssIdource']) && $this->_dataIn['ssIdource']){
    			$this->_keyModule		= $this->_dataIn['ssIdource'];
    			$this->view->moduleInfo = $cPerfiles->getDataModule($this->_keyModule);
	
	    		$inputEmpresa     = $this->_dataUser['ID_EMPRESA'];    		
	    		$this->aDbManInfo = $cDbman->getData($this->_keyModule);    		
	    		$sQuery 		  = str_ireplace('$inputEmpresa',$inputEmpresa,$this->aDbManInfo['TABLA_QUERY']);
				$cDbman->_name	  = $this->aDbManInfo['TABLA_NOMBRE'];
				$cDbman->_primary = $this->aDbManInfo['TABLA_ID'];
				
	    		$delete = $cDbman->deleteRow($this->_dataIn);
				if($delete){
					$answer = Array('answer' => 'deleted'); 
				}
			}
	        echo Zend_Json::encode($answer);
	        die();			     		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }
}