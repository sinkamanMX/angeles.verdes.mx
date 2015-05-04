<?php

class atn_ServicesController extends My_Controller_Action
{	
	protected $_clase = 'mservices';
	public $dataIn;	
	public $aService;
	public $realPath='/var/www/vhosts/sima/htdocs/public';
	//public $realPath='/Users/itecno2/Documents/workspace/productividad.mx/public';
		
    public function init()
    {
    	try{	
			$sessions = new My_Controller_Auth();
			$perfiles = new My_Model_Perfiles();
	        if(!$sessions->validateSession()){
	            $this->_redirect('/');		
			}
			
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
			$cTecnicos		= new My_Model_Tecnicos();			
			$cCitas			= new My_Model_Citas();
			
			$aSucursales 	= "";
			$idSucursal		= -1;
			$idTecnico		= '';			
			$dFechaIn		= '';
			$dFechaFin		= '';
			$bShowUsers		= false;
			$aTypeSearch	= Array(		
								array("id"=>"1",'name'=>'Fecha inicio programada' ),
								array("id"=>"2",'name'=>'Fecha inicio real ' )    );
			$bType 			= 1;
			$bStatus		= -1;
			
			$dataCenter		= $cInstalaciones->getCbo($this->view->dataUser['ID_EMPRESA']);			
			$aTecnicos      = $cTecnicos->getAll($this->view->dataUser['ID_EMPRESA'],1);
			$aSucursales	= $cInstalaciones->getList($this->view->dataUser['ID_EMPRESA']);			
			
			if(isset($this->dataIn['optReg']) && $this->dataIn['optReg']){
				$dFechaIn	= $this->dataIn['inputFechaIn'];
				$dFechaFin	= $this->dataIn['inputFechaFin'];
				
				if(isset($this->dataIn['cboInstalacion']) && $this->dataIn['cboInstalacion']>0){
					$aSucursales	= $this->dataIn['cboInstalacion'];
					$idSucursal		= $this->dataIn['cboInstalacion'];	
				}
				
				$idTecnico	= $this->dataIn['inputTecnicos'];
				$bType		= $this->dataIn['cboTypeSearch'];
				$bStatus	= $this->dataIn['inputStatus'];				
				$bShowUsers=true;
			}else{
				$dFechaIn	= Date('Y-m-d');
				$dFechaFin	= Date('Y-m-d');
				$bShowUsers=true;
				$idSucursal		= "";	
			}			
			
			$aTecnicos 		= $cTecnicos->getTecnicosBySucursal($aSucursales);
			$dataResume     = $cCitas->getResumeByDay($aSucursales,$dFechaIn,$dFechaFin,$idTecnico,$bType);
			$dataProcess	= $cFunciones->setResume($dataResume);
						
			$this->view->cInstalaciones 	= $cFunciones->selectDb($dataCenter,$idSucursal);
			$this->view->aTecnicos 			= $cFunciones->selectDb($aTecnicos,$idTecnico);	
			$this->view->aTypeSearchs		= $cFunciones->cbo_from_array($aTypeSearch,$bType);
			$this->view->data 				= $this->dataIn;
			$this->view->dataResume 	 	= $dataProcess;
			$this->view->dataResumeTotal 	= $dataProcess['TOTAL'];
			$this->view->showUsers			= $bShowUsers;
			$this->view->aResume 			= $dataResume;
			$this->view->iStatus			= $bStatus;
			
			unset($this->view->dataResume['TOTAL']);
				
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function getlastpAction(){
    	$result = '';
		try{  
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			
			if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']){
				$cTecnicos  = new My_Model_Tecnicos();				
				$allInputs  = implode(',', $this->dataIn['strInput']);				
				$dataPos    = $cTecnicos->getLastPositions($allInputs);		
				foreach ($dataPos as $key => $items){
					if($items['ID']!="" && $items['ID']!="NULL")
					$result .= ($result!="") ? "!" : "";
					$result .=  $items['ID']."|".
								$items['FECHA_GPS']."|".
                                $items['EVENTO']."|".
                                $items['LATITUD']."|".
                                $items['LONGITUD']."|".
                                round($items['VELOCIDAD'],2)."|".
                                round($items['NIVEL_BATERIA'],2)."|".
                                $items['TIPO_GPS']."|".
                                $items['ANGULO']."|".
                                $items['UBICACION'];
				}
			}
			echo $result;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }     	
    }
    
    public function getinformationAction(){
		$this->view->layout()->setLayout('blank');
		
		if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']){
			$cTecnicos		= new My_Model_Tecnicos();
			$cFunctions 	= new My_Controller_Functions();
			$cCitas			= new My_Model_Citas();
			$dToday			= Date("Y-m-d");
			$dToday			= '2014-08-20';
			
			$aTecnicos 		= $cTecnicos->getTecnicosBySucursal($this->dataIn['strInput']);
			$this->view->aTecnicos = $cFunctions->selectDb($aTecnicos);
			
			$dataResume     = $cCitas->getResumeByDay($this->dataIn['strInput'],$dToday);
			$dataProcess	= $cFunctions->setResume($dataResume);
			$this->view->dataResume 	 = $dataProcess;
			$this->view->dataResumeTotal = $dataProcess['TOTAL'];
			unset($this->view->dataResume['TOTAL']);
		}
    	
    	$this->view->data = $this->dataIn;
    }
    
