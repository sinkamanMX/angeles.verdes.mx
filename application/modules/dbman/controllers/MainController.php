<?php
class dbman_MainController extends My_Controller_Action
{
	/**
	 * Clave principal para identificar el modulo
	 * @var $_clase String
	 */
	protected $_clase 	  = 'dbman';
	
	/**
	 * Variable que almacena el id del modulo a procesar
	 * @var $_keyModule String
	 */	
	protected $_keyModule = '';
	
	/**
	 * Variable que almacena en un arreglo la informacion del Modulo a procesar
	 * @var $aDbManInfo Array
	 */	
	public 	  $aDbManInfo = Array();
	
	/**
	 * Variable que almacena en un arreglo el tipo de campo que no se valida.
	 * @var $_fieldsNoValue Array
	 */		
	protected $_fieldsNoValue = Array(6,7);
		
	/**
	 * Funcion que se ejecuta al llamar a este script
	 * Recibe las variables que se reciben, ademas de validar la session del usuario
	 * @see My_Controller_Action::init()
	 */
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
				$this->view->optResult	 = (isset($this->_dataIn['optResult'])) ?  $this->_dataIn['optResult'] : '';		    							    			
    		}else{
    			$this->_redirect("/");
    		}    
    				
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }	
    
    /**
     * Script que mostrara la tabla general de un modulo
     * 
     */
    public function indexAction(){
    	try{
    		$cFunctions   	  = new My_Model_Functions();
    		$inputEmpresa     = $this->_dataUser['ID_EMPRESA'];
    		$inputSucursal	  = $this->_dataUser['ID_SUCURSAL'];

    		if($this->_dataUser['TIPO_USUARIO']==1){
    			$sQuery 		  = str_ireplace('$inputEmpresa',$inputEmpresa,$this->aDbManInfo['TABLA_QUERY_ADMIN']);
    			$this->view->aTittles    = explode(',',$this->aDbManInfo['TABLA_TITULOS_ADMIN']);	
    		}else{
    			$sQuery 		  = str_ireplace('$inputSucursal',$inputSucursal,$this->aDbManInfo['TABLA_QUERY']);
    			$this->view->aTittles    = explode(',',$this->aDbManInfo['TABLA_TITULOS']);
    		}
    		
    		$this->view->aDataTable  = $cFunctions->executeQuery($sQuery);
    		$this->view->aNamesTable = $cFunctions->getFieldsName($this->view->aDataTable);
		}catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function getdatainfoAction(){
    	try{
    		$aTabs			= Array();
    		$tabSelected	= 1;
    		$cFunctions   	= new My_Model_Functions();
    		$cDbman 	   	= new My_Model_DbmanConfig();    

			$aDataFields	= $cDbman->getFieldsForm($this->aDbManInfo['ID_DB_MODULO'],$this->_dataUser['TIPO_USUARIO']);			
    		$aFieldsValues  = $this->setValuesFields($aDataFields,$this->aDbManInfo['ID_DB_MODULO'],$this->_idUpdate);    		
    		$aProcessFields = $this->processFields($aFieldsValues);
    		$inputEmpresa     = $this->_dataUser['ID_EMPRESA'];
    		    				
    		if($this->_dataOp=='new' || $this->_dataOp=='update'){  
				$aValidateFields = $this->validateFields($aDataFields);
				if(count($aValidateFields)==0){
					$sqlInsert 		= $cFunctions->constructSqlActions($this->aDbManInfo['TABLA_NOMBRE'],$this->aDbManInfo['TABLA_ID'],$this->_dataOp,$aDataFields,$this->_idUpdate,$this->_dataIn);
	    			$bActionResult  = $cFunctions->insertUpdateRow($sqlInsert);
	    			if($bActionResult['status']){
	    				//$this->_idUpdate = ($this->_dataOp=='new') ? $bActionResult['id'] : $this->_idUpdate;
	    				$this->_resultOp = 'okRegister';   
	    				if($this->_dataOp=='new'){
	    					$this->_redirect("".$this->view->moduleInfo['SCRIPT']);
	    				}else{
	    					$aFieldsValues  = $this->setValuesFields($aDataFields,$this->aDbManInfo['ID_DB_MODULO'],$this->_idUpdate);    		
	    					$aProcessFields = $this->processFields($aFieldsValues);
	    				}
	    			}else{
	    				$this->_aErrors['errorAction'] = 1;
	    			}
				}else{
					$this->_aErrors['errorAction'] = 1;
					$this->_aErrorsFields		   = $aValidateFields;
				}
    		}
    		
    		$tabSelected = (isset($this->_dataIn['strTabSelected']) &&
    						    $this->_dataIn['strTabSelected'] !="") ? $this->_dataIn['strTabSelected'] : 1;
    		
			if(count($this->_aErrorsFields) > 0){
				$aFieldsValues  = $this->setValuesFieldsIn($aDataFields, $this->_dataIn);
				$aProcessFields = $this->processFields($aFieldsValues);
			}    						    
    						    
    		$this->view->aFields	= $aProcessFields;
			$this->view->errors 	= $this->_aErrors;	
			$this->view->resultOp   = $this->_resultOp;
			$this->view->catId		= $this->_idUpdate;
			$this->view->idToUpdate = $this->_idUpdate;
			$this->view->aErrorFields= $this->_aErrorsFields;			
    		$this->view->aTabs    	= ($this->aDbManInfo['TAB_TITULOS']!="") ? (explode('|',$this->aDbManInfo['TAB_TITULOS'])) : $aTabs;
    		$this->view->tabSelected= $tabSelected;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
    }
    
    public function setValuesFields($aFields,$idModule,$idFind){    	
		$cFunctions    = new My_Controller_Functions();
		$mFunctions    = new My_Model_Functions();
		$aResultFields = Array();

		$sQuery 		= str_ireplace('$inputEmpresa',$this->_dataUser['ID_EMPRESA'],$this->aDbManInfo['DATA_QUERY']);
		$sQuery 		= str_ireplace('$idObject',$idFind,$sQuery);
		$sqlGetData    	= $mFunctions->executeQuery($sQuery,true); 		
		
		if(count($sqlGetData)>0){
			$aNameKeys		= $mFunctions->getFieldsNameSimple($sqlGetData);
			
			foreach($aFields as $key => $itemsFields){						
				for($i=0;$i<count($aNameKeys);$i++){
					if($itemsFields['NOMBRE_BD']==$aNameKeys[$i]){
						$itemsFields['VALUE_INPUT']   = $sqlGetData[$aNameKeys[$i]];
						break;	
					}					
				}
				$aResultFields[] = $itemsFields;					
			}
		}else{			
			foreach($aFields as $key => $items){
					
				$items['VALUE_INPUT'] = '';
				$aResultFields[] 	  = $items;
			}			
		}		
		return $aResultFields;	
    }
    
	public function processFields($aFields){
		$cFunctions    = new My_Controller_Functions();
		$mFunctions    = new My_Model_Functions();
		$aResultFields = Array();			  
		$inputEmpresa  = $this->_dataUser['ID_EMPRESA'];
		$inputSucursal = $this->_dataUser['ID_SUCURSAL'];
		
		foreach($aFields as $key => $items){	
			$inputName = 'input'.$items['INPUT_NAME'];	
			$sAction   = (isset($items['ACCION'])) ? $items['ACCION'] : '' ;
			$valueInput= (isset($items['VALUE_INPUT'])) ? $items['VALUE_INPUT'] : '' ;			
			
			if($items['ID_TIPO_CAMPO']==1){
				$items['INPUT']		 = '<input id="'.$inputName.'" name="'.$inputName.'" type="text" class="input-inline form-control"  value="'.$valueInput.'"  autocomplete="off" '.$sAction.'>';				
			}else if($items['ID_TIPO_CAMPO']==2){
				$items['INPUT']		 = '<select id="'.$inputName.'" name="'.$inputName.'" '.$sAction.' class=""  autocomplete="off">';
				
				if(isset($items['OPCIONES_FUNCTIONS'])){
					$method_name = $items['OPCIONES_FUNCTIONS'];
					if($method_name=='cboStatus'){
						$items['INPUT']  .= $cFunctions->cboStatus($valueInput);	
					}else if($method_name=='cboGenero'){
						$items['INPUT']  .= $cFunctions->cboGenero($valueInput);	
					}else if($method_name=='cboOptions'){
						$items['INPUT']  .= $cFunctions->cboOptions($valueInput);	
					}else if($method_name=='cboHours'){
						$items['INPUT']  .= $cFunctions->getCboHoursDate($valueInput);
					}
				}			
				$items['INPUT']		.= '</select>';
			}else if($items['ID_TIPO_CAMPO']==7){
								
			}else if($items['ID_TIPO_CAMPO']==8){
				$items['INPUT']		 = '<input id="'.$inputName.'" name="'.$inputName.'" type="hidden" class="input-inline form-control"  value="'.$this->_dataIn[$items['VALUE']].'"  autocomplete="off"  '.$sAction.'>';				
			}else if($items['ID_TIPO_CAMPO']==10){
				$aDataValue = ($valueInput!="" && $valueInput!="NULL") ? explode('|', $valueInput) : Array();
				$sValNameInput  = '';
				$sValIdInput	= 'NULL';
				
				if(count($aDataValue)>0){
					$sValIdInput	= $aDataValue[0];
					$sValNameInput  = $aDataValue[1];
				}
				
				$items['INPUT']		 = '<input type="hidden" id="'.$inputName.'" name="'.$inputName.'" value="'.$sValIdInput.'" autocomplete="off" />';
				$items['INPUT']		.= '<div class="input-group">'.
											'<input disabled readonly type="text" name="inputSearch" id="inputSearch" class="form-control" value="'.$sValNameInput.'" autocomplete="off" >	'.
												'<span class="input-group-btn">';
				
				$showOn = ($sValIdInput !="NULL") ? 'none': '';
				$showOff = ($sValIdInput=="NULL") ? 'none': '';
				$items['INPUT'] .= '<span id="spanSearchOff" type="button" class="btn btn-danger" style="display:'.$showOff.'" onClick="cleanSearch(\''.$inputName.'\')"><i class="icon-minus-circle2"></i></span>';
				$items['INPUT'] .= '<span id="spanSearchOn" type="button" class="btn btn-info"    style="display:'.$showOn.'" onClick="openSearch(\''.$items['OPCIONES_AJAX'].'\',\''.$inputName.'\')"><i class="icon-search2"></i></span>';                                                	
               	$items['INPUT']		.= '</span></div>';							
			}else if($items['ID_TIPO_CAMPO']==11){				
				$items['INPUT']		 = '<select id="'.$inputName.'" name="'.$inputName.'" '.$sAction.' class="">';
				$items['INPUT']		 .= '<option value="">Seleccionar una opci&oacute;n</option>';								
				if(isset($items['OPCIONES_QUERY'])){
					if($items['QUERY_DEPENDENCIES']==1 && $this->_idUpdate>0 || @$items['REVALIDATE']==true){
						$sQueryReplace 	  = str_ireplace($items['REPLACE_QUERY'],$valueInput, $items['OPCIONES_QUERY']);
						$sQueryReplace 	  = str_ireplace('$inputEmpresa',$inputEmpresa,$sQueryReplace);
						$sQueryReplace 	  = str_ireplace('$inputSucursal',$inputSucursal,$sQueryReplace);
						$aDataSelect      = $mFunctions->executeQuery($sQueryReplace);
						$items['INPUT']  .= $cFunctions->selectDb($aDataSelect,$valueInput);
					}elseif($items['QUERY_DEPENDENCIES']==0){						
						$sQueryReplace 	  = str_ireplace('$inputEmpresa' ,$inputEmpresa,$items['OPCIONES_QUERY']);
						$sQueryReplace 	  = str_ireplace('$inputSucursal',$inputSucursal,$items['OPCIONES_QUERY']);	
						$aDataSelect      = $mFunctions->executeQuery($sQueryReplace);
						$items['INPUT']  .= $cFunctions->selectDb($aDataSelect,$valueInput);
					}		
				}else{
					$items['INPUT']  .= '';
				}			
				$items['INPUT']		.= '</select>';		
			}else if($items['ID_TIPO_CAMPO']==12){
				$items['INPUT']		 = '<input id="'.$inputName.'" name="'.$inputName.'" type="password" class="input-inline form-control"  value="'.$valueInput.'"  autocomplete="off"  '.$sAction.'>';
			}else if($items['ID_TIPO_CAMPO']==13){
				$items['INPUT']		 = '<input id="'.$inputName.'" name="'.$inputName.'" type="hidden" class="input-inline form-control"  value="'.$valueInput.'"  autocomplete="off" '.$sAction.'>';
			}
			
			
			/*
		<?php elseif($items['ID_TIPO_CAMPO']==2):?>
			<select id="input<?php echo $items['INPUT_NAME'];?>" name="input<?php echo $items['INPUT_NAME'];?>" <?php echo (isset($items['ACTION'])) ? $items['ACTION'] : '' ;?> >
				<option value="-1">Seleccionar una Opci—n</option>
		
			</select>
		<?php elseif($items['ID_TIPO_CAMPO']==3):?>
		
		<?php elseif($items['ID_TIPO_CAMPO']==4):?>
		
		<?php elseif($items['ID_TIPO_CAMPO']==5):?>
		
		<?php elseif($items['ID_TIPO_CAMPO']==6):?>
		
		<?php elseif($items['ID_TIPO_CAMPO']==7):?>
		<?php elseif($items['ID_TIPO_CAMPO']==8):?>
		<?php elseif($items['ID_TIPO_CAMPO']==9):?>
		<?php elseif($items['ID_TIPO_CAMPO']==10):?>
		<?php elseif($items['ID_TIPO_CAMPO']==11):?>
		
		<?php endif;?>
		*/
			$aResultFields[] = $items;
		}
		//Zend_Debug::dump($aResultFields);
		return $aResultFields;
	} 

	public function validateFields($aFields){
		$aResultValidate = Array();				
		$mFunctions 	 = new My_Model_Functions();
		
		foreach($aFields as $key => $items){
			$inputName 	= 'input'.$items['INPUT_NAME'];
			$sQuery 	= '';
			if($items['VALIDACION_QUERY']!=""){
				$sQuery = str_ireplace('$idObject',$this->_idUpdate,$items['VALIDACION_QUERY']);
				$sQuery = str_ireplace('$inputEmpresa',$this->_dataUser['ID_EMPRESA'],$sQuery);
				$sQuery = str_ireplace('$'.$inputName,$this->_dataIn[$inputName],$sQuery);
				$qExecuteSql = $mFunctions->countResult($sQuery);
				if($qExecuteSql){
					$aItemsError = Array();
					$aItemsError['inputName'] 	 = $inputName;
					$aItemsError['bValidate']	 = 'error';
					$aItemsError['MessageError'] = $items['VALIDACION_MENSAJE'];					
					$aResultValidate[] = $aItemsError;
				}
			}
		}
		return $aResultValidate;
	}
	
    public function setValuesFieldsIn($aFields,$aDataIn){    	
		$cFunctions    = new My_Controller_Functions();
		$mFunctions    = new My_Model_Functions();
		$aResultFields = Array();
		
		foreach($aFields as $key => $itemsFields){
			if(!in_array($itemsFields['ID_TIPO_CAMPO'],$this->_fieldsNoValue)){
				$inputName 	= 'input'.$itemsFields['INPUT_NAME'];	
				$itemsFields['VALUE_INPUT']   = $aDataIn[$inputName];
				$itemsFields['REVALIDATE']    = true;
				$aResultFields[] = $itemsFields;	
			}
		}
		
		return $aResultFields;	
    }	
}