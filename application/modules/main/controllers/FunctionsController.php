<?php

class main_FunctionsController extends My_Controller_Action
{
	protected $_clase = 'principal';
	public    $dataIn = Array();
	protected $idEmpresa = -1;
	public    $aDbTables = Array (  'distribuidore'        	=> Array('nameClass'=>'Distibuidores'),
									'modelot'              	=> Array('nameClass'=>'Modelostel'),
									'modeloa'              	=> Array('nameClass'=>'Activosmodelos'),
									'modeloe'              	=> Array('nameClass'=>'Activosmodelos'),
									'mun'        			=> Array('nameClass'=>'Municpios'),
									'colonia'    			=> Array('nameClass'=>'Colonias'),
									'horario'    			=> Array('nameClass'=>'Cinstalaciones'),
									'tecnicos'    			=> Array('nameClass'=>'Tecnicos'),
									'musuarios'    			=> Array('nameClass'=>'USUARIOS'),
									'munidades'    			=> Array('nameClass'=>'UNIDADES'),
								);				
    public function init()
    {		
    	try{  
			$cPerfiles	= new My_Model_Perfiles();
    		$this->validateSession();

    		
    		/*	
    		$sessions = new My_Controller_Auth();
			$perfiles = new My_Model_Perfiles();
	        if($sessions->validateSession()){
		        $this->_dataUser   = $sessions->getContentSession(); 	
			}else{
				$this->_redirect("/");
			}    		
			$this->view->dataUser   = $this->_dataUser;
						
			$this->_dataIn 					= $this->_request->getParams();
			$this->_dataIn['userCreate']	= $this->_dataUser['ID_USUARIO'];
			
	    	if(isset($this->_dataIn['optReg'])){
				$this->_dataOp = $this->_dataIn['optReg'];				
			}
			
			if(isset($this->_dataIn['catId'])){
				$this->_idUpdate = $this->_dataIn['catId'];				
			}	*/				
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }
    
    public function indexAction(){
    	
    }
    
    public function getselectAction(){
    	try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();    
			    	
	    	$result = 'no-info';
			$this->dataIn = $this->_request->getParams();
			$functions = new My_Controller_Functions();				
			$validateNumbers = new Zend_Validate_Digits();
			$validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
					
			
			if($validateAlpha->isValid($this->_dataIn['catId']) && $validateAlpha->isValid($this->_dataIn['oprDb'])){
				if(isset($this->aDbTables[$this->dataIn['oprDb']])){
					$classObject = $functions->creationClass($this->_dataIn['oprDb']);
					$cboValues   = $classObject->getCbo($this->_dataIn['catId']);
					$result      = $functions->selectDb($cboValues);		
				}
			}
			
			echo $result;
		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    		
    }
}