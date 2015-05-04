<?php
class dbman_ValidatorsController extends My_Controller_Action
{
	protected $_clase 	  = 'dbman';
	protected $_keyModule = '';
	public 	  $aDbManInfo = Array();
	
    public function init()
    {
    	try{   		
    		$this->view->layout()->setLayout('blank');
    		$cPerfiles	= new My_Model_Perfiles();
    		$this->validateSession();
			
    	    if(isset($this->_dataIn['ssIdource']) && $this->_dataIn['ssIdource']){    	    	
    			$this->_keyModule		= $this->_dataIn['ssIdource'];
    			$this->view->moduleInfo = $cPerfiles->getDataModule($this->_keyModule);

				$cDbman 	   	  = new My_Model_DbmanConfig();
    			$this->aDbManInfo = $cDbman->getData($this->_keyModule);
				$this->view->DbmanInfo   = $this->aDbManInfo;
    		}        			
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }	   
    
    public function scriptvalidationAction(){
    	try{    	
    		Header("content-type: application/x-javascript");
    		$cDbman 	   	= new My_Model_DbmanConfig();
    		$aDataFields	= $cDbman->getFieldsForm($this->aDbManInfo['ID_DB_MODULO']);
    		
    		$this->view->aFields	= $aDataFields;			
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 
    }
}