	public function exportpdfsbackAction(){
		try{   			
			$aDataForms;
			$dataCita;
			$validate=0;
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$cCitas = new My_Model_Citas();
			
			if(isset($this->dataIn['strInput'])  	 && $this->dataIn['strInput']!=""){
				$dataCita = $cCitas->getDataRep($this->dataIn['strInput']);
				
				if(count($dataCita)>0){
					/** PHPExcel */ 
					include 'PHPExcel.php';
					
					/** PHPExcel_Writer_Excel2007*/ 
					/*include 'PHPExcel/Autoloader.php';
					include 'PHPExcel/Writer/Excel2007.php';
					include 'PHPExcel/Writer/PDF.php';		*/			
					$objPHPExcel = new PHPExcel();
					$objPHPExcel->getProperties()->setCreator("UDA")
											 ->setLastModifiedBy("UDA")
											 ->setTitle("Office 2007 XLSX")
											 ->setSubject("Office 2007 XLSX")
											 ->setDescription("Reporte del Viaje")
											 ->setKeywords("office 2007 openxml php")
											 ->setCategory("Reporte del Viaje");
					
											 
					$styleHeader = new PHPExcel_Style();
					$styleAutor	 = new PHPExcel_Style();
					$styleTittle = new PHPExcel_Style();
					$styleHeadermin = new PHPExcel_Style();
		
					$styleHeader->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => '000000')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'size'  => 15,
					        'name'  => 'Arial'
					    )
					));
					$styleHeadermin->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => '000000')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'size'  => 15,
					        'name'  => 'Arial'
					    )
					));					
					$styleAutor->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => '6E6E6E')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'name'  => 'Arial'
					    )
					));
					
					$styleTittle->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'BDBDBD')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'name'  => 'Arial'
					    )
					));					
				
					/**
					 * Header del Reporte
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', 'ORDEN DE INSTALACION');
					$objPHPExcel->getActiveSheet()->mergeCells('B2:F2');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeader, 'B2:F2');
					$objPHPExcel->getActiveSheet()->getStyle('B2:F2')
									->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', utf8_decode('Tracking Systems de Mexico, S.A de C.V.'));
					$objPHPExcel->getActiveSheet()->mergeCells('B3:F3');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleAutor, 'B3:F3');
					$objPHPExcel->getActiveSheet()->getStyle('B3:F3')
									->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', 'Folio:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C5', $dataCita['FOLIO']);

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B6', 'Fecha:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', $dataCita['FECHA_CITA']);					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', 'Horario de la cita:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', $dataCita['HORA_CITA']);
					$objPHPExcel->getActiveSheet()->mergeCells('C7:F7');					
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B8', 'Registrada por:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C8', $dataCita['USR_REGISTRADO']);	
					$objPHPExcel->getActiveSheet()->mergeCells('C8:F8');										

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', 'Cliente:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C9', $dataCita['NOMBRE_CLIENTE']);
					$objPHPExcel->getActiveSheet()->mergeCells('C9:F9');						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B10', 'Domicilio del cliente');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C10', $dataCita['DIRECCION_CLIENTE1']);
					$objPHPExcel->getActiveSheet()->mergeCells('C10:F10');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C11', $dataCita['DIRECCION_CLIENTE2']);
					$objPHPExcel->getActiveSheet()->mergeCells('C11:F11');											
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B12', 'Domicilio de instalacion:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C12', $dataCita['DIRECCION_CITA1']);
					$objPHPExcel->getActiveSheet()->mergeCells('C12:F12');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C13', $dataCita['DIRECCION_CITA2']);
					$objPHPExcel->getActiveSheet()->mergeCells('C13:F13');					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B14', utf8_encode('Personal asignado:'));
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C14', $dataCita['NOMBRE_TECNICO']);
					$objPHPExcel->getActiveSheet()->mergeCells('C14:F14');						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B15', 'Inicio de instalacion:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C15', $dataCita['FECHA_INICIO']);
					$objPHPExcel->getActiveSheet()->mergeCells('C15:F15');						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B16', 'Fin de instalacion:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C16', $dataCita['FECHA_TERMINO']);	
					$objPHPExcel->getActiveSheet()->mergeCells('C16:F16');	

					$rowControl		= 18;
					$zebraControl  	= 0;							
					
					$aForms = $cCitas->getFormsCita($this->dataIn['strInput']);					
					foreach($aForms as $key => $itemsForm){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $itemsForm['TITULO']);
						$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':F'.$rowControl);
						$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeadermin, 'B'.$rowControl.':F'.$rowControl);						
						$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':F'.$rowControl)
									->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);													
																
						$rowControl++;	
						$aDataForms = $cCitas->getDataSendbyForms($this->dataIn['strInput'],$itemsForm['ID_FORMULARIO']);
						
						foreach($aDataForms as $items){							
							if($items['TIPO']=='ENCABEZADO'){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $items['DESCRIPCION']);
								$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':F'.$rowControl);
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleTittle, 'B'.$rowControl.':F'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':F'.$rowControl)
												->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);													
								//Falta aplicar el estilo								
							
							}else{		
								/*------ La respuesta es una foto ------*/						
								if($items['T_ELEMENTO']=='9' || $items['T_ELEMENTO']=='10'){									
									$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $items['DESCRIPCION']);
									$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':F'.$rowControl)
										->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
									$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':F'.$rowControl);	
									$rowControl++;					
									
									$objDrawing = new PHPExcel_Worksheet_Drawing();
									
									$objDrawing->setName('Picture1');
									$objDrawing->setDescription('Picture1');
									
									$objDrawing->setPath($this->realPath.$items['CONTESTACION']);
									$objDrawing->setHeight(120);
									$objDrawing->setWidth(120);
									$objDrawing->setOffsetX(120);
									$objDrawing->setResizeProportional(true);
									$objDrawing->setCoordinates('B'.$rowControl);
									
									$objPHPExcel->getActiveSheet()->getRowDimension('B'.$rowControl)->setRowHeight(150);
									$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':F'.$rowControl)
										->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
									$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':F'.$rowControl);
									if($items['T_ELEMENTO']=='10'){
										$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(60);	
									}else{
										$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(200);
									}
									
									$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));
									
								/*------ La respuesta es texto    ------*/	
								}else{
									$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $items['DESCRIPCION']);
									$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $items['CONTESTACION']);								
									$objPHPExcel->getActiveSheet()->mergeCells('D'.$rowControl.':F'.$rowControl);
																		
								}
								$rowControl++;
							}					
						}						
					}
					
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);					
					
					$filename  = "Reporte_Cita_".date("YmdHi").".xlsx";			
					
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');			
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');
				}
			}
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }   
	}

	public function exportsearchAction(){
		try{   						
			$aDataForms;
			$dataCita;
			$validate=0;
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$cCitas = new My_Model_Citas();
			
			if(isset($this->dataIn['strInput'])  	 && $this->dataIn['strInput']!=""){
				$dataCita = $cCitas->getDataRep($this->dataIn['strInput']);
				
				if(count($dataCita)>0){			
				/** PHPExcel */ 
				require_once 'PHPExcel.php';		
										
				if (!PHPExcel_Settings::setPdfRenderer(
						PHPExcel_Settings::PDF_RENDERER_DOMPDF,
						$this->realPath.'/PHPExcel/Classes/dompdf'
				)) {
					die(
						'NOTICE: Please set the $rendererName and asdads$rendererLibraryPath values' .
						'<br />' .
						'at the top of this script as appropriate for your directory structure'
					);
				}
					/** PHPExcel_Writer_Excel2007*/ 								
					$objPHPExcel = new PHPExcel();
	 					
					$objPHPExcel->getProperties()->setCreator("UDA")
											 ->setLastModifiedBy("UDA")
											 ->setTitle("Office 2007 XLSX")
											 ->setSubject("Office 2007 XLSX")
											 ->setDescription("Reporte del Viaje")
											 ->setKeywords("office 2007 openxml php")
											 ->setCategory("Reporte del Viaje");
					
											 
					$styleHeader = new PHPExcel_Style();
					$styleAutor	 = new PHPExcel_Style();
					$styleTittle = new PHPExcel_Style();
					$styleHeadermin = new PHPExcel_Style();
					$allBlank	 = new PHPExcel_Style(); 
					
					$allBlank->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )				        
					));					
		
					$styleHeader->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => '000000')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'size'  => 15,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));
					$styleHeadermin->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => '000000')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'size'  => 15,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));					
					$styleAutor->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => '6E6E6E')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));
					
					$styleTittle->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'BDBDBD')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));					
				
					/**
					 * Header del Reporte
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($allBlank, 'A1:Z99');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', 'ORDEN DE INSTALACION');
					$objPHPExcel->getActiveSheet()->mergeCells('C2:H2');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeader, 'C2:H2');
					$objPHPExcel->getActiveSheet()->getStyle('C2:H2')
									->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', utf8_decode('Tracking Systems de Mexico, S.A de C.V.'));
					$objPHPExcel->getActiveSheet()->mergeCells('C3:H3');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleAutor, 'C3:H3');
					$objPHPExcel->getActiveSheet()->getStyle('C3:H3')
									->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C5', 'Orden de servicio:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D5', $dataCita['ID']);

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', 'Fecha:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D6', $dataCita['FECHA_CITA']);					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', 'Horario de la cita:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D7', $dataCita['HORA_CITA']);
					$objPHPExcel->getActiveSheet()->mergeCells('D7:H7');					
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C8', 'Registrada por:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D8', $dataCita['USR_REGISTRADO']);	
					$objPHPExcel->getActiveSheet()->mergeCells('D8:H8');										

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C9', 'Cliente:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D9', $dataCita['NOMBRE_CLIENTE']);
					$objPHPExcel->getActiveSheet()->mergeCells('D9:H9');						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C10', 'Domicilio del cliente');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D10', $dataCita['DIRECCION_CLIENTE1']);
					$objPHPExcel->getActiveSheet()->mergeCells('D10:H10');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D11', $dataCita['DIRECCION_CLIENTE2']);
					$objPHPExcel->getActiveSheet()->mergeCells('D11:H11');											
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C12', 'Domicilio de instalacion:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D12', $dataCita['DIRECCION_CITA1']);
					$objPHPExcel->getActiveSheet()->mergeCells('D12:H12');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D13', $dataCita['DIRECCION_CITA2']);
					$objPHPExcel->getActiveSheet()->mergeCells('D13:H13');					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C14', utf8_encode('Personal asignado:'));
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D14', $dataCita['NOMBRE_TECNICO']);
					$objPHPExcel->getActiveSheet()->mergeCells('D14:H14');						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C15', 'Inicio de instalacion:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D15', $dataCita['FECHA_INICIO']);
					$objPHPExcel->getActiveSheet()->mergeCells('D15:H15');						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C16', 'Fin de instalacion:');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D16', $dataCita['FECHA_TERMINO']);	
					$objPHPExcel->getActiveSheet()->mergeCells('D16:H16');	
					
					$objPHPExcel->getActiveSheet()->mergeCells('A17:H17');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($allBlank, 'A17:H17');					

					$rowControl		= 18;
					$zebraControl  	= 0;							
					
					$aForms = $cCitas->getFormsCita($this->dataIn['strInput']);					
					foreach($aForms as $key => $itemsForm){
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$rowControl.':B'.$rowControl);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $itemsForm['TITULO']);
						$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':H'.$rowControl);
						$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleHeadermin, 'C'.$rowControl.':H'.$rowControl);						
						$objPHPExcel->getActiveSheet()->getStyle('C'.$rowControl.':H'.$rowControl)
									->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);													
																
						$rowControl++;	
						$aDataForms = $cCitas->getDataSendbyForms($this->dataIn['strInput'],$itemsForm['ID_FORMULARIO']);
						
						foreach($aDataForms as $items){												
							if($items['TIPO']=='ENCABEZADO'){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['DESCRIPCION']);
								$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':H'.$rowControl);
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($styleTittle, 'C'.$rowControl.':H'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('C'.$rowControl.':H'.$rowControl)
												->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$rowControl++;									
							}else{		
								/*------ La respuesta es una foto ------*/						
								if($items['T_ELEMENTO']=='9' || $items['T_ELEMENTO']=='10'){									
									$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['DESCRIPCION']);
									$objPHPExcel->getActiveSheet()->getStyle('C'.$rowControl.':H'.$rowControl)
										->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
									$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':H'.$rowControl);	
									$rowControl++;		
									
									$exist_file = file_exists($this->realPath.$items['CONTESTACION']); 

									if ($exist_file== true && $items['CONTESTACION']!="") {
										$objDrawing = new PHPExcel_Worksheet_Drawing();
										
										$objDrawing->setName('Picture1');
										$objDrawing->setDescription('Picture1');
										
										$objDrawing->setPath($this->realPath.$items['CONTESTACION']);
										$objDrawing->setWidth(120);
										$objDrawing->setOffsetX(150);
										$objDrawing->setHeight(135);
										$objDrawing->setOffsetY(-160);

										$objDrawing->setCoordinates('C'.$rowControl);
										
										$objPHPExcel->getActiveSheet()->getRowDimension('C'.$rowControl)->setRowHeight(150);
										$objPHPExcel->getActiveSheet()->getStyle('C'.$rowControl.':H'.$rowControl)
											->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
										$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':H'.$rowControl);
										
										if($items['T_ELEMENTO']=='10'){
											//$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(60);	
										}else{
											//$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(150);
										}
										
										$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(140);										
										$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
									}else{
										$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), "Imagen no disponible.");								
										$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':H'.$rowControl);										
									}
									
								/*------ La respuesta es texto    ------*/	
								}else{
									$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['DESCRIPCION']);
									$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':C'.$rowControl);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $items['CONTESTACION']);								
									$objPHPExcel->getActiveSheet()->mergeCells('D'.$rowControl.':H'.$rowControl);
																		
								}
								$rowControl++;
							}					
						}						
					}
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(5);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(5);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);	

					$objPHPExcel->setActiveSheetIndex(0)->setShowGridLines(true);
					$objPHPExcel->setActiveSheetIndex(0)->setPrintGridLines(true);					
				
					$filename  = "Reporte_Cita_".date("YmdHi").".pdf";
	
					// Redirect output to a clientÍs web browser (PDF)
					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');									
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
					$objWriter->save('php://output');
				/*			
					$filename  = "Reporte_Cita_".date("YmdHi").".xlsx";
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');			
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');
				*/				
				}
			}
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    
	}

	public function exportallAction(){
	    try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();	    	
	    	
			$cInstalaciones = new My_Model_Cinstalaciones();
			$cFunciones		= new My_Controller_Functions();
			$cTecnicos		= new My_Model_Tecnicos();			
			$cCitas			= new My_Model_Citas();
			
			$idSucursal		= -1;
			$idTecnico		= '';			
			$dFechaIn		= '';
			$dFechaFin		= '';
			$bShowUsers		= false;
			$aTypeSearch	= Array(		
								array("id"=>"1",'name'=>'Fecha inicio programada' ),
								array("id"=>"2",'name'=>'Fecha inicio real ' )    );
			$bType 			= 1;
			$bStatus		= -1;	
							
			
			$dataCenter		= $cInstalaciones->getCbo($this->view->dataUser['ID_EMPRESA']);			
			$aTecnicos      = $cTecnicos->getAll($this->view->dataUser['ID_EMPRESA'],1);
			$aSucursales	= $cInstalaciones->getList($this->view->dataUser['ID_EMPRESA']);

			
			$dFechaIn	= $this->dataIn['inputFechaIn'];
			$dFechaFin	= $this->dataIn['inputFechaFin'];
			$idSucursal	= $this->dataIn['cboInstalacion'];			
			$bShowUsers=true;
							
			$bStatus	  = (isset($this->dataIn['inputStatus']) && $this->dataIn['inputStatus']!="") ? $this->dataIn['inputStatus'] : "-1";					
			$aSucursalesIn= (isset($this->dataIn['cboInstalacion']) && $this->dataIn['cboInstalacion']!="") ? $this->dataIn['cboInstalacion'] : $aSucursales;							
			$bType		  = (isset($this->dataIn['cboTypeSearch']) && $this->dataIn['cboTypeSearch']!="")   ? $this->dataIn['cboTypeSearch']  : "1";
			$idTecnico	  = (isset($this->dataIn['inputTecnicos']) && $this->dataIn['inputTecnicos']!="")   ? $this->dataIn['inputTecnicos']  : "";

			$aTecnicos 		= $cTecnicos->getTecnicosBySucursal($aSucursalesIn);
			$dataResume     = $cCitas->getResumeByDay($aSucursalesIn,$dFechaIn,$dFechaFin,$idTecnico,$bType);
			$dataProcess	= $cFunciones->setResume($dataResume);
						
			/*
			$this->view->cInstalaciones 	= $cFunciones->selectDb($dataCenter,$idSucursal);
			$this->view->aTecnicos 			= $cFunciones->selectDb($aTecnicos,$idTecnico);	
			$this->view->data 				= $this->dataIn;
			$this->view->dataResume 	 	= $dataProcess;
			$this->view->dataResumeTotal 	= $dataProcess['TOTAL'];
			$this->view->showUsers			= $bShowUsers;
			$this->view->aResume 			= $dataResume;*/
			
			if(count($dataResume)>0){
				/** PHPExcel */ 
				require_once 'PHPExcel.php';
						
				/*
				 * 
				if (!PHPExcel_Settings::setPdfRenderer(
						PHPExcel_Settings::PDF_RENDERER_DOMPDF,
						$this->realPath.'/PHPExcel/Classes/dompdf'
				)) {
					die(
						'NOTICE: Please set the $rendererName and asdads$rendererLibraryPath values' .
						'<br />' .
						'at the top of this script as appropriate for your directory structure'
					);
				}*/
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
					$sHeaderBig   	 = new PHPExcel_Style();
					$stylezebraTable = new PHPExcel_Style();  
					$sHeaderOrange 	 = new PHPExcel_Style();
					$sTittleTable 	 = new PHPExcel_Style();
							
					$stylezebraTable->applyFromArray(array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('argb' => 'e7f3fc')
						)
					));		

					$sHeaderBig->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => '000000'),
					        'size'  => 16,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )				        
					));			

					$sHeaderOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FF8000'),
					        'size'  => 10,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));		

					$sTittleTable->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FF8000')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'size'  => 10,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));						
				
					/**
					 * Header del Reporte
					 **/					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', utf8_decode('Tracking Systems de Mexico, S.A de C.V.'));
					$objPHPExcel->getActiveSheet()->mergeCells('B3:G3');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderBig, 'B3:J3');
					
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					
					$objDrawing->setName('Logo');
					$objDrawing->setDescription('Logo');
					
					$objDrawing->setPath($this->realPath.'/logoUDA.jpg');
					$objDrawing->setWidth(70);
					$objDrawing->setHeight(90);
					//$objDrawing->setOffsetX(10);
					$objDrawing->setCoordinates('I2');
					
					$objPHPExcel->getActiveSheet()->getRowDimension('I2')->setRowHeight(150);										
					$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', utf8_decode('REPORTE DE CITAS'));
					$objPHPExcel->getActiveSheet()->mergeCells('B5:G5');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderOrange, 'B5:J5');												
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', 'Folio Cita');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', 'Tipo Servicio');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C7', 'Estatus');										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D7', 'Cliente');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E7', 'Fecha Programada');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F7', 'Hora Programada');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G7', 'Hora Inicio');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H7', 'Hora Terminado');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I7', 'Tecnico Asignado');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J7', 'Municipio');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K7', 'CP');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L7', 'Estado');					
					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleTable, 'A7:L7');														
					
					$rowControl		= 8;
					$zebraControl  	= 0;
					
					if($bStatus==-1){
						foreach($dataResume as $key => $items){						
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControl), $items['FOLIO']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $items['N_TIPO']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['DESCRIPCION']);								
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $items['NOMBRE_CLIENTE']);								
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControl), $items['F_PROGRAMADA']);								
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControl), $items['H_PROGRAMADA']);								
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControl), $items['FECHA_INICIO']);								
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,  ($rowControl), $items['FECHA_TERMINO']);								
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,  ($rowControl), $items['NOMBRE_TECNICO']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,  ($rowControl), $items['DIR_MUN']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, ($rowControl), $items['DIR_CP']);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, ($rowControl), $items['DIR_ESTADO']);
	
							if($zebraControl++%2==1){
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControl.':L'.$rowControl);			
							}
							$rowControl++;
						}						
					}else if($bStatus>0){
						foreach($dataResume as $key => $items){
							if($bStatus==$items['IDE']){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,  ($rowControl), $items['FOLIO']);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,  ($rowControl), $items['N_TIPO']);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,  ($rowControl), $items['DESCRIPCION']);								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,  ($rowControl), $items['NOMBRE_CLIENTE']);								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,  ($rowControl), $items['F_PROGRAMADA']);								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,  ($rowControl), $items['H_PROGRAMADA']);								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,  ($rowControl), $items['FECHA_INICIO']);								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,  ($rowControl), $items['FECHA_TERMINO']);								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,  ($rowControl), $items['NOMBRE_TECNICO']);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,  ($rowControl), $items['DIR_MUN']);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, ($rowControl), $items['DIR_CP']);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, ($rowControl), $items['DIR_ESTADO']);
		
								if($zebraControl++%2==1){
									$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($stylezebraTable, 'A'.$rowControl.':L'.$rowControl);			
								}
								$rowControl++;								
							}
						}								
					} 

								
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setAutoSize(true);
					$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setAutoSize(true);			
						
					$filename  = "Reporte_Citas_".date("YmdHi").".xlsx";	
	
					header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
					header("Content-type:   application/x-msexcel; charset=utf-8");
					header("Content-Disposition: attachment; filename=$filename"); 
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: private",false);
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');								
			}else{
				echo "No Hay informaciÑn";
			}
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  	
	}


	public function exportoservicioAction(){
		try{
			$aDataForms;
			$dataCita;
			$validate=0;
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$cCitas = new My_Model_Citas();

			if(isset($this->dataIn['strInput'])  	 && $this->dataIn['strInput']!=""){
				$dataCita = $cCitas->getDataRep($this->dataIn['strInput']);
				
				if(count($dataCita)>0){			
					/** PHPExcel */ 
					require_once 'PHPExcel.php';		
											
					if (!PHPExcel_Settings::setPdfRenderer(
							PHPExcel_Settings::PDF_RENDERER_DOMPDF,
							$this->realPath.'/PHPExcel/Classes/dompdf'
					)) {
						die(
							'NOTICE: Please set the $rendererName and asdads$rendererLibraryPath values' .
							'<br />' .
							'at the top of this script as appropriate for your directory structure'
						);
					}
					
					/** PHPExcel_Writer_Excel2007*/ 								
					$objPHPExcel = new PHPExcel();
	 					
					$objPHPExcel->getProperties()->setCreator("UDA")
											 ->setLastModifiedBy("UDA")
											 ->setTitle("Office 2007 XLSX")
											 ->setSubject("Office 2007 XLSX")
											 ->setDescription("Orden de Servicio")
											 ->setKeywords("office 2007 openxml php")
											 ->setCategory("Orden de Servicio");					 
					//$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
					$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);				
					$sHeaderBig    = new PHPExcel_Style();
					$sHeaderBlack  = new PHPExcel_Style();
					$sTextBlack    = new PHPExcel_Style();
					$sHeaderOrange = new PHPExcel_Style();					
					$sBorderOrange = new PHPExcel_Style();
					$sBordersBottom= new PHPExcel_Style();
					$sTittleOrange = new PHPExcel_Style();
					$sTextOrange   = new PHPExcel_Style();
					
					$sHeaderBig->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => '000000'),
					        'size'  => 16,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )				        
					));					
		
					$sHeaderBlack->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => '000000'),
					        'size'  => 12,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));
					
					$sTextBlack->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => '000000'),
					        'size'  => 9,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));
					
					$sHeaderOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FF8000'),
					        'size'  => 12,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));	
					
					$sTextOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FF8000'),
					        'size'  => 10,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));	
					
					$sBorderOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FF8000'),
					        'size'  => 9,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      	'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					        	'color' => array('rgb' => 'FF8000')
						    )
						  )
					));	
					
					$sTittleOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FF8000')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'size'  => 12,
					        'name'  => 'Arial'
					    )
					));	
					
					$sBordersBottom->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => false,
					        'color' => array('rgb' => '000000'),
					        'size'  => 9,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'bottom' => array(
						      	'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					        	'color' => array('rgb' => 'FF8000')
						    )
						  )
					));	
										
					$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(1);	
					$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('10')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('12')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('18')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('22')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('24')->setRowHeight(5);					
					
					$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
					$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(19);
					$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
					$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
					$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
					$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);						 
											 
					/**
					 * Header del Reporte
					 **/					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', utf8_decode('Tracking Systems de Mexico, S.A de C.V.'));
					$objPHPExcel->getActiveSheet()->mergeCells('B3:G3');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderBig, 'B3:J3');
					
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					
					$objDrawing->setName('Logo');
					$objDrawing->setDescription('Logo');
					
					$objDrawing->setPath($this->realPath.'/logoUDA.jpg');
					$objDrawing->setWidth(70);
					$objDrawing->setHeight(90);
					//$objDrawing->setOffsetX(10);
					$objDrawing->setCoordinates('I2');
					
					$objPHPExcel->getActiveSheet()->getRowDimension('I2')->setRowHeight(150);										
					$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', utf8_decode('GERENCIA DE OPERACIONES '));
					$objPHPExcel->getActiveSheet()->mergeCells('B5:G5');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderOrange, 'B5:J5');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', utf8_decode('ORDEN DE SERVICIO'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderBlack, 'B7:J7');
					$objPHPExcel->getActiveSheet()->mergeCells('B7:G7');					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', utf8_decode('REVISION'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTextBlack, 'B9:B9');					
					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'D9:D9');					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E9', utf8_decode('Corporativo (Mexico)'));
					$objPHPExcel->getActiveSheet()->mergeCells('E9:F9');	
					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'G9:G9');					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H9', utf8_decode('Sucursal'));
					$objPHPExcel->getActiveSheet()->mergeCells('H9:H9');
										
															
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I9', utf8_decode('Folio:'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'J9:J9');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J9', utf8_encode($dataCita['FOLIO']));
					$objPHPExcel->getActiveSheet()->mergeCells('J9:J9');
					
					/**
					 * Datos Generales de la Cita
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B11', utf8_decode('Datos generales'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B11:J11');	
					$objPHPExcel->getActiveSheet()->mergeCells('B11:J11');					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B13', utf8_encode('Nombre o Razon Social:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B13:C13');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D13', substr($dataCita['NOMBRE_CLIENTE'], 0, 45) );	
					$objPHPExcel->getActiveSheet()->mergeCells('D13:G13');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D13:G13');
					
									
					if(strlen($dataCita['NOMBRE_CLIENTE'])>45){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D14',substr($dataCita['NOMBRE_CLIENTE'], 45, 60) );	
					}											
					$objPHPExcel->getActiveSheet()->mergeCells('D14:G14');					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D14:G14');
										

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B15', utf8_encode('Direccion: '));	
					$objPHPExcel->getActiveSheet()->mergeCells('B15:C15');		
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D15', ($dataCita['DIRECCION_CITA1']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D15:G15');	
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D15:G15');					

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D16', ($dataCita['DIRECCION_CITA2']));
					$objPHPExcel->getActiveSheet()->mergeCells('D16:G16');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D16:G16');
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B17', utf8_decode('Contacto:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B17:C17');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D17', $dataCita['CONTACTO']);	
					$objPHPExcel->getActiveSheet()->mergeCells('D17:G17');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D17:G17');
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H13', utf8_decode('Fecha de Servicio:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('H13:H13');						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I13', ($dataCita['FECHA_CITA']));	
					$objPHPExcel->getActiveSheet()->mergeCells('I13:J13');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I13:J13');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H14', utf8_decode('Hora de Cita:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('H14:H14');						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I14', ($dataCita['HORA_CITA']));
					$objPHPExcel->getActiveSheet()->mergeCells('I14:J14');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I14:J14');
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H15', utf8_decode('Hora Inicial:'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('H15:J15');	

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I15', utf8_decode($dataCita['FECHA_INICIO']));
					$objPHPExcel->getActiveSheet()->mergeCells('I15:J15');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I15:J15');
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H16', utf8_decode('Hora Final:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('H16:H16');	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I16', ($dataCita['FECHA_TERMINO']));
					$objPHPExcel->getActiveSheet()->mergeCells('I16:J16');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I16:J16');
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H17', utf8_decode('Telefono:'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('H17:H17');	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I17', ($dataCita['TELEFONO_CONTACTO']));
					$objPHPExcel->getActiveSheet()->mergeCells('I17:J17');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I17:J17');

					/**
					 * Datos del Equipo y Accesorios Instalados
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B19', utf8_decode('Datos del Equipo y Accesorios Instalados'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B19:J19');	
					$objPHPExcel->getActiveSheet()->mergeCells('B19:J19');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B20', utf8_decode('Marca'));											
					$objPHPExcel->getActiveSheet()->mergeCells('B20:C20');
					$objPHPExcel->getActiveSheet()->getStyle('B20:C20')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E20', utf8_decode('Modelo'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('E20:F20');	
					$objPHPExcel->getActiveSheet()->getStyle('E20')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);					

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G20', utf8_decode('IMEI'));	
					$objPHPExcel->getActiveSheet()->mergeCells('G20:H20');
					$objPHPExcel->getActiveSheet()->getStyle('G20:H20')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I20', utf8_decode('IP'));	
					$objPHPExcel->getActiveSheet()->mergeCells('I20:J20');
										$objPHPExcel->getActiveSheet()->getStyle('I20:J20')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
					
					/*------------- -------------*/
					$aDataEqForm = $cCitas->getDataSendbyForms($this->dataIn['strInput'],13);
					$dataEquipment = Array();
					
					foreach($aDataEqForm as $items){
						if($items['ID_ELEMENTO']==220){
							@$dataEquipment['MARCA'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==223){
							@$dataEquipment['IP'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==222){
							@$dataEquipment['IMEI'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==221){
							@$dataEquipment['MODELO'] = $items['CONTESTACION'];
						}  
					}
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B21', ($dataEquipment['MARCA']));	
					$objPHPExcel->getActiveSheet()->mergeCells('B21:C21');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'B21:C21');
					$objPHPExcel->getActiveSheet()->getStyle('B21:C21')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E21', ($dataEquipment['MODELO']));	
					//$objPHPExcel->getActiveSheet()->mergeCells('E21:F21');					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'E21');
					$objPHPExcel->getActiveSheet()->getStyle('E21')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G21', ($dataEquipment['IMEI']));
					$objPHPExcel->getActiveSheet()->getStyle('G21')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);	
					$objPHPExcel->getActiveSheet()->mergeCells('G21:H21');					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G21:H21');
					$objPHPExcel->getActiveSheet()->getStyle('G21:H21')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);						
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I21', ($dataEquipment['IP']));	
					$objPHPExcel->getActiveSheet()->mergeCells('I21:J21');	
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I21:J21');
					$objPHPExcel->getActiveSheet()->getStyle('I21:J21')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B23', utf8_decode('Partes Instaladas (Marca con un X)'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B23:J23');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTextOrange, 'B23:J23');	
			
					$iValColumn = 0;
					$rowControl	= 25;
					/* ----- -----*/
					foreach($aDataEqForm as $items){
						if($items['ID_ELEMENTO']>223 && $items['ID_ELEMENTO'] < 244){
							$sRespuesta = ($items['CONTESTACION']=='SI') ?  'X': '';
							if($iValColumn==0){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'D'.$rowControl);	
								$iValColumn++;
							}else if($iValColumn==1){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('E'.$rowControl.':F'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'G'.$rowControl);
								
								$iValColumn++;								
							}else if($iValColumn==2){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('H'.$rowControl.':I'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'J'.$rowControl);
								
								$rowControl++;								
								$iValColumn=0;								
							}
						}						 
					}
					
					$rowControl++;
					$rowControl++;
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Partes Reemplazadas (Marca con un X)'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTextOrange,'B'.$rowControl.':J'.$rowControl);	
					
					$rowControl++;
					$rowControl++;
					
					$iValColumn = 0;					
					/* ----- -----*/
					foreach($aDataEqForm as $items){
						if($items['ID_ELEMENTO']>244){
							$sRespuesta = ($items['CONTESTACION']=='SI') ?  'X': '';
							if($iValColumn==0){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'D'.$rowControl);	
								$iValColumn++;
							}else if($iValColumn==1){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('E'.$rowControl.':F'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'G'.$rowControl);
								
								$iValColumn++;								
							}else if($iValColumn==2){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('H'.$rowControl.':I'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'J'.$rowControl);
								
								$rowControl++;								
								$iValColumn=0;								
							}
						}						 
					}	
					
					$rowControl = $rowControl+10;
					
																			
					/**
					 * Pruebas del Funcionamiento del Equipo
					 **/
					
					$aDataPruebas = $cCitas->getDataSendbyForms($this->dataIn['strInput'],14);		
					$aDataCUDA = Array();
					foreach($aDataPruebas as $items){
						if($items['ID_ELEMENTO']==252){
							@$aDataCUDA['FOLIO'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==253){
							@$aDataCUDA['EJUDA'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==254){
							@$aDataCUDA['FOLCLI'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==255){
							@$aDataCUDA['MCLI'] = $items['CONTESTACION'];
						}											
					}
															
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Pruebas del Funcionamiento del Equipo'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B'.$rowControl.':J'.$rowControl);	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$rowControl++;
					$rowControl++;

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Folio de Validacion CCUDA: '));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, utf8_decode('* Folio de Validacion Cliente:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('G'.$rowControl.':J'.$rowControl);
					$rowControl++;
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($aDataCUDA['FOLIO']));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'B'.$rowControl.':E'.$rowControl);	
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':E'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);						
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, ($aDataCUDA['FOLCLI']));	
					$objPHPExcel->getActiveSheet()->mergeCells('G'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G'.$rowControl.':J'.$rowControl);
					
					$rowControl++;
					$rowControl++;
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ('Ejecutivo de Atencion CCUDA:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, ('* Monitorista por parte del cliente:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('G'.$rowControl.':J'.$rowControl);
					$rowControl++;
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($aDataCUDA['EJUDA']));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'B'.$rowControl.':E'.$rowControl);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':E'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);											

										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, ($aDataCUDA['MCLI']));	
					$objPHPExcel->getActiveSheet()->mergeCells('G'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G'.$rowControl.':J'.$rowControl);		
					$objPHPExcel->getActiveSheet()->getStyle('G'.$rowControl.':J'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);						
					
					$rowControl++;											
					$rowControl++;
					
					$iValColumn = 0;
					foreach($aDataPruebas as $items){	
						if($items['ID_ELEMENTO']>255){
							$sRespuesta = ($items['CONTESTACION']=='SI') ?  'X': '';
							if($iValColumn==0){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'D'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('D'.$rowControl)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								
								$iValColumn++;
							}else if($iValColumn==1){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('E'.$rowControl.':F'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'G'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('G'.$rowControl)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								
								$iValColumn++;								
							}else if($iValColumn==2){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('H'.$rowControl.':I'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'J'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('J'.$rowControl)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								
								$rowControl++;
								$iValColumn=0;
							}
						}						 
					}			
					
					$rowControl = $rowControl+5; 
					
					/**
					 * Firmas
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Firmas'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B'.$rowControl.':J'.$rowControl);
					$rowControl++;
					$rowControl++;

					$aDataFirma = $cCitas->getDataSendbyForms($this->dataIn['strInput'],15);
					$dataFirma  = Array();
					
					foreach($aDataFirma as $items){
						if($items['ID_ELEMENTO']==274){
							@$dataFirma['NCLIENTE'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==276){
							@$dataFirma['FCLIENTE'] = $items['CONTESTACION'];
						}
						/*else if($items['ID_ELEMENTO']==157){
							@$dataFirma['FINSTALADOR'] = $items['CONTESTACION'];
						}*/  
					}
	
					$exist_file = file_exists($this->realPath.$dataFirma['FCLIENTE']); 

					if ($exist_file== true && $dataFirma['FCLIENTE']!="") {
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						
						$objDrawing->setName('Picture1');
						$objDrawing->setDescription('Picture1');
						
						$objDrawing->setPath($this->realPath.$dataFirma['FCLIENTE']);
						$objDrawing->setWidth(60);
						//$objDrawing->setOffsetX(150);
						$objDrawing->setHeight(75);
						//$objDrawing->setOffsetY(-160);

						$objDrawing->setCoordinates('C'.$rowControl);
						
						$objPHPExcel->getActiveSheet()->getRowDimension('C'.$rowControl)->setRowHeight(150);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$rowControl.':F'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
						$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':F'.$rowControl);

						//$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(140);										
						$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
					}else{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$rowControl, utf8_decode('Imagen no disponible.'));								
						$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':F'.$rowControl);										
					}
					
					/*
					$exist_file = file_exists($this->realPath.$dataFirma['FINSTALADOR']); 

					if ($exist_file== true && $dataFirma['FINSTALADOR']!="") {
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						
						$objDrawing->setName('Picture1');
						$objDrawing->setDescription('Picture1');
						
						$objDrawing->setPath($this->realPath.$dataFirma['FINSTALADOR']);
						$objDrawing->setWidth(60);
						//$objDrawing->setOffsetX(150);
						$objDrawing->setHeight(75);
						//$objDrawing->setOffsetY(-160);

						$objDrawing->setCoordinates('G'.$rowControl);
						
						$objPHPExcel->getActiveSheet()->getRowDimension('G'.$rowControl)->setRowHeight(150);
						$objPHPExcel->getActiveSheet()->getStyle('G'.$rowControl.':J'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
						$objPHPExcel->getActiveSheet()->mergeCells('G'.$rowControl.':J'.$rowControl);

						//$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(140);										
						$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
					}else{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, utf8_decode('Imagen no disponible.'));								
						$objPHPExcel->getActiveSheet()->mergeCells('G'.$rowControl.':J'.$rowControl);										
					}	*/				
					
					$rowControl = $rowControl+5;	
					/*				
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, $dataCita['NOMBRE_TECNICO']);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':E'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);
					*/
						
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, $dataFirma['NCLIENTE']);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':E'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);							
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);
					
					
					$rowControl = $rowControl-1;
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'B'.$rowControl.':E'.$rowControl);
					/*$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G'.$rowControl.':J'.$rowControl);*/	
    
					$objPHPExcel->setActiveSheetIndex(0)->setShowGridLines(false);
					$objPHPExcel->setActiveSheetIndex(0)->setPrintGridLines(false);	

					/*
					$filename  = "Orden_Servicio_".$this->dataIn['strInput'].".xlsx";		
					// Redirect output to a clientÕs web browser (PDF)
					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');									
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');
					*/	
					
					$filename  = "Orden_Servicio_".$dataCita['FOLIO'].".pdf";
					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');									
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
					$objWriter->save('php://output');
									
				}else{
					echo "no hay informacion";
				}
			}else{
				echo "no hay informacion";
			}			
		}catch(Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
		}	
	}
	
	public function exportchecklistAction(){
		try{
			$aDataForms;
			$dataCita;
			$validate=0;
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$cCitas = new My_Model_Citas();
			
			if(isset($this->dataIn['strInput'])  	 && $this->dataIn['strInput']!=""){
				$dataCita = $cCitas->getDataRep($this->dataIn['strInput']);
				
				if(count($dataCita)>0){	
					/** PHPExcel */ 
					require_once 'PHPExcel.php';		
											
					if (!PHPExcel_Settings::setPdfRenderer(
							PHPExcel_Settings::PDF_RENDERER_DOMPDF,
							$this->realPath.'/PHPExcel/Classes/dompdf'
					)) {
						die(
							'NOTICE: Please set the $rendererName and asdads$rendererLibraryPath values' .
							'<br />' .
							'at the top of this script as appropriate for your directory structure'
						);
					}
					
					/** PHPExcel_Writer_Excel2007*/ 								
					$objPHPExcel = new PHPExcel();
	 					
					$objPHPExcel->getProperties()->setCreator("UDA")
											 ->setLastModifiedBy("UDA")
											 ->setTitle("Office 2007 XLSX")
											 ->setSubject("Office 2007 XLSX")
											 ->setDescription("Orden de Servicio")
											 ->setKeywords("office 2007 openxml php")
											 ->setCategory("Orden de Servicio");					 
					//$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
					$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);				
					$sHeaderBig    = new PHPExcel_Style();
					$sHeaderBlack  = new PHPExcel_Style();
					$sTextBlack    = new PHPExcel_Style();
					$sHeaderOrange = new PHPExcel_Style();					
					$sBorderOrange = new PHPExcel_Style();
					$sBordersBottom= new PHPExcel_Style();
					$sTittleOrange = new PHPExcel_Style();
					$sTextOrange   = new PHPExcel_Style();
					
					$sHeaderBig->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => '000000'),
					        'size'  => 16,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )				        
					));					
		
					$sHeaderBlack->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => '000000'),
					        'size'  => 12,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));
					
					$sTextBlack->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => '000000'),
					        'size'  => 9,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));
					
					$sHeaderOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FF8000'),
					        'size'  => 12,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));	
					
					$sTextOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FF8000'),
					        'size'  => 9,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      'style' => PHPExcel_Style_Border::BORDER_NONE
						    )
						  )
					));	
					
					$sBorderOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FF8000'),
					        'size'  => 9,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'allborders' => array(
						      	'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					        	'color' => array('rgb' => 'FF8000')
						    )
						  )
					));	
					
					$sTittleOrange->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FF8000')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => 'FFFFFF'),
					        'size'  => 12,
					        'name'  => 'Arial'
					    )
					));	
					
					$sBordersBottom->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => false,
					        'color' => array('rgb' => '000000'),
					        'size'  => 9,
					        'name'  => 'Arial'
					    ),
						  'borders' => array(
						    'bottom' => array(
						      	'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					        	'color' => array('rgb' => 'FF8000')
						    )
						  )
					));
										
					$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(1);	
					$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('10')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('12')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('18')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('22')->setRowHeight(5);
					$objPHPExcel->getActiveSheet()->getRowDimension('24')->setRowHeight(5);					
					
					$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9);
					$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(9);					
					$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
					$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(5);
					$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
					$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
					$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);										 

					/**
					 * Header del Reporte
					 **/					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', utf8_decode('Tracking Systems de Mexico, S.A de C.V.'));
					$objPHPExcel->getActiveSheet()->mergeCells('B3:G3');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderBig, 'B3:H3');
					
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					
					$objDrawing->setName('Logo');
					$objDrawing->setDescription('Logo');
					
					$objDrawing->setPath($this->realPath.'/logoUDA.jpg');
					$objDrawing->setWidth(70);
					$objDrawing->setHeight(90);
					//$objDrawing->setOffsetX(10);
					$objDrawing->setCoordinates('I2');
					
					$objPHPExcel->getActiveSheet()->getRowDimension('I2')->setRowHeight(150);										
					$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B5', utf8_decode('GERENCIA DE OPERACIONES '));
					$objPHPExcel->getActiveSheet()->mergeCells('B5:G5');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderOrange, 'B5:J5');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', utf8_decode('CHECK LIST DE SERVICIO '));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderBlack, 'B7:J7');
					$objPHPExcel->getActiveSheet()->mergeCells('B7:G7');					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', utf8_decode('REVISION'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTextBlack, 'B9:B9');					
					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'D9:D9');					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E9', utf8_decode('Corporativo (Mexico)'));
					$objPHPExcel->getActiveSheet()->mergeCells('E9:F9');	
					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'G9:G9');					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H9', utf8_decode('Sucursal'));
					$objPHPExcel->getActiveSheet()->mergeCells('H9:H9');
																									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I9', utf8_decode('Fecha:'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'J9:J9');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J9', utf8_encode($dataCita['FECHA_CITA']));
					$objPHPExcel->getActiveSheet()->mergeCells('J9:J9');					
					
					/**
					 * Datos Generales de la Cita
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B11:J11');						
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B11', utf8_decode('Datos generales'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B11:J11');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B13', utf8_decode('Nombre o Razon Social:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B13:C13');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D13', ($dataCita['NOMBRE_CLIENTE']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D13:G13');					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D13:G13');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B15', utf8_encode('Direccion: '));	
					$objPHPExcel->getActiveSheet()->mergeCells('B15:C15');		
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D15', ($dataCita['DIRECCION_CITA1']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D15:G15');	
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D15:G15');					

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D16', ($dataCita['DIRECCION_CITA2']));
					$objPHPExcel->getActiveSheet()->mergeCells('D16:G16');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D16:F16');
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B17', utf8_decode('Contacto:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B17:C17');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D17', ($dataCita['CONTACTO']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D17:G17');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D17:G17');
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H13', utf8_decode('Folio:'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('H13:J13');						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I13',  ($dataCita['FOLIO']));	
					$objPHPExcel->getActiveSheet()->mergeCells('I13:J13');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I13:J13');
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H15', utf8_decode('Fec. Servicio:'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('H15:J15');	

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I15', ($dataCita['FECHA_CITA'].' '.$dataCita['HORA_CITA']));
					$objPHPExcel->getActiveSheet()->mergeCells('I15:J15');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I15:J15');
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H17', utf8_decode('Telefono:'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('H17:J17');	
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I17', ($dataCita['TELEFONO_CONTACTO']));
					$objPHPExcel->getActiveSheet()->mergeCells('I17:J17');		
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I17:J17');			
					
					/**
					 * Datos de veh’culo 
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B19:J19');	
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B19', utf8_decode('Datos de vehiculo '));	
					$objPHPExcel->getActiveSheet()->mergeCells('B19:J19');					
									
					$aDataEqForm = $cCitas->getDataSendbyForms($this->dataIn['strInput'],12);
					$dataEquipment = Array();
					
					foreach($aDataEqForm as $items){
						if($items['ID_ELEMENTO']==176){
							@$dataEquipment['MARCA'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==178){
							@$dataEquipment['MODELO'] = $items['CONTESTACION'];	
						}else if($items['ID_ELEMENTO']==179){
							@$dataEquipment['PLACAS'] = $items['CONTESTACION'];	
						}else if($items['ID_ELEMENTO']==177){
							@$dataEquipment['TIPO'] = $items['CONTESTACION'];	
						}else if($items['ID_ELEMENTO']==180){
							@$dataEquipment['COLOR'] = $items['CONTESTACION'];	
						}else if($items['ID_ELEMENTO']==181){
							@$dataEquipment['ECO'] = $items['CONTESTACION'];	
						}else if($items['ID_ELEMENTO']==182){
							@$dataEquipment['SERIE'] = $items['CONTESTACION'];	
						}else if($items['ID_ELEMENTO']==183){
							@$dataEquipment['NO_MOTOR']= $items['CONTESTACION'];	
						}else if($items['ID_ELEMENTO']==184){
							@$dataEquipment['FOTO_SERIE'] = $items['CONTESTACION'];								
						/*LAS FOTOS DE LA CARROCERIA*/	
						}else if($items['ID_ELEMENTO']==186){
							@$dataEquipment['FOTO_FRENTE'] = $items['CONTESTACION'];		
						}else if($items['ID_ELEMENTO']==187){
							@$dataEquipment['FOTO_POST'] = $items['CONTESTACION'];	
						}else if($items['ID_ELEMENTO']==188){
							@$dataEquipment['FOTO_IZQ'] = $items['CONTESTACION'];	
						}else if($items['ID_ELEMENTO']==189){
							@$dataEquipment['FOTO_DER'] = $items['CONTESTACION'];	
						}
					}
															
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B21', utf8_decode('Marca'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B21:C21');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D21', ($dataEquipment['MARCA']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D21:E21');		
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D21:E21');	

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F21', utf8_decode('Placas'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('F21:G21');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G21', ($dataEquipment['PLACAS']));	
					$objPHPExcel->getActiveSheet()->mergeCells('G21:H21');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G21:H21');	
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I21', utf8_decode('No. de Serie'));	
					$objPHPExcel->getActiveSheet()->mergeCells('I21:J21');

					$exist_file = file_exists($this->realPath.$dataEquipment['FOTO_SERIE']); 

					if ($exist_file== true && $dataEquipment['FOTO_SERIE']!="") {
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						
						$objDrawing->setName('Picture1');
						$objDrawing->setDescription('Picture1');
						
						$objDrawing->setPath($this->realPath.$dataEquipment['FOTO_SERIE']);
						$objDrawing->setWidth(150);
						//$objDrawing->setOffsetX(150);
						$objDrawing->setHeight(170);
						//$objDrawing->setOffsetY(-160);

						$objDrawing->setCoordinates('I22');
						
						$objPHPExcel->getActiveSheet()->getRowDimension('I22')->setRowHeight(150);
						$objPHPExcel->getActiveSheet()->getStyle('I22:J22')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
						$objPHPExcel->getActiveSheet()->mergeCells('I22:J22');

						$objPHPExcel->getActiveSheet()->getRowDimension('I22')->setRowHeight(140);										
						$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
					}else{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I22', utf8_decode('Imagen no disponible.'));								
						$objPHPExcel->getActiveSheet()->mergeCells('I22:J22');										
					}					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B23', utf8_decode('Tipo'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B23:C23');	

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D23', utf8_encode($dataEquipment['TIPO']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D23:E23');	
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D23:E23');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F23', utf8_decode('Color:'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('G23:G23');	

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G23', ($dataEquipment['COLOR']));	
					$objPHPExcel->getActiveSheet()->mergeCells('G23:H23');	
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G23:H23');					

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B25', utf8_decode('Modelo'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B25:C25');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D25', ($dataEquipment['MODELO']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D25:E25');		
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D25:E25');		

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F25', utf8_decode('No. Eco: '));	
					//$objPHPExcel->getActiveSheet()->mergeCells('G25:G25');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G25', utf8_encode($dataEquipment['ECO']));	
					$objPHPExcel->getActiveSheet()->mergeCells('G25:H25');			
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G25:H25');					
										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B27', utf8_decode('No. de Serie:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B27:C27');					
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D27', utf8_encode($dataEquipment['SERIE']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D27:F27');		
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D27:F27');				
															
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B29', utf8_decode('No. de Motor:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B29:C29');					
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D29', utf8_encode($dataEquipment['NO_MOTOR']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D29:F29');	
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D29:F29');

					/**
					 * Datos de veh’culo 
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B31', utf8_decode('Carroceria (Indicar golpes, rayones y/o danos en la pintura)'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B31:J31');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTextOrange, 'B31:J31');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B33', utf8_decode('FRENTE'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B33:F33');
					$objPHPExcel->getActiveSheet()->getStyle('B33:F33')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
												
					$exist_file = file_exists($this->realPath.$dataEquipment['FOTO_FRENTE']); 

					if ($exist_file== true && $dataEquipment['FOTO_FRENTE']!="") {
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						
						$objDrawing->setName('Picture1');
						$objDrawing->setDescription('Picture1');
						
						$objDrawing->setPath($this->realPath.$dataEquipment['FOTO_FRENTE']);
						$objDrawing->setOffsetX(-25);
						$objDrawing->setWidth(120);
						$objDrawing->setHeight(155);

						$objDrawing->setCoordinates('C34');
						
						$objPHPExcel->getActiveSheet()->getRowDimension('C34')->setRowHeight(150);
						$objPHPExcel->getActiveSheet()->getStyle('C34:F34')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
						$objPHPExcel->getActiveSheet()->mergeCells('C34:F34');

						$objPHPExcel->getActiveSheet()->getRowDimension('C34')->setRowHeight(140);										
						$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
					}else{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C34', utf8_decode('Imagen no disponible.'));								
						$objPHPExcel->getActiveSheet()->mergeCells('C34:F34');										
					}						
							
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G33', utf8_decode('POSTERIOR'));	
					$objPHPExcel->getActiveSheet()->mergeCells('G33:J33');
					$objPHPExcel->getActiveSheet()->getStyle('G33:J33')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						
					$exist_file = file_exists($this->realPath.$dataEquipment['FOTO_POST']); 

					if ($exist_file== true && $dataEquipment['FOTO_POST']!="") {
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						
						$objDrawing->setName('Picture1');
						$objDrawing->setDescription('Picture1');
						
						$objDrawing->setPath($this->realPath.$dataEquipment['FOTO_POST']);
						$objDrawing->setOffsetX(35);
						$objDrawing->setWidth(120);
						$objDrawing->setHeight(155);

						$objDrawing->setCoordinates('H34');
						
						$objPHPExcel->getActiveSheet()->getRowDimension('H34')->setRowHeight(150);
						$objPHPExcel->getActiveSheet()->getStyle('H34:J34')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
						$objPHPExcel->getActiveSheet()->mergeCells('H34:J34');

						$objPHPExcel->getActiveSheet()->getRowDimension('H34')->setRowHeight(140);										
						$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
					}else{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H34', utf8_decode('Imagen no disponible.'));								
						$objPHPExcel->getActiveSheet()->mergeCells('H34:J34');										
					}
					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B45', utf8_decode('LATERAL IZQUIERDO'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B45:F45');
					$objPHPExcel->getActiveSheet()->getStyle('B45:F45')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$exist_file = file_exists($this->realPath.$dataEquipment['FOTO_IZQ']); 

					if ($exist_file== true && $dataEquipment['FOTO_IZQ']!="") {
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						
						$objDrawing->setName('Picture1');
						$objDrawing->setDescription('Picture1');
						
						$objDrawing->setPath($this->realPath.$dataEquipment['FOTO_IZQ']);
						$objDrawing->setOffsetX(-25);
						$objDrawing->setWidth(120);
						$objDrawing->setHeight(155);

						$objDrawing->setCoordinates('C46');
						
						$objPHPExcel->getActiveSheet()->getRowDimension('C46')->setRowHeight(150);
						$objPHPExcel->getActiveSheet()->getStyle('C46:F46')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
						$objPHPExcel->getActiveSheet()->mergeCells('C46:F46');

						$objPHPExcel->getActiveSheet()->getRowDimension('C46')->setRowHeight(140);										
						$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
					}else{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C46', utf8_decode('Imagen no disponible.'));								
						$objPHPExcel->getActiveSheet()->mergeCells('C46:F46');										
					}	
										
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G45', utf8_decode('LATERAL DERECHO'));	
					$objPHPExcel->getActiveSheet()->mergeCells('G45:J45');
					$objPHPExcel->getActiveSheet()->getStyle('G45:J45')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	

					$exist_file = file_exists($this->realPath.$dataEquipment['FOTO_DER']); 

					if ($exist_file== true && $dataEquipment['FOTO_DER']!="") {
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						
						$objDrawing->setName('Picture1');
						$objDrawing->setDescription('Picture1');
						
						$objDrawing->setPath($this->realPath.$dataEquipment['FOTO_DER']);
						$objDrawing->setOffsetX(35);
						$objDrawing->setWidth(120);
						$objDrawing->setHeight(155);

						$objDrawing->setCoordinates('H46');
						
						$objPHPExcel->getActiveSheet()->getRowDimension('H46')->setRowHeight(150);
						$objPHPExcel->getActiveSheet()->getStyle('H46:J46')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
						$objPHPExcel->getActiveSheet()->mergeCells('H46:J46');

						$objPHPExcel->getActiveSheet()->getRowDimension('H46')->setRowHeight(140);										
						$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
					}else{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H46', utf8_decode('Imagen no disponible.'));								
						$objPHPExcel->getActiveSheet()->mergeCells('H46:J46');										
					}	
											
					//$rowControl = $rowControl+2;

					/**
					 * Checklist de revisi—n de unidad
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B55:J55');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B55', utf8_decode('Checklist de revision de unidad'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B55:J55');		
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B56', utf8_decode('Si la parte mencionada funciona correctamente, se marcar‡ con una X , de lo contrario se marcar‡ con una O'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B56:J56');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTextOrange, 'B56:J56');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H57', utf8_decode('Nivel de Combustible'));
					$objPHPExcel->getActiveSheet()->mergeCells('H57:J57');
					$objPHPExcel->getActiveSheet()->getStyle('H57:J57')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);						
										
					$exist_file = file_exists($this->realPath.$aDataRev['FOTO_COMB']); 

					if ($exist_file== true && $aDataRev['FOTO_COMB']!="") {
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						
						$objDrawing->setName('Picture1');
						$objDrawing->setDescription('Picture1');
						
						$objDrawing->setPath($this->realPath.$aDataRev['FOTO_COMB']);
						$objDrawing->setWidth(40);
						$objDrawing->setOffsetX(10);
						$objDrawing->setHeight(165);
						//$objDrawing->setOffsetY(-160);

						$objDrawing->setCoordinates('I58');
						
						$objPHPExcel->getActiveSheet()->getRowDimension('I58')->setRowHeight(150);
						$objPHPExcel->getActiveSheet()->getStyle('I58:J58')
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
						$objPHPExcel->getActiveSheet()->mergeCells('I58:J58');

						//$objPHPExcel->getActiveSheet()->getRowDimension('L45')->setRowHeight(140);										
						$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
					}else{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I58', utf8_decode('Imagen no disponible.'));								
						$objPHPExcel->getActiveSheet()->mergeCells('I58:J58');										
					}	
					
					$rowControl	  = 58;

					
					//$aDataFormRev = $cCitas->getDataSendbyForms($this->dataIn['strInput'],6);					
					$aDataRev 	  = Array();					
					$iValColumn   = 0;
					
					foreach($aDataEqForm as $items){
						if($items['ID_ELEMENTO']==210){
							@$aDataRev['VOLTS'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==211){
							@$aDataRev['AMP'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==214){
							@$aDataRev['FOTO_COMB'] = $items['CONTESTACION'];
						}
						
						if($items['ID_ELEMENTO']>190 && $items['ID_ELEMENTO']<209 && $items['ID_TIPO']!=8 &&  $items['ID_TIPO']!=9 ){
							$sRespuesta = ($items['CONTESTACION']=='SI') ?  'X': 'O';

							if($iValColumn==0){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'D'.$rowControl);	
								$objPHPExcel->getActiveSheet()->getStyle('D'.$rowControl)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$iValColumn++;
							}else if($iValColumn==1){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$rowControl, utf8_decode($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('E'.$rowControl.':F'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'G'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('G'.$rowControl)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$rowControl++;								
								$iValColumn=0;									
							}					
						}											
					}
							
					$rowControl++;
					$rowControl++;	
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Bateria:'));						
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$rowControl, utf8_decode('Voltaje:'));
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowControl, utf8_decode($aDataRev['VOLTS']));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$rowControl, utf8_decode('volts.'));
							
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$rowControl, utf8_decode('Corriente:'));
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, utf8_decode($aDataRev['AMP']));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$rowControl, utf8_decode('amp.'));	
																							
					$rowControl = $rowControl+3;
										
					/**
					 * FIRMAS
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Firmas'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);						
					$rowControl++;						
																
					$aDataFirma = $cCitas->getDataSendbyForms($this->dataIn['strInput'],16);
					$dataFirma  = Array();
					
					foreach($aDataFirma as $items){
						if($items['ID_ELEMENTO']==216){
							@$dataFirma['NCLIENTE'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==218){
							@$dataFirma['FCLIENTE'] = $items['CONTESTACION'];
						}
					}
	
					$exist_file = file_exists($this->realPath.$dataFirma['FCLIENTE']); 

					if ($exist_file== true && $dataFirma['FCLIENTE']!="") {
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						
						$objDrawing->setName('Picture1');
						$objDrawing->setDescription('Picture1');
						
						$objDrawing->setPath($this->realPath.$dataFirma['FCLIENTE']);
						$objDrawing->setWidth(60);
						//$objDrawing->setOffsetX(150);
						$objDrawing->setHeight(75);
						//$objDrawing->setOffsetY(-160);

						$objDrawing->setCoordinates('C'.$rowControl);
						
						$objPHPExcel->getActiveSheet()->getRowDimension('C'.$rowControl)->setRowHeight(150);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$rowControl.':F'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
						$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':F'.$rowControl);

						//$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(140);										
						$objDrawing->setWorksheet($objPHPExcel->setActiveSheetIndex(0));									    
					}else{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$rowControl, utf8_decode('Imagen no disponible.'));								
						$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':F'.$rowControl);										
					}					
								
					$rowControl = $rowControl+1;					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.($rowControl+4), $dataFirma['NCLIENTE']);
					$objPHPExcel->getActiveSheet()->getStyle('B'.($rowControl+4).':E'.($rowControl+4))
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($rowControl+4).':E'.($rowControl+4));
										
					$rowControl = $rowControl-1;
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'B'.($rowControl+4).':E'.($rowControl+4));
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($rowControl+1), utf8_decode('Casa Matriz'));	
					$objPHPExcel->getActiveSheet()->mergeCells('F'.($rowControl+1).':J'.($rowControl+1));	
					$objPHPExcel->getActiveSheet()->getStyle('F'.($rowControl+1).':J'.($rowControl+1))
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);		

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($rowControl+2), utf8_decode('Carlos Arellano No.14,  Cto. Centro Comercial'));	
					$objPHPExcel->getActiveSheet()->mergeCells('F'.($rowControl+2).':J'.($rowControl+2));	
					$objPHPExcel->getActiveSheet()->getStyle('F'.($rowControl+2).':J'.($rowControl+2))
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($rowControl+3), utf8_decode('Cd. Satelite, Naucalpan , Edo. De Mex.'));	
					$objPHPExcel->getActiveSheet()->mergeCells('F'.($rowControl+3).':J'.($rowControl+3));
					$objPHPExcel->getActiveSheet()->getStyle('F'.($rowControl+3).':J'.($rowControl+3))
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($rowControl+4), utf8_decode('C.P. 53100 Tel: (0155)53749321'));	
					$objPHPExcel->getActiveSheet()->mergeCells('F'.($rowControl+4).':J'.($rowControl+4));
					$objPHPExcel->getActiveSheet()->getStyle('F'.($rowControl+4).':J'.($rowControl+4))
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);					
					
					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($rowControl+5), utf8_decode('Lada sin costo (01800)221.1367'));	
					$objPHPExcel->getActiveSheet()->mergeCells('F'.($rowControl+5).':J'.($rowControl+5));
					$objPHPExcel->getActiveSheet()->getStyle('F'.($rowControl+5).':J'.($rowControl+5))
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($rowControl+6), utf8_decode('http://www.grupouda.com.mx '));	
					$objPHPExcel->getActiveSheet()->mergeCells('F'.($rowControl+6).':J'.($rowControl+6));
					$objPHPExcel->getActiveSheet()->getStyle('F'.($rowControl+6).':J'.($rowControl+6))
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);					

					
					/*$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'G'.$rowControl.':J'.$rowControl);*/	
    
					/*
					$rowControl++;
					$rowControl++;	*/
					
					

					$objPHPExcel->setActiveSheetIndex(0)->setShowGridLines(false);
					$objPHPExcel->setActiveSheetIndex(0)->setPrintGridLines(false);								
					/*
					$filename  = "Checklist_Orden_".$dataCita['FOLIO'].".xlsx";
					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');									
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');
					*/
					
					$filename  = "Checklist_Orden_".$dataCita['FOLIO'].".pdf";
					header('Content-Type: application/pdf');
					header('Content-Disposition: attachment;filename="'.$filename.'"');
					header('Cache-Control: max-age=0');									
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
					$objWriter->save('php://output');	
																					
				}else{
					echo "no hay informacion";
				}
			}else{
				echo "no hay informacion";
			}				
		}catch(Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
		}		
	}
	
	public function posistiondateAction(){
		try{
			$aDataDate = Array();
			$aPositon  = Array();
			$result	   = '';
			$this->view->layout()->setLayout('layout_blank');
			
			$cCitas		= new My_Model_Citas();
			$cTecnicos  = new My_Model_Tecnicos();				
			if(isset($this->dataIn['strInput']) && $this->dataIn['strInput']){
				$aDataDate	= $cCitas->getCitasDet($this->dataIn['strInput']);				
				if(isset($aDataDate['ID_OPERADOR']) && $aDataDate['ID_OPERADOR']!=""){
					$dataPhone = $cTecnicos->getPhoneByuser($aDataDate['ID_OPERADOR']);
					if(count($dataPhone)>0){
						$dataPos    = $cTecnicos->getLastPositions($dataPhone['ID_TELEFONO']);
						if(count($dataPos)>0){
							$aPositon   = $dataPos[0];	
							$result .=  $aPositon['ID']."|".
										$aPositon['FECHA_GPS']."|".
		                                $aPositon['EVENTO']."|".
		                                $aPositon['LATITUD']."|".
		                                $aPositon['LONGITUD']."|".
		                                round($aPositon['VELOCIDAD'],2)."|".
		                                round($aPositon['NIVEL_BATERIA'],2)."|".
		                                $aPositon['TIPO_GPS']."|".
		                                $aPositon['ANGULO']."|".
		                                $aPositon['UBICACION'];
						}							
					}
				}			
			}
    		
    		$this->view->dataDate = $aDataDate;
    		$this->view->dataPos  = $aPositon;
    		$this->view->resultPos= $result;
    		$this->view->data 	  = $this->dataIn;				
		}catch(Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
		}			
	}

}