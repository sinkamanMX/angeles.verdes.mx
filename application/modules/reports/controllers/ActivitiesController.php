<?php
class reports_ActivitiesController extends My_Controller_Action
{
	/**
	 * Clave principal para identificar el modulo
	 * @var $_clase String
	 */
	protected $_clase 	  = 'mlognooks';
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
    		   		
    		$aInstalaciones	 = $cInstalacion->getCbo($this->_dataUser['ID_EMPRESA']);
    		$sFechaIn		 = date("Y-m-d");
    		$sFechaFin		 = date("Y-m-d");
    		$iSucursal		 = -1;
    		
    		if(isset($this->_dataIn['cboInstalacion']) && isset($this->_dataIn['inputFechaIn']) && 
    		   isset($this->_dataIn['inputFechaFin']) ){
    			$sFechaIn	= $this->_dataIn['inputFechaIn'];
    			$sFechaFin	= $this->_dataIn['inputFechaFin'];
    			$iSucursal	= $this->_dataIn['cboInstalacion'];
    		}else{
    			$this->_dataIn['cboInstalacion'] = -1;
    		}
    		
    		$aDataServicios		= $cServicios->getActividades($iSucursal,$sFechaIn,$sFechaFin);    		
    		$this->view->aData	= $aDataServicios;    		
    		$this->view->cInstalaciones = $cFunciones->selectdb($aInstalaciones);
    		$this->view->data			= $this->_dataIn;    		
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
    			
    			$aDataServicios		= $cServicios->getActividades($iSucursal,$sFechaIn,$sFechaFin);
    			
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
    				
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Centro');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', 'Fecha');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'Usuario');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', 'Lugar de Servicio');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A1:J1');
					$aElementos = $cServicios->getElementos(3);				
					$iControl   = 4;	
					
					foreach($aElementos as $key => $items){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($iControl,  1, $items['DESCIPCION']);
						$iControl++;
					}
					
					$rowControl		= 2;
					foreach($aDataServicios as $key => $itemServ){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControl), $itemServ['N_SUCURSAL']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $itemServ['FECHA_CAPTURA_EQUIPO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $itemServ['USUARIO']);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $itemServ['UBICACION']);
						
						$controlColumn = 4;
						$aDataResult = $cServicios->getResultados($itemServ['ID_RESULTADO']);
						if(count($aDataResult)>0){
							foreach($aDataResult as $key => $itemsResult){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($controlColumn,  ($rowControl), $itemsResult['CONTESTACION']);
								$controlColumn++;	
							}
						}
						$rowControl++;
					}
										
					$filename  = "Reporte_Actividades_".date("YmdHi").".xlsx";	
	
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
}