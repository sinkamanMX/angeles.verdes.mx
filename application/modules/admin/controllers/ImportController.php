<?php

class admin_ImportController extends My_Controller_Action
{
	protected $_clase 	  = 'mimport';
	protected $_keyModule = '';
	public 	  $aDbManInfo = Array();
	public    $sCaracteres= 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789';
	public $realPath='/var/www/vhosts/angeles/htdocs/public';
	//public $realPath='/Users/itecno2/Documents/workspace/angeles.verdes.mx/public';	
	
    public function init(){
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
    
    public function datapaAction(){
    	try{    
			$this->view->layout()->setLayout('layout_blank');   		
    		$rOperation = -1;
    		
			$cFunctions 	  = new My_Controller_Functions();
			$cValidateNumbers = new Zend_Validate_Float();
			$cValidateAlpha   = new Zend_Validate_Alnum(true);
			$cValidaReq		  = new Zend_Validate_NotEmpty();    		
    		
			$cGeoRefs = new My_Model_GeoPuntos();	
			//$aFilter  = $cGeoRefs->getFilterUp();
			$aPuntosE = $cGeoRefs->getFilterPasistencia();
			$aSucursal= $cGeoRefs->getFilterSucursales(); 		
    		    	    		
			$controlImport = 0;
			$aFieldsErrors = Array();			
			
            $targetFolder = $this->realPath.'/trash';
			if(@$_FILES['imageProfile']['name']!=""){	
				$rOperation = 'ok';						
				$tempFile = $_FILES['imageProfile']['tmp_name'];				
				$targetFile = $targetFolder.'/'.$_FILES['imageProfile']['name'];
				
				$fileTypes = array('xls','xlsx');
				$fileParts = pathinfo($_FILES['imageProfile']['name']);
				
				if (in_array($fileParts['extension'],$fileTypes)) {
					$nameFinalFile   = $targetFolder.'/'.$this->view->dataUser['TIPO_USUARIO'].'_'.$cFunctions->getRandomCode().'.'.$fileParts['extension'];
					
					if(move_uploaded_file($tempFile,$nameFinalFile)){
						include $this->realPath.'/PHPExcel/IOFactory.php'; 						
						try {
							$objPHPExcel = PHPExcel_IOFactory::load($nameFinalFile);
						}catch(Exception $e) {
							die('Error loading file "'.pathinfo($nameFinalFile,PATHINFO_BASENAME).'": '.$e->getMessage());
						}
						
						$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
						$arrayCount = count($allDataInSheet);

						for($i=2;$i<=$arrayCount;$i++){
							$controlImport++;
							$controliError  = 0;
							$sError 		= '';
							
							$siSucursal = trim($allDataInSheet[$i]["A"]);
							$siClaveUni = trim($allDataInSheet[$i]["B"]);
							$siDescrip  = $allDataInSheet[$i]["C"];
							$siLatitud  = floatval(trim($allDataInSheet[$i]["D"]));
							$siLongitud = floatval(trim($allDataInSheet[$i]["E"]));
							
							if(!$cValidateNumbers->isValid($siSucursal) || !isset($aSucursal[$siSucursal])){
								$sError .= 'La Jefatura '.$siSucursal.' no existe <br>';
								$controliError++;	
							}
							
							if(!$cValidaReq->isValid($siDescrip)){
								$sError .= 'Favor de verificar la Descripcion <br>';
								$controliError++;				
							}
							
							if(!$cValidateAlpha->isValid($siClaveUni) || isset($aPuntosE[$siClaveUni])){
								$sError .=  'La clave '.$siClaveUni.' ya se encuentra registrada <br>';
								$controliError++;				
							}
							
							if(!$cValidateNumbers->isValid($siLatitud)){
								$sError .= 'Favor de verificar la latitud <br>';
								$controliError++;			
							}
							
							if(!$cValidateNumbers->isValid($siLongitud)){
								$sError .= 'Favor de verificar la longitud <br>';
								$controliError++;			
							}

							if($controliError  == 0){
								$aInsertData = Array();
								
								$aInsertData['inputEmpresa'] 	= $this->view->dataUser['ID_EMPRESA'];
								$aInsertData['inputSucursal'] 	= $siSucursal[0];
								$aInsertData['inputClave'] 		= $siClaveUni;
								$aInsertData['inputDescripcion']= $siDescrip;
								$aInsertData['inputLatOrigen'] 	= $siLatitud;
								$aInsertData['inputLonOrigen'] 	= $siLongitud;
								$aInsertData['inputEstatus'] 	= '1';	
								
								$insertDate  = $cGeoRefs->insertPasistencia($aInsertData);	
								
								if(!$insertDate['status']){
									$sError	.= 'Error al insertar la informaci—n.';
									$allDataInSheet[$i]['linea']  = $i;
									$allDataInSheet[$i]['errors'] = $sError;
									$aFieldsErrors[] = $allDataInSheet[$i];
								}								
							}else{
								$allDataInSheet[$i]['linea']  = $i;
								$allDataInSheet[$i]['errors'] = $sError;
								$aFieldsErrors[] = $allDataInSheet[$i]; 
							}
														
						}
					}
				}else {
					echo "El archivo es inv‡lido.";
				}
			}
			
			$this->view->iProcess = $controlImport;
			$this->view->iErrors  = $aFieldsErrors;
    		$this->view->Operation = $rOperation;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }
    
    public function datacampAction(){
    	try{    
			$this->view->layout()->setLayout('layout_blank');   		
    		$rOperation = -1;
    		
			$cFunctions 	  = new My_Controller_Functions();
			$cValidateNumbers = new Zend_Validate_Float();
			$cValidateAlpha   = new Zend_Validate_Alnum(true);
			$cValidaReq		  = new Zend_Validate_NotEmpty();    		
    		
			$cGeoRefs = new My_Model_GeoPuntos();	
			//$aFilter  = $cGeoRefs->getFilterUp();
			$aPuntosE = $cGeoRefs->getFilterCampamentos();
			$aSucursal= $cGeoRefs->getFilterSucursales(); 		
    		    	    		
			$controlImport = 0;
			$aFieldsErrors = Array();			
			
            $targetFolder = $this->realPath.'/trash';
			if(@$_FILES['imageProfile']['name']!=""){	
				$rOperation = 'ok';						
				$tempFile = $_FILES['imageProfile']['tmp_name'];				
				$targetFile = $targetFolder.'/'.$_FILES['imageProfile']['name'];
				
				$fileTypes = array('xls','xlsx');
				$fileParts = pathinfo($_FILES['imageProfile']['name']);
				
				if (in_array($fileParts['extension'],$fileTypes)) {
					$nameFinalFile   = $targetFolder.'/'.$this->view->dataUser['TIPO_USUARIO'].'_'.$cFunctions->getRandomCode().'.'.$fileParts['extension'];
					
					if(move_uploaded_file($tempFile,$nameFinalFile)){
						include $this->realPath.'/PHPExcel/IOFactory.php'; 						
						try {
							$objPHPExcel = PHPExcel_IOFactory::load($nameFinalFile);
						}catch(Exception $e) {
							die('Error loading file "'.pathinfo($nameFinalFile,PATHINFO_BASENAME).'": '.$e->getMessage());
						}
						
						$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
						$arrayCount = count($allDataInSheet);

						for($i=2;$i<=$arrayCount;$i++){
							$controlImport++;
							$controliError  = 0;
							$sError 		= '';
							
							$siSucursal = trim($allDataInSheet[$i]["A"]);
							$siClaveUni = trim($allDataInSheet[$i]["B"]);
							$siDescrip  = $allDataInSheet[$i]["C"];
							$siLatitud  = floatval(trim($allDataInSheet[$i]["D"]));
							$siLongitud = floatval(trim($allDataInSheet[$i]["E"]));
							$siDireccion= trim($allDataInSheet[$i]["F"]);
							
							if(!$cValidateNumbers->isValid($siSucursal) || !isset($aSucursal[$siSucursal])){
								$sError .= 'La Jefatura '.$siSucursal.' no existe <br>';
								$controliError++;	
							}
							
							if(!$cValidaReq->isValid($siDescrip)){
								$sError .= 'Favor de verificar la Descripcion <br>';
								$controliError++;				
							}
							
							if(!$cValidateAlpha->isValid($siClaveUni) || isset($aPuntosE[$siClaveUni])){
								$sError .=  'La clave '.$siClaveUni.' ya se encuentra registrada <br>';
								$controliError++;				
							}
							
							if(!$cValidateNumbers->isValid($siLatitud)){
								$sError .= 'Favor de verificar la latitud <br>';
								$controliError++;			
							}
							
							if(!$cValidateNumbers->isValid($siLongitud)){
								$sError .= 'Favor de verificar la longitud <br>';
								$controliError++;			
							}
							
							if(!$cValidaReq->isValid($siDireccion)){
								$sError .= 'Sin Direcci—n <br>';
								$controliError++;				
							}

							if($controliError  == 0){
								$aInsertData = Array();
								
								$aInsertData['inputEmpresa'] 	= $this->view->dataUser['ID_EMPRESA'];
								$aInsertData['inputSucursal'] 	= $siSucursal[0];
								$aInsertData['inputClave'] 		= $siClaveUni;
								$aInsertData['inputDireccion'] 	= $siDireccion;
								$aInsertData['inputDescripcion']= $siDescrip;
								$aInsertData['inputLatOrigen'] 	= $siLatitud;
								$aInsertData['inputLonOrigen'] 	= $siLongitud;
								$aInsertData['inputEstatus'] 	= '1';	
								
								$insertDate  = $cGeoRefs->insertCampamento($aInsertData);	
								
								if(!$insertDate['status']){
									$sError	.= 'Error al insertar la informaci—n.';
									$allDataInSheet[$i]['linea']  = $i;
									$allDataInSheet[$i]['errors'] = $sError;
									$aFieldsErrors[] = $allDataInSheet[$i];
								}								
							}else{
								$allDataInSheet[$i]['linea']  = $i;
								$allDataInSheet[$i]['errors'] = $sError;
								$aFieldsErrors[] = $allDataInSheet[$i]; 
							}
														
						}
					}
				}else {
					echo "El archivo es inv‡lido.";
				}
			}
			
			$this->view->iProcess = $controlImport;
			$this->view->iErrors  = $aFieldsErrors;
    		$this->view->Operation = $rOperation;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  			
    }    
}