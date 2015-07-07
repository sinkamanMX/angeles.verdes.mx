<?php

class admin_GeosController extends My_Controller_Action
{
	protected $_clase 	  = 'mgeos';
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
					$aDataInfo 	= $cGeos->getDataRow($this->_idUpdate);
		    		$sEstatus	= $aDataInfo['ESTATUS'];
		    		$sTipo		= $aDataInfo['ID_TIPO'];
		    		$sSucursal  = $aDataInfo['ID_SUCURSAL'];						
					$this->_resultOp = 'okRegister';
				}
			}else if($this->_dataOp=='update'){
				$resultOp = $cGeos->updateRowPoint($this->_dataIn);
				if($resultOp['status']){
					$aDataInfo 	= $cGeos->getDataRow($this->_idUpdate);
	    			$sEstatus	= $aDataInfo['ESTATUS'];
	    			$sTipo		= $aDataInfo['ID_TIPO'];
	    			$sSucursal  = $aDataInfo['ID_SUCURSAL'];	
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

    public function importdataAction(){
    	try{
			$this->view->layout()->setLayout('layout_blank');    
			$cFunctions = new My_Controller_Functions();
			$iOption = -1;
			$iToken  = Date("Ymd")."_".$this->view->dataUser['ID_USUARIO'];			
			
			if(isset($this->_dataIn['optImp']) && $this->_dataIn['optImp']!=-1 ){
				$iOption = $this->_dataIn['optImp'];
			}
			$cGeos = new My_Model_GeoPuntos();
			
    		$this->view->iOptionImport = $iOption;
    		$this->view->inputToken    = $iToken;
		}catch(Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 
    }  

    public function uploadfilepointsAction(){
        try{
			$this->view->layout()->setLayout('layout_blank');  			
			$cFunctions 	  = new My_Controller_Functions();
			$cValidateNumbers = new Zend_Validate_Float();
			$cValidateAlpha   = new Zend_Validate_Alnum(true);
			$cValidaReq		  = new Zend_Validate_NotEmpty();
			
			$cGeoRefs = new My_Model_GeoPuntos();	
			$aFilter  = $cGeoRefs->getFilterUp();
			$aPuntosE = $cGeoRefs->getFilterPuntos();
			$aSucursal= $cGeoRefs->getFilterSucursales();
			
			$controlImport = 0;
			$aFieldsErrors = Array();			
			
            $targetFolder = $this->realPath.'/trash';
			if(@$_FILES['imageProfile']['name']!=""){							
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
							$sError = '';
							
							$siSucursal = trim($allDataInSheet[$i]["A"]);
							$siTipo 	= trim($allDataInSheet[$i]["B"]);
							$siDescrip  = $allDataInSheet[$i]["C"];
							$siClaveUni = trim($allDataInSheet[$i]["D"]);
							$siLatitud  = floatval(trim($allDataInSheet[$i]["E"]));
							$siLongitud = floatval(trim($allDataInSheet[$i]["F"]));
							$siRadio    = floatval(trim($allDataInSheet[$i]["G"]));
							
							if(!$cValidateNumbers->isValid($siSucursal) || !isset($aSucursal[$siSucursal])){
								$sError .= 'La Jefatura '.$siSucursal.' no existe <br>';
								$controliError++;	
							}
							
							if(!$cValidateAlpha->isValid($siTipo) || !isset($aFilter[$siTipo])){
								$sError .=  'La tipo de referencia '.$siTipo.' no existe <br>';
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
							
							if(!$cValidateNumbers->isValid($siRadio)){
								$sError .= 'Favor de verificar el radio <br>';
								$controliError++;
							}
							
							if($controliError  == 0){
								$aInsertData = Array();
								$aInsertData['inputEmpresa'] 	= $this->view->dataUser['ID_EMPRESA'];
								$aInsertData['inputSucursal'] 	= $siSucursal[0];
								$aInsertData['inputTipo'] 		= @$aFilter[$siTipo]['ID'];
								$aInsertData['inputDescripcion']= $siDescrip;
								$aInsertData['inputClave'] 		= $siClaveUni;
								$aInsertData['inputLatOrigen'] 	= $siLatitud;
								$aInsertData['inputLonOrigen'] 	= $siLongitud;
								$aInsertData['inputRadio'] 		= $siRadio;	
								$aInsertData['inputEstatus'] 	= '1';			
								
								$insertDate  = $cGeoRefs->insertRowPoint($aInsertData);								
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
		}catch(Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }
    
    public function uploadfilegeosAction(){
        try{
			$this->view->layout()->setLayout('layout_blank');  	

			$cFunctions = new My_Controller_Functions();
			$cValidateNumbers = new Zend_Validate_Float();
			$cValidateAlpha   = new Zend_Validate_Alnum(true);
			$cValidaReq		  = new Zend_Validate_NotEmpty();
			
			$cGeoRefs = new My_Model_GeoPuntos();
			
			$aFilter  = $cGeoRefs->getFilterUp();
			$aPuntosE = $cGeoRefs->getFilterPuntos();
			$aSucursal= $cGeoRefs->getFilterSucursales();
			$aColores = $cGeoRefs->getFilterColores();
			
			$controlImport = 0;
			$aFieldsErrors = Array();			
			
            $targetFolder = $this->realPath.'/trash';
			if(@$_FILES['imageProfile']['name']!=""){		
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
							$sPolygon		= ($this->_dataIn['optImp']==2) ? 'POLYGON((' : 'LINESTRING(';
							$sintPos  		= '';
							
							$siSucursal = trim($allDataInSheet[$i]["A"]);
							$siTipo 	= trim($allDataInSheet[$i]["B"]);
							$siClaveUni = trim($allDataInSheet[$i]["C"]);							
							$siColor	= trim($allDataInSheet[$i]["D"]);
							$siDescrip  = $allDataInSheet[$i]["E"];							
							$siPos    	= $allDataInSheet[$i]["F"];
													
							if(!$cValidateNumbers->isValid($siSucursal) || !isset($aSucursal[$siSucursal])){
								$sError .= 'La Jefatura '.$siSucursal.' no existe <br>';
								$controliError++;	
							}
							
							if(!$cValidateAlpha->isValid($siTipo) || !isset($aFilter[$siTipo])){
								$sError .=  'La tipo de referencia '.$siTipo.' no existe <br>';
								$controliError++;			
							}
							
							if(!$cValidaReq->isValid($siDescrip)){
								$sError .= 'Favor de verificar la Descripcion <br>';
								$controliError++;				
							}
							
							if($cValidateAlpha->isValid($siClaveUni)==false || !isset($aPuntosE[$siClaveUni])==false ){														
								$sError .=  'La clave '.$siClaveUni.' ya se encuentra registrada <br>';
								$controliError++;				
							}
							
							if(!$cValidateAlpha->isValid($siColor) || !isset($aColores[$siColor])){
								$sError .=  'El color '.$siColor.' no se encuentra registrada <br>';
								$controliError++;
							}							
							
							if($siPos==""){
								$sError .=  'Sin Posiciones <br>';
								$controliError++;				
							}

							
							$aDataPosiciones = explode("|", $siPos);							
							if(count($aDataPosiciones)==0){
								$sError .=  'Sin Posiciones <br>';
								$controliError++;
							}else{
								$totalIntErrors = 0;								
								for($p=0;$p<count($aDataPosiciones);$p++){
									$aIntData = explode(",",$aDataPosiciones[$p]);									
									if(count($aIntData)>0){	
										$intLatitud = floatval(trim($aIntData[0]));
										$intLongitud= floatval(trim($aIntData[1]));
										
										if($cValidateNumbers->isValid($intLatitud)==true && $cValidateNumbers->isValid($intLongitud)==true){
											$sintPos .= ($sintPos!="") ?',':'';
											$sintPos .= $intLatitud." ".$intLongitud;			
										}else{
											$totalIntErrors++;
										}
									}else{
										$totalIntErrors++;
									}
								}
								
								if($totalIntErrors>0){
									$sError .=  'Las posiciones no son v‡lidas <br>';
									$controliError++;
								}
							}		
							
							if($controliError  == 0){
								$aInsertData = Array();
								$aInsertData['inputEmpresa'] 	= $this->view->dataUser['ID_EMPRESA'];
								$aInsertData['inputSucursal'] 	= $siSucursal[0];
								$aInsertData['inputTipo'] 		= @$aFilter[$siTipo]['ID'];
								$aInsertData['inputDescripcion']= $siDescrip;
								$aInsertData['inputClave'] 		= $siClaveUni;
								$aInsertData['inputTypeObj'] 	= ($this->_dataIn['optImp']==2) ? 'C':'R';
								$aInsertData['inputColor'] 		= @$aColores[$siColor]['ID'];
								$aInsertData['inputEstatus'] 	= '1';
								
								$insertDate  = $cGeoRefs->insertRowGeo($aInsertData);
								if($insertDate['status']){
									$sPolygon   .= $sintPos;
									$sPolygon	.= ($this->_dataIn['optImp']==2) ? '))' : ')';
									
									$aDataSpacial = Array();
									$aDataSpacial['id'] 	= $insertDate['id'];
									$aDataSpacial['object'] = $sPolygon;									
									$insertSpatial = $cGeoRefs->insertSpatialRow($aDataSpacial);
								}else{
									$sError .= 'Error al insertar el registro <br>';
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
					}else{
						echo "Ocurrio un error al subir el archivo.";
					}
				}else {
					echo "El archivo es inv‡lido.";
				}
			}
			
			$this->view->iProcess = $controlImport;
			$this->view->iErrors  = $aFieldsErrors;
		}catch(Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }    
}