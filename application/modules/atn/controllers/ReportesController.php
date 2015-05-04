<?php

class atn_ReportesController extends My_Controller_Action
{	
	protected $_clase = 'mservices';
	public $dataIn;	
	public $aService;
	public $realPath='/var/www/vhosts/angeles/htdocs/public';
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
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', 'Sucursal');								
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C9', @$dataCita['SUCURSAL']);
					$objPHPExcel->getActiveSheet()->mergeCells('C9:D9');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'C9:D9');
							
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E9', 'Tecnico');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F9', @$dataCita['NOMBRE_TECNICO']);
					$objPHPExcel->getActiveSheet()->mergeCells('F9:H9');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'F9:H9');									
																									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I8', utf8_decode('Folio: '));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'J8:J8');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J8', ($dataCita['FOLIO']));
					$objPHPExcel->getActiveSheet()->mergeCells('J8:J8');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I9', utf8_decode('Tipo de Servicio:'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'J9:J9');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J9', ($dataCita['TIPO_CITA']));
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
					
					if($dataCita['FECHA_TERMINO']<'2015-02-20 23:59:00'){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B23', utf8_decode('Partes Instaladas (Marca con un X)')); 
					}else{
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B23', ('Partes: (IN)staladas, (RE)visadas, (SU)stituidas '));
					}					
						
					$objPHPExcel->getActiveSheet()->mergeCells('B23:J23');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTextOrange, 'B23:J23');	
			
					$iValColumn = 0;
					$rowControl	= 25;
					/* ----- -----*/
					foreach($aDataEqForm as $items){
						if($items['ID_ELEMENTO']>223 && $items['ID_ELEMENTO'] < 244){
							$sRespuesta="";
							if($dataCita['FECHA_TERMINO']<'2015-02-20 23:59:00'){
								$sRespuesta = ($items['CONTESTACION']=='SI') ?  'X': '';
							}else{
								$stringUpper = strtoupper($items['CONTESTACION']);
								$sRespuesta  =  substr($stringUpper, 0, 2); 
							}
							
							if($iValColumn==0){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'D'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('D'.$rowControl)
											->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
								$iValColumn++;
							}else if($iValColumn==1){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('E'.$rowControl.':F'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'G'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('G'.$rowControl)
											->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);								
								$iValColumn++;								
							}else if($iValColumn==2){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('H'.$rowControl.':I'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'J'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('J'.$rowControl)
											->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);								
								
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
							
							if($items['ID_ELEMENTO']==245){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($items['DESCRIPCION']));	
								$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
								
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$rowControl, ($sRespuesta));
								$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'D'.$rowControl);	
								$rowControl++;								
							}else{
								if($iValColumn==0){
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($items['DESCRIPCION']));	
									//$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':C'.$rowControl);
									
									$objPHPExcel->getActiveSheet()->setCellValue('C'.$rowControl, ($items['CONTESTACION']));
									$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':D'.$rowControl);
									$objPHPExcel->getActiveSheet()->setSharedStyle($sBordersBottom, 'C'.$rowControl.':D'.$rowControl);	
									$iValColumn++;
								}else if($iValColumn==1){
									$objPHPExcel->getActiveSheet()->setCellValue('E'.$rowControl, ($items['DESCRIPCION']));	
									//$objPHPExcel->getActiveSheet()->mergeCells('E'.$rowControl.':F'.$rowControl);
									
									$objPHPExcel->getActiveSheet()->setCellValue('F'.$rowControl, ($items['CONTESTACION']));
									$objPHPExcel->getActiveSheet()->mergeCells('F'.$rowControl.':G'.$rowControl);
									$objPHPExcel->getActiveSheet()->setSharedStyle($sBordersBottom, 'F'.$rowControl.':G'.$rowControl);
									
									$iValColumn++;								
								}else if($iValColumn==2){
									$objPHPExcel->getActiveSheet()->setCellValue('H'.$rowControl, ($items['DESCRIPCION']));	
									//$objPHPExcel->getActiveSheet()->mergeCells('H'.$rowControl.':I'.$rowControl);
									
									$objPHPExcel->getActiveSheet()->setCellValue('I'.$rowControl, ($items['CONTESTACION']));
									$objPHPExcel->getActiveSheet()->mergeCells('I'.$rowControl.':J'.$rowControl);
									$objPHPExcel->getActiveSheet()->setSharedStyle($sBordersBottom, 'I'.$rowControl.':J'.$rowControl);
									
									$rowControl++;								
									$iValColumn=0;								
								}								
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
						}else if($items['ID_ELEMENTO']==55){
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

										
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$rowControl, (@$aDataCUDA['MCLI']));	
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
					$rowControl++;
					$rowControl++;
															 				
					$aDataFirma = $cCitas->getDataSendbyForms($this->dataIn['strInput'],15);
					$dataFirma  = Array();
					
					foreach($aDataFirma as $items){
						if($items['ID_ELEMENTO']==274){
							@$dataFirma['NCLIENTE'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==275){
							@$dataFirma['OBSERVACIONES'] = $items['CONTESTACION'];		
						}else if($items['ID_ELEMENTO']==276){
							@$dataFirma['FCLIENTE'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==283){
							@$dataFirma['FQRCODE'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==282){
							@$dataFirma['TCONTESTA'] = $items['CONTESTACION'];		
						}
					}
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Observaciones'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$rowControl++;
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($dataFirma['OBSERVACIONES']));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':J'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);						
					$rowControl++;
					$rowControl++;

					/**
					 * Firmas
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Firmas'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B'.$rowControl.':J'.$rowControl);
					$rowControl++;
					$rowControl++;					
					
					if(@$dataFirma['TCONTESTA'] == 'FIRMA'){
						$exist_file = file_exists($this->realPath.$dataFirma['FCLIENTE']);	

						if($exist_file== true && $dataFirma['FCLIENTE']!="") {						
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
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$rowControl, 'imagen no disponible');								
							$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':F'.$rowControl);
						}
					}else if(@$dataFirma['TCONTESTA'] == 'QR'){
						$qrExist    = file_exists($this->realPath."/movi/".$dataFirma['FQRCODE'].".png");	
						if($qrExist== true && $dataFirma['FQRCODE']!="") {
							$objDrawing = new PHPExcel_Worksheet_Drawing();
							
							$objDrawing->setName('Picture1');
							$objDrawing->setDescription('Picture1');
							
							$objDrawing->setPath($this->realPath."/movi/".$dataFirma['FQRCODE'].".png");
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
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$rowControl, $dataFirma['FQRCODE']);								
							$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':F'.$rowControl);										
						}
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
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
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
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', 'Sucursal');								
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C9', @$dataCita['SUCURSAL']);
					$objPHPExcel->getActiveSheet()->mergeCells('C9:D9');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'C9:D9');
							
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E9', 'Tecnico');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F9', @$dataCita['NOMBRE_TECNICO']);
					$objPHPExcel->getActiveSheet()->mergeCells('F9:H9');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'F9:H9');					
					/*
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', utf8_decode('REVISION'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTextBlack, 'B9:B9');					
					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'D9:D9');					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E9', utf8_decode('Corporativo (Mexico)'));
					$objPHPExcel->getActiveSheet()->mergeCells('E9:F9');	
					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBorderOrange, 'G9:G9');					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H9', utf8_decode('Sucursal'));
					$objPHPExcel->getActiveSheet()->mergeCells('H9:H9');*/
	
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I8', utf8_decode('Folio: '));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'J8:J8');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J8', ($dataCita['FOLIO']));
					$objPHPExcel->getActiveSheet()->mergeCells('J8:J8');
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I9', utf8_decode('Tipo de Servicio:'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'J9:J9');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J9', ($dataCita['TIPO_CITA']));
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
	
					$rowControl = $rowControl+2;
																														
					$aDataFirma = $cCitas->getDataSendbyForms($this->dataIn['strInput'],16);
					$dataFirma  = Array();
					
					foreach($aDataFirma as $items){
						if($items['ID_ELEMENTO']==216){
							@$dataFirma['NCLIENTE'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==217){
							@$dataFirma['OBSERVACIONES'] = $items['CONTESTACION'];		
						}else if($items['ID_ELEMENTO']==218){
							@$dataFirma['FCLIENTE'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==283){
							@$dataFirma['FQRCODE'] = $items['CONTESTACION'];
						}else if($items['ID_ELEMENTO']==282){
							@$dataFirma['TCONTESTA'] = $items['CONTESTACION'];		
						}
					}					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Observaciones'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$rowControl++;
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, ($dataFirma['OBSERVACIONES']));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':J'.$rowControl)
							->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);						
					$rowControl++;
					$rowControl++;					
					
					/**
					 * FIRMAS
					 **/
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B'.$rowControl.':J'.$rowControl);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$rowControl, utf8_decode('Firmas'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);						
					$rowControl++;							

					if(@$dataFirma['TCONTESTA'] == 'FIRMA'){
						$exist_file = file_exists($this->realPath.$dataFirma['FCLIENTE']);	

						if($exist_file== true && $dataFirma['FCLIENTE']!="") {						
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
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$rowControl, 'imagen no disponible');								
							$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':F'.$rowControl);
						}
					}else if(@$dataFirma['TCONTESTA'] == 'QR'){
						$qrExist    = file_exists($this->realPath."/movi/".$dataFirma['FQRCODE'].".png");	
						if($qrExist== true && $dataFirma['FQRCODE']!="") {
							$objDrawing = new PHPExcel_Worksheet_Drawing();
							
							$objDrawing->setName('Picture1');
							$objDrawing->setDescription('Picture1');
							
							$objDrawing->setPath($this->realPath."/movi/".$dataFirma['FQRCODE'].".png");
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
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$rowControl, $dataFirma['FQRCODE']);								
							$objPHPExcel->getActiveSheet()->mergeCells('C'.$rowControl.':F'.$rowControl);										
						}
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
					$sHeaderBig    = new PHPExcel_Style();
					$sHeaderBlack  = new PHPExcel_Style();
					$sTextBlack    = new PHPExcel_Style();
					$sHeaderOrange = new PHPExcel_Style();					
					$sBorderOrange = new PHPExcel_Style();
					$sBordersBottom= new PHPExcel_Style();
					$sTittleOrange = new PHPExcel_Style();
					$sTextOrange   = new PHPExcel_Style();
					$sOrangeSubTittle= new PHPExcel_Style();
					
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
					
					$sOrangeSubTittle->applyFromArray(array(
						'fill' => array(
				            'type' => PHPExcel_Style_Fill::FILL_SOLID,
				            'color' => array('rgb' => 'FFFFFF')
				        ),
				        'font'  => array(
					        'bold'  => true,
					        'color' => array('rgb' => '000000'),
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
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B7', utf8_decode('ORDEN DE SERVICIO '));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sHeaderBlack, 'B7:J7');
					$objPHPExcel->getActiveSheet()->mergeCells('B7:G7');

						/*																			
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I8', utf8_decode('Folio: '));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'J8:J8');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J8', ($dataCita['FOLIO']));
					$objPHPExcel->getActiveSheet()->mergeCells('J8:J8');
					*/	
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I7', utf8_decode('Tipo de Servicio:'));
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'J7:J7');
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J7', ($dataCita['TIPO_CITA']));
					$objPHPExcel->getActiveSheet()->mergeCells('J7:J7');					

					/**
					 * Datos Generales de la Cita
					 **/
					
					/*
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sTittleOrange, 'B9:J9');						
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B9', utf8_decode('Datos generales'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B9:J9');*/

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B10', utf8_decode('Folio SAP:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B10:C10');					
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D10', ($dataCita['FOLIO']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D10:G10');					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D10:G10');
					$objPHPExcel->getActiveSheet()->getStyle('D10:G10')
													->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);					

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H10', utf8_decode('Fecha:'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('H13:J13');						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I10',  ($dataCita['FECHA_CITA']));	
					$objPHPExcel->getActiveSheet()->mergeCells('I10:J10');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I10:J10');	
									
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B11', ('Cliente:'));	
					$objPHPExcel->getActiveSheet()->mergeCells('B11:C11');
											
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D11', ($dataCita['NOMBRE_CLIENTE']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D11:G11');					
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D11:G11');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H11', utf8_decode('Hora:'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('H13:J13');						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I11',  ($dataCita['HORA_CITA']));	
					$objPHPExcel->getActiveSheet()->mergeCells('I11:J11');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I11:J11');	

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B12', utf8_encode('Direccion: '));	
					$objPHPExcel->getActiveSheet()->mergeCells('B12:C12');		
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D12', ($dataCita['DIRECCION_CITA1']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D12:G12');	
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D12:G12');					

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D13', ($dataCita['DIRECCION_CITA2']));
					$objPHPExcel->getActiveSheet()->mergeCells('D13:G13');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D13:F13');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H12', utf8_decode('Fecha Inicio:'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('H13:J13');						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I12',  ($dataCita['FECHA_INICIO']));	
					$objPHPExcel->getActiveSheet()->mergeCells('I12:J12');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I12:J12');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H13', utf8_decode('Fecha Fin:'));	
					//$objPHPExcel->getActiveSheet()->mergeCells('H13:J13');						

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I13',  ($dataCita['FECHA_TERMINO']));	
					$objPHPExcel->getActiveSheet()->mergeCells('I13:J13');
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'I13:J13');

					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B14', utf8_encode('Tecnico: '));	
					$objPHPExcel->getActiveSheet()->mergeCells('B14:C14');		
					
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D14', ($dataCita['NOMBRE_TECNICO']));	
					$objPHPExcel->getActiveSheet()->mergeCells('D14:G14');	
					$objPHPExcel->setActiveSheetIndex(0)->setSharedStyle($sBordersBottom, 'D14:G14');
				
					$rowControl		= 16;					
					
					$aForms = $cCitas->getFormsCita($this->dataIn['strInput']);		
					foreach($aForms as $key => $itemsForm){
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$rowControl, $itemsForm['TITULO']);
						$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
						$objPHPExcel->getActiveSheet()->setSharedStyle($sTittleOrange, 'B'.$rowControl.':J'.$rowControl);
						$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':J'.$rowControl)
												->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);						
																
						$rowControl++;
						
						$aDataForms = $cCitas->getDataSendbyForms($this->dataIn['strInput'],$itemsForm['ID_FORMULARIO']);
						
						foreach($aDataForms as $items){												
							if($items['TIPO']=='ENCABEZADO'){
								$objPHPExcel->getActiveSheet()->setCellValue('B'.$rowControl, $items['DESCRIPCION']);
								$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':J'.$rowControl);
								$objPHPExcel->getActiveSheet()->setSharedStyle($sOrangeSubTittle, 'B'.$rowControl.':J'.$rowControl);
								$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':J'.$rowControl)
												->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);								
								$rowControl++;
							}else{		
								//------ La respuesta es una foto -----						
								if($items['T_ELEMENTO']=='9' || $items['T_ELEMENTO']=='10'){
									$objPHPExcel->getActiveSheet()->setCellValue('B'.$rowControl, $items['DESCRIPCION']);
									$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);
									$objPHPExcel->getActiveSheet()->getStyle('B'.$rowControl.':E'.$rowControl)
													->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);									
									$rowControl++;
									
									$exist_file = file_exists($this->realPath.$items['CONTESTACION']); 									
									if($exist_file== true && $items['CONTESTACION']!="") {
										$objDrawing = new PHPExcel_Worksheet_Drawing();
										
										$objDrawing->setName('Picture');
										$objDrawing->setDescription('Picture');
										
										$objDrawing->setPath($this->realPath.$items['CONTESTACION']);
										$objDrawing->setWidth(120);
										$objDrawing->setOffsetX(150);
										$objDrawing->setHeight(135);
										$objDrawing->setOffsetY(-170);										
										$objDrawing->setCoordinates('F'.$rowControl);
																				
										$objPHPExcel->getActiveSheet()->getRowDimension('F'.$rowControl)->setRowHeight(150);
										$objPHPExcel->getActiveSheet()->getStyle('F'.$rowControl.':J'.$rowControl)
													->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
										$objPHPExcel->getActiveSheet()->mergeCells('F'.$rowControl.':J'.$rowControl);
										
										$objPHPExcel->getActiveSheet()->getRowDimension($rowControl)->setRowHeight(140);										
										$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
									}else{
										$objPHPExcel->getActiveSheet()->setCellValue('F'.$rowControl, "Imagen no disponible.");
										$objPHPExcel->getActiveSheet()->mergeCells('F'.$rowControl.':J'.$rowControl);									
									}
								//------ La respuesta es texto    ------	
								}else{
									$objPHPExcel->getActiveSheet()->setCellValue('B'.$rowControl, $items['DESCRIPCION']);	
									$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowControl.':E'.$rowControl);
															
									$objPHPExcel->getActiveSheet()->setCellValue('F'.$rowControl, $items['CONTESTACION']);	
									$objPHPExcel->getActiveSheet()->mergeCells('F'.$rowControl.':J'.$rowControl);	
									$objPHPExcel->getActiveSheet()->getStyle('F'.$rowControl.':J'.$rowControl)
													->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);																						
								}
								$rowControl++;
							}					
						}												
					}
					
					
					$objPHPExcel->setActiveSheetIndex(0)->setShowGridLines(false);
					$objPHPExcel->setActiveSheetIndex(0)->setPrintGridLines(false);
					
					$filename  = "Reporte_Cita_".date("YmdHi").".pdf";	
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
}