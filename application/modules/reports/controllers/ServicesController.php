<?php
class reports_ServicesController extends My_Controller_Action
{
	/**
	 * Clave principal para identificar el modulo
	 * @var $_clase String
	 */
	protected $_clase 	  = 'mrservices';
	protected $_keyModule = '';
	public 	  $aDbManInfo = Array();
	public $realPath='/var/www/vhosts/angeles/htdocs/public';
	//public $realPath='/Users/itecno2/Documents/workspace/angeles.verdes.mx/public';	
	
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
    
    public function indexAction(){
    	try{
    		$cInstalacion 	 = new My_Model_Cinstalaciones();
    		$cFunciones		 = new My_Controller_Functions(); 
    		$cServicios		 = new My_Model_Servicios();
    		$cUsuarios		 = new My_Model_Usuarios();
    		$cUnidades		 = new My_Model_Unidades();
    		
    		$aUnidades		 = Array();
    		$aUsuarios		 = Array();
    		   		
    		$aInstalaciones	 = $cInstalacion->getCbo($this->_dataUser['ID_EMPRESA'],$this->_dataUser['TIPO_USUARIO']);
    		$sFechaIn		 = date("Y-m-d");
    		$sFechaFin		 = date("Y-m-d");
    		$iSucursal		 = -1;
    		$iUsuario		 = -1;
    		$iUnidad		 = -1;
    		
    		if(isset($this->_dataIn['cboInstalacion']) && isset($this->_dataIn['inputFechaIn']) && 
    		   isset($this->_dataIn['inputFechaFin']) ){
    			$sFechaIn	= $this->_dataIn['inputFechaIn'];
    			$sFechaFin	= $this->_dataIn['inputFechaFin'];
    			$iSucursal	= $this->_dataIn['cboInstalacion'];    			
    			$iUnidad	= $this->_dataIn['inputUnidad'];
				$iUsuario   = $this->_dataIn['inputUsuario'];
    			
    			$aUnidades  = $cUnidades->getCbo($iSucursal);
    			$aUsuarios  = $cUsuarios->getCbo($iUsuario);
    		}else{
    			$this->_dataIn['inputFechaIn']   = $sFechaIn;
    			$this->_dataIn['inputFechaFin']  = $sFechaFin;
    			$this->_dataIn['cboInstalacion'] = -1;
    		}
    		
    		$aDataServicios		   = $cServicios->getServicios($iSucursal,$sFechaIn,$sFechaFin,$iUsuario);
    		    		
    		$this->view->aData	   = $aDataServicios;    		
    		$this->view->cInstalaciones = $cFunciones->selectdb($aInstalaciones,$iSucursal);
    		$this->view->data	   = $this->_dataIn; 
    		$this->view->aUnidades = $cFunciones->selectdb($aUnidades,$iUnidad);
    		$this->view->ausuarios = $cFunciones->selectdb($aUsuarios,$iUsuario); 
    		$this->view->iUsuario  = $iUsuario;
    		$this->view->iUnidad   = $iUnidad;     		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
    }
    
	public function exportdataAction(){
		try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();			
			$cServicios		 = new My_Model_Servicios();
				   
		    if(isset($this->_dataIn['cboInstalacion']) && isset($this->_dataIn['inputFechaIn']) && 
    		   isset($this->_dataIn['inputFechaFin']) ){
    			$sFechaIn	= $this->_dataIn['inputFechaIn'];
    			$sFechaFin	= $this->_dataIn['inputFechaFin'];
    			$iSucursal	= $this->_dataIn['cboInstalacion'];
				$iUnidad	= $this->_dataIn['inputUnidad'];
				$iUsuario   = $this->_dataIn['inputUsuario'];
    			
    			$aDataServicios		= $cServicios->getServicios($iSucursal,$sFechaIn,$sFechaFin,$iUsuario);
    			
    			if(count($aDataServicios)>0){
    				/** PHPExcel */ 
					require_once 'PHPExcel.php';
					
					/** PHPExcel_Writer_Excel2007*/ 								
					$objPHPExcel = new PHPExcel();
	 					
					$objPHPExcel->getProperties()->setCreator("UDA")
											 ->setLastModifiedBy("UDA")
											 ->setTitle("Office 2007 XLSX")
											 ->setSubject("Office 2007 XLSX")
											 ->setDescription("Reporte del Viaje")
											 ->setKeywords("office 2007 openxml php")
											 ->setCategory("Reporte del Viaje");
					
					$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
					$stylezebraTable = new PHPExcel_Style();
					$stylezebraTable->applyFromArray(array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '04B45F')
						)
					));					
    				
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Jefatura');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', 'Unidad');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'Usuario');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', 'Tecnico 2');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', 'Fecha');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', 'Latitud');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'Longitud');
					
					$aElementos = $cServicios->getElementos(2);				
					$iControl   = 7;
					
    				foreach($aElementos as $key => $items){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($iControl,  1, $items['DESCIPCION']);
						$iControl++;
					}
					
					$rowControl		= 2;
					foreach($aDataServicios as $key => $itemServ){
						$results = explode("|",$itemServ['N_UNIDAD']);
						
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControl), $itemServ['N_SUCURSAL']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), @$results[0]);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $itemServ['N_USUARIO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), @$results[1]);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControl), $itemServ['FECHA_CAPTURA_EQUIPO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControl), $itemServ['LATITUD']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControl), $itemServ['LONGITUD']);
						
						$controlColumn = 7;
						$aDataResult = $cServicios->getResultados($itemServ['ID_RESULTADO']);
						if(count($aDataResult)>0){
							foreach($aDataResult as $key => $itemsResult){
								$subFijo = '';								
								if($itemsResult['ID_TIPO']==9 || $itemsResult['ID_TIPO']==10 || $itemsResult['ID_TIPO']==11){
									$subFijo = 'http://201.131.96.62/evidencia/';
										
									$objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($controlColumn,  ($rowControl))->getHyperlink()->setUrl($subFijo.$itemsResult['CONTESTACION']);
								}
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($controlColumn,  ($rowControl), $subFijo.$itemsResult['CONTESTACION']);
									
								$controlColumn++;	
							}
						}
						$rowControl++;
					}	
										
					$filename  = "Reporte_Servicios_".date("YmdHi").".xlsx";	
	
					header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
					header("Content-type:   application/x-msexcel; charset=utf-8");
					header("Content-Disposition: attachment; filename=$filename"); 
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: private",false);
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');
    			}else{
    				echo "No hay información";	
    			}
    		}else{
    			echo "No hay información";
    		}	
			
		   	 	
		}catch(Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
		}	
    }
    
	public function mapubicacionAction(){
		try{			
			$this->view->layout()->setLayout('layout_blank');			    
        	$adataService  = Array();
        	
        	if(isset($this->_dataIn['strInput']) && $this->_dataIn['strInput']!=""){
        		$idResult   = $this->_dataIn['strInput'];
        		$cServicios = new My_Model_Servicios();        		
        		$adataService = $cServicios->getDataServicio($idResult);
        	}
        	
			$this->view->dataService = $adataService;
			$this->view->data		 = $this->_dataIn;
		}catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
	}   

	
	public function exportkmlAction(){
		try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();			
			$cServicios		 = new My_Model_Servicios();
				   
		    if(isset($this->_dataIn['cboInstalacion']) && isset($this->_dataIn['inputFechaIn']) && 
    		   isset($this->_dataIn['inputFechaFin']) ){
    			$sFechaIn	= $this->_dataIn['inputFechaIn'];
    			$sFechaFin	= $this->_dataIn['inputFechaFin'];
    			$iSucursal	= $this->_dataIn['cboInstalacion'];
				$iUnidad	= $this->_dataIn['inputUnidad'];
				$iUsuario   = $this->_dataIn['inputUsuario'];
    			
    			$aDataServicios		= $cServicios->getServicios($iSucursal,$sFechaIn,$sFechaFin,$iUsuario);    			
    			if(count($aDataServicios)>0){			
    				
					include_once($this->realPath.'/kmlcreator/kml.class.php');
					$kml = new KML('Recorrido Historico');
					
					$document = new KMLDocument('Recorrido', 'Reporte');    				
    				
					$style = new KMLStyle('boatStyle');
					$style->setIconStyle($this->realPath.'/kmlcreator/images/fish.png', 'ffffffff', 'normal', 1);
					$style->setLineStyle('ffffffff', 'normal', 2);
					$document->addStyle($style);
					
					$style = new KMLStyle('navintStyle');
					$style->setIconStyle($this->realPath.'/kmlcreator/images/navint.png', 'ffffffff', 'normal', 1);
					$style->setLineStyle('ff0000ff', 'normal', 3);
					$document->addStyle($style);
					
					$style = new KMLStyle('plotStyle');
					$style->setIconStyle($this->realPath.'/kmlcreator/images/small.png', 'ff00ff00', 'normal', 0.2);
					$document->addStyle($style);
					
					$style = new KMLStyle('portStyle');
					$style->setIconStyle($this->realPath.'/kmlcreator/images/port.png');
					$document->addStyle($style);
					
					$style = new KMLStyle('polyStyle');
					$style->setPolyStyle('660000ff');
					$document->addStyle($style);
					
					/**
					  * File adds
					  */
					$kml->addFile($this->realPath.'/kmlcreator/images/navint.png', $this->realPath.'/kmlcreator/images/navint.png');
					$kml->addFile($this->realPath.'/kmlcreator/images/icone.png', $this->realPath.'/kmlcreator/images/icone.png');
					$kml->addFile($this->realPath.'/kmlcreator/images/small.png', $this->realPath.'/kmlcreator/images/small.png');
					$kml->addFile($this->realPath.'/kmlcreator/images/fish.png', $this->realPath.'/kmlcreator/images/fish.png');
					$kml->addFile($this->realPath.'/kmlcreator/images/port.png', $this->realPath.'/kmlcreator/images/port.png');
										
					$boatListFolder = new KMLFolder('', 'Actividades');
					$iControl = 0;
					$dateIn   = '';
					$dateFin  = '';
					$apolyLine = Array();
					foreach($aDataServicios as $items){
						$results = explode("|",$items['N_UNIDAD']);
						
						if(isset($items['LONGITUD']) && $items['LONGITUD']!="" &&
						   isset($items['LATITUD'])  && $items['LATITUD'] !=""){
								if($iControl==0){
									$dateIn = $items['FECHA_CAPTURA_EQUIPO'];
									$iControl=0;
								}
								$boatFollow = new KMLPlaceMark($iControl, @$results[0].'-'.@$items['N_ACTIVIDAD'], true);
								$boatFollow->setGeometry(new KMLPoint($items['LONGITUD'], $items['LATITUD'], 0));
								//$boatFollow->setStyleUrl('#plotStyle');
								$boatFollow->setTimePrimitive(new KMLTimeStamp('',$items['FECHA_CAPTURA_EQUIPO']));
								$boatListFolder->addFeature($boatFollow);
								$dateFin  = $items['FECHA_CAPTURA_EQUIPO'];					   	
						   }						
					}					
					$document->addFeature($boatListFolder);			
					$kml->setFeature($document);
					
					$nameFile  = "Reporte_Actividades_".date("YmdHi").".kml";
					$kml->output('A',$nameFile);
    			}else{
    				echo "No hay información";	
    			}
    		}else{
    			echo "No hay información";
    		}
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }          		
	}	
	
	
}    
