<?php
class reports_LogbookController extends My_Controller_Action
{
	/**
	 * Clave principal para identificar el modulo
	 * @var $_clase String
	 */
	protected $_clase 	  = 'mlognooks';
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
		} catch (Zend_Exception $e){
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }	
    
    public function indexAction(){
    	try{
    		$cInstalacion 	 = new My_Model_Cinstalaciones();
    		$cFunciones		 = new My_Controller_Functions();
    		$cTurnos 		 = new My_Model_Turnos(); 
    		$aInstalaciones	 = $cInstalacion->getCbo($this->_dataUser['ID_EMPRESA']);
    		$sFecha			 = date("Y-m-d h:i:s");
    		$sFechaEnd		 = date("Y-m-d 23:59:59");
    		$iSucursal		 = -1;
    		$aDataTurnos	 = Array();
    		
    		if(isset($this->_dataIn['cboInstalacion']) && isset($this->_dataIn['inputFechaIn']) && isset($this->_dataIn['inputFechaFin'])){
    			$sFecha		= $this->_dataIn['inputFechaIn'];
    			$sFechaEnd	= $this->_dataIn['inputFechaFin'];
    			$iSucursal	= $this->_dataIn['cboInstalacion'];
    		}
    		
    		$aDataTurnos  	 = $cTurnos->getTurnos($iSucursal,$sFecha,$sFechaEnd);    		
    		$this->view->aData			= $aDataTurnos;
    		$this->view->cInstalaciones = $cFunciones->selectdb($aInstalaciones);
    		$this->view->data			= $this->_dataIn;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }     	
    }
    
    public function getinfoAction(){
    	try{
    		$cTurnos 	= new My_Model_Turnos();
    		$cServicios = new My_Model_Servicios();
    		$aElemtosInicio	= Array();
    		$aElemtosFin	= Array();
    		
    		if(isset($this->_dataIn['strInput'])){
    			$idObject  = $this->_dataIn['strInput'];
    			
    			$aDatainfo = $cTurnos->getData($idObject);
    			
    			$aDataInfoIn 	= $cTurnos->getDataInfoResult($aDatainfo['ID'],1);  
    			if(count($aDataInfoIn)>0){
    				$aElemtosInicio = $cTurnos->getElementos($aDataInfoIn['ID_RESULTADO']);	
    			}	
    			
    			$aDataInfoFin 	= $cTurnos->getDataInfoResult($idObject,4);
    			if(count($aDataInfoFin)>0){
    				$aElemtosFin 	= $cTurnos->getElementos($aDataInfoFin['ID_RESULTADO']);	
    			}
    			
    			$aServicios 	= $cTurnos->getServicios($aDatainfo['ID']);
    			$aActividades 	= $cTurnos->getActividades($aDatainfo['ID']);
    			
    			$this->view->aDatainfo 		= $aDatainfo;
    			$this->view->aDataInicio 	= $aElemtosInicio;
    			$this->view->aDataFin 		= $aElemtosFin;
    			$this->view->aServicios		= $aServicios;
    			$this->view->aActividades	= $aActividades;
    		}else{
    			$this->redirect("/reports/loogbook/index");	
    		}
    	} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function detailinfoAction(){
        try{
    		$cTurnos 	= new My_Model_Turnos();
    		$cServicios = new My_Model_Servicios();
    		
    		if(isset($this->_dataIn['strInput'])){
    			$idResult =  $this->_dataIn['strInput'];
    			
    			$aDataInfo = $cServicios->getInfoForm($idResult);    			    			
    			$aRespuestas = $cTurnos->getElementos($idResult,true);
    			
    			$this->view->aData 		 = $aDataInfo;
    			$this->view->aRespuestas = $aRespuestas;
    		}else{
    			$this->redirect("/reports/loogbook/index");	
    		}
    	} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }
    
	public function exportdataAction(){
		try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
    		$cTurnos 	= new My_Model_Turnos();
    		$cServicios = new My_Model_Servicios();
    		$cHistorico	= new My_Model_Historico();
    		$aElemtosInicio	= Array();
    		$aElemtosFin	= Array();
    					
			$content = '';
		    if(isset($this->_dataIn['strInput'])){
    			$idResult  = $this->_dataIn['strInput'];    			
    			$aDatainfo = $cTurnos->getData($idResult);
    			
    			$aDataInfoIn 	= $cTurnos->getDataInfoResult($aDatainfo['ID'],1);  
    			if(count($aDataInfoIn)>0){
    				$aElemtosInicio = $cTurnos->getElementos($aDataInfoIn['ID_RESULTADO']);	
    			}	
    						
    			$aPrevActividades 	= $cTurnos->getActividadesResume($aDatainfo['ID']);
    			$aActividades 		= $this->processData($aPrevActividades);
    			$aServicios 		= $cTurnos->getServicios($aDatainfo['ID']);
    			
    			$aResume			= $cTurnos->getResumeBitacora($aDatainfo['ID']);
    			$aDataResume 		= $this->processDataResume($aResume);
    			
    			$aValuesinEnd       = Array();
    			$aDatainEnd 		= $cTurnos->getValuesInend($aDatainfo['ID']);
    			
    			/*
				$aDataHistorico = $cHistorico->getPositions($aDataInfoIn['ID_TELEFONO'],$aDatainfo['FECHA_INICIO'],$aDatainfo['FECHA_FIN']);	    			
    			if(count($aDataHistorico)>0){	    					    				
    				$aStopTravels = $cHistorico->getStopTravels($aDataHistorico);
    			}	
    			*/    			
    			
    			foreach($aDatainEnd as $key => $items){
    				if($items['ID_ELEMENTO']==11){
    					@$aValuesinEnd['odominicial']  = $items['CONTESTACION']; 
    				}else if($items['ID_ELEMENTO']==44){
						@$aValuesinEnd['odomfinal']    = $items['CONTESTACION']; 
    				}else if($items['ID_ELEMENTO']==10){
						@$aValuesinEnd['nivelgasin']   = $items['CONTESTACION']; 
    				}else if($items['ID_ELEMENTO']==45){
    					@$aValuesinEnd['nivelfasfin']  = $items['CONTESTACION']; 
    				}
    			}
    			
    			$aNotasTurno = $cTurnos->getNotasTurno($aDatainfo['ID']);
		
			    require_once($this->_publicPath.'/html_pdf/html2pdf.class.php');
			    
			    ob_start();
			    include($this->_publicPath.'/layouts/reports/header_report.html');
			    $lHeader = ob_get_clean();
			    
			    ob_start();
			    include($this->_publicPath.'/layouts/reports/footer_report.html');
			    $lFooter = ob_get_clean();
			    	
			    $tittle  = 'BIT&Aacute;CORA DE CONTROL DIARIO DE OPERACI&Oacute;N';		    
			    $lHeader = str_ireplace('0titulo0', $tittle, $lHeader);		
				$content = '<page backtop="10mm" backbottom="10mm" backleft="20mm" backright="20mm">
						    '.$lHeader.'
						    '.$lFooter.'					   
						    <br>
						    <br>
							<table width="645" style="border: solid 2px #000000;  margin-top:30px;border-radius: 2px;font-size:9px; " align="center">
							    <tr>
								 	<th width="660" colspan="6" style="text-align:center;background-color:#F2F2F2;"></th>								 
								 </tr>
								 <tr>
								 	<td><b>Inicio de Turno</b>   </td><td>'.$aDatainfo['FECHA_INICIO'].'</td>
								 	<td><b>Fin de Turno</b></td><td>'.$aDatainfo['FECHA_FIN'].'</td>
									<td colspan="2"></td>
								 </tr>
								 <tr>
								 	<td><b>Folio</b>   </td><td>'.$aDatainfo['ID'].'</td>
								 	<td><b>Jefatura</b></td><td>'.$aDataInfoIn['N_SUCURSAL'].'</td>
									<td><b>Terminal</b>   </td><td>'.$aDataInfoIn['N_PHONE'].'</td>
								 </tr>';
					$sTecnico2 = '';
								$controlIt = 1;
								foreach($aElemtosInicio as $key => $itemsInicio){
									
									if($itemsInicio['ID_ELEMENTO']==5){
										$sTecnico2 = $itemsInicio['CONTESTACION'];
									}
									
									if($controlIt==1){
										$content .= '<tr>';
									}
										$content .= '<td><b>'.$itemsInicio['DESCIPCION'].'</b></td>'.
													'<td>'.   $itemsInicio['CONTESTACION'].'</td>';
									
									if($controlIt==3){
										$content .= '</tr>';
										$controlIt=1;
									}else{
										$controlIt++;
									}
								}
		
								$content .= ($controlIt!=1) ? '</tr>': '';
								$totalDistancia = intval(@$aValuesinEnd['odomfinal']) - intval(@$aValuesinEnd['odominicial']);
								$content .= '<tr>
											 	<td><b>Odometro Inicial</b></td><td>'.@$aValuesinEnd['odominicial'].'</td>
											 	<td><b>Odometro Final  </b></td><td>'.@$aValuesinEnd['odomfinal'].'</td>
												<td><b>Kms Recorridos  </b></td><td>'.$totalDistancia.' kms.</td>
											 </tr>';
								$content .= '<tr>
											 	<td><b>Nivel Gasolina Inicial</b></td><td>'.@$aValuesinEnd['nivelgasin'].'</td>
											 	<td><b>Nivel Gasolina Final  </b></td><td>'.@$aValuesinEnd['nivelfasfin'].'</td>
												<td colspan="2"></td>
											 </tr>';								
						$content .= '</table><br>';
								
						$content .= '<table cellspacing="0" style="width:100%;border: solid 1px #000000;font-size:9px;"  align="center" >
									<thead>
										<tr>
											<th style="text-align:center;width:18%;border: solid .5px #000000;background-color:#F2F2F2;" >HORA</th>
											<th style="text-align:center;width:30%;border: solid .5px #000000;background-color:#F2F2F2;" >LOCALIDAD</th>
											<th style="text-align:center;width:43%;border: solid .5px #000000;background-color:#F2F2F2;" align="center">ACTIVIDADES Y SERVICIOS</th>
											<th style="text-align:center;width:8%;border: solid .5px #000000;background-color:#F2F2F2;" align="center">LITROS</th>
											<th style="text-align:center;width:8%;border: solid .5px #000000;background-color:#F2F2F2;" align="center">IMPORTE</th>
										</tr>									
									</thead>
									<tbody>';
											
						foreach($aActividades as $key => $items){
							$aDireccion = explode(",",$items['UBICACION']);
							$sDireccion = $aDireccion[0].", ".$aDireccion[1]."<br/>".$aDireccion[2].", ".$aDireccion[3];
							$content .= '<tr>
											<td style="text-align:center;border: solid .5px #000000;">'.$items['FECHA'].'</td>
											<td style="border: solid .5px #000000;">'.$sDireccion.'</td>
											<td style="border: solid .5px #000000;">'.$items['OPCION1'].' - '.$items['OPCION2'].'</td>
											<td style="border: solid .5px #000000;text-align:center;">'.$items['LITROS'].' Lts.</td>
											<td style="border: solid .5px #000000;text-align:center;">$ '.$items['IMPORTE'].'</td>
										</tr>';	
						}
						
						$content .= '</tbody></table><br/>';
						
						$content .= '<table cellspacing="0" style="width:100%;border: solid 1px #000000;font-size:9px;"  align="center" >
										<tr>
											<td style="text-align:center;background-color:#F2F2F2;" colspan="4" align="center">
												RESUMEN DE SERVICIOS DE ASISTENCIA Y AUXILIO TURÍSTICO PROPORCIONADOS
											</td>
										</tr>
										<tr>
											<td style="width:26%;text-align:center;border: solid .5px #000000;">TOTAL SERVICIOS PROPORCIONADOS</td>
											<td style="width:27%;text-align:center;border: solid .5px #000000;">VEHÍCULOS ATENDIDOS</td>
											<td style="width:27%;text-align:center;border: solid .5px #000000;">TOTAL TURISTAS ATENDIDOS</td>
											<td style="width:27%;text-align:center;border: solid .5px #000000;">ACCIDENTES ATENDIDOS</td>
										</tr>	
										<tr>
											<td style="text-align:center;border: solid .5px #000000;">'.count($aServicios).'</td>
											<td style="text-align:center;border: solid .5px #000000;">'.$aDataResume['V_ATENDIDOS'].'</td>
											<td style="text-align:center;border: solid .5px #000000;">'.$aDataResume['TOTAL_PERSONAS'].'</td>
											<td style="text-align:center;border: solid .5px #000000;">'.$aDataResume['ACCIDENTES'].'</td>
										</tr>
									</table><br/>'.
									'<table cellspacing="0" style="width:100%;border: solid 1px #000000;font-size:9px;"  align="center" >
										<tr>
											<td style="text-align:center;background-color:#F2F2F2;" colspan="5" align="center">
												OPINIÓN DE LOS TURISTAS POR LOS SERVICIOS PROPORCIONADOS
											</td>
										</tr>
										<tr>
											<td style="width:21%;text-align:center;border: solid .5px #000000;">TOTAL SERVICIOS</td>
											<td style="width:22%;text-align:center;border: solid .5px #000000;">EXCELENTE</td>
											<td style="width:21%;text-align:center;border: solid .5px #000000;">BUENO</td>
											<td style="width:22%;text-align:center;border: solid .5px #000000;">REGULAR</td>
											<td style="width:21%;text-align:center;border: solid .5px #000000;">MALO</td>
										</tr>	
										<tr>
											<td style="text-align:center;border: solid .5px #000000;">'.count($aServicios).'</td>
											<td style="text-align:center;border: solid .5px #000000;">'.$aDataResume['EXCELENTE'].'</td>
											<td style="text-align:center;border: solid .5px #000000;">'.$aDataResume['BUENO'].'</td>
											<td style="text-align:center;border: solid .5px #000000;">'.$aDataResume['REGULAR'].'</td>
											<td style="text-align:center;border: solid .5px #000000;">'.$aDataResume['MALO'].'</td>
										</tr>
									</table><br/>';
						
						$content .= '<table cellspacing="0" style="width:645px;border: solid 1px #000000;font-size:9px;" >
										<tr>
											<td style="width:645px;text-align:center;background-color:#F2F2F2;" align="center">
												Notas del Turno
											</td>
										</tr>
										<tr>
											<td style="width:658px;text-align:left;">
												'.@$aNotasTurno['CONTESTACION'].'
											</td>
										</tr>										
										</table><br/><br/><br/><br/><br/><br/>';
						
						
						$content .= '<table cellspacing="0" style="width:645px;border: solid 0px #000000;font-size:9px;" >
										<tr>
											<td style="height:100px;width:320px;text-align:center;border: solid .5px #000000;" align="center">
												
											</td>
											<td style="width:10px;text-align:center;" align="center">
											
											</td>
											<td style="width:320px;text-align:center;border: solid .5px #000000;" align="center">
											
											</td>
										</tr>
										<tr>
											<td  style="width:21%;text-align:center;border: solid .5px #000000;">
												'.$aDataInfoIn['N_USUARIO'].'<br/>
												Tecnico 1
											</td>
											<td style="width:10px;text-align:center;" align="center">
											
											</td>
											<td align="center"  style="width:21%;text-align:center;border: solid .5px #000000;">
												'.$sTecnico2.'<br/>
												Tecnico 2
											</td>
										</tr>
									</table><br/>';
												
						$content .='</page>';
			    try
			    {
			        $html2pdf = new HTML2PDF('P', 'A4', 'es', true, 'UTF-8', 3);
			        $html2pdf->pdf->SetDisplayMode('fullpage');
			        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
			        $html2pdf->Output('exemple03.pdf');
			    }
			    catch(HTML2PDF_exception $e) {
			        echo $e;
			        exit;
			    }    			
    			
    		}else{
    			$this->redirect("/reports/loogbook/index");	
    		}			    		
		}catch(Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
		}	
    }
    
    public function processData($aData){
    	$result = Array();
    	$aDataProcess = Array();
    	
    	foreach($aData as $key => $items){
    		@$aDataProcess[$items['ID_RESULTADO']]['ID'] 		= $items['ID_RESULTADO'];
    		@$aDataProcess[$items['ID_RESULTADO']]['FECHA'] 	= $items['FECHA_CAPTURA_EQUIPO'];
    		@$aDataProcess[$items['ID_RESULTADO']]['UBICACION'] = $items['UBICACION'];
    		
    		if($items['ID_ELEMENTO']==37){
    			@$aDataProcess[$items['ID_RESULTADO']]['OPCION1'] = $items['CONTESTACION'];	
    		}
    		
    	    if($items['ID_ELEMENTO']==38){
    			@$aDataProcess[$items['ID_RESULTADO']]['LITROS'] = $items['CONTESTACION'];	
    		}    	

    	    if($items['ID_ELEMENTO']==39){
    			@$aDataProcess[$items['ID_RESULTADO']]['IMPORTE'] = $items['CONTESTACION'];	
    		}        		
    		
    		if($items['ID_ELEMENTO']==42){
    			@$aDataProcess[$items['ID_RESULTADO']]['OPCION2'] = $items['CONTESTACION'];	
    		}  
    	 		    		    		
    	}
   		$result = $aDataProcess;
    	
    	return $result;
    }
    
    public function processDataResume($aData){
    	$result = Array(); 
    	$result['TOTAL_PERSONAS'] = 0;
    	$result['V_ATENDIDOS'] 	  = 0;
    	$result['ACCIDENTES'] 	  = 0;
    	$result['EXCELENTE'] 	  = 0;
		//$result['MUY BUENO'] 	  = 0;
		$result['BUENO'] 	  	  = 0;
		$result['REGULAR'] 	  	  = 0;
		$result['MALO'] 	  	  = 0;
    		    	
    	foreach($aData as $key => $items){
    		if($items['ID_ELEMENTO']==31 || $items['ID_ELEMENTO']==32 ){
    			@$result['TOTAL_PERSONAS'] += $items['CONTESTACION'];	
    		}
    		
    		if($items['ID_ELEMENTO']==13 || ($items['CONTESTACION']=='Arrastre' || $items['CONTESTACION']=='Asistencia Mecánica' )){
    			@$result['V_ATENDIDOS'] += 1;	
    		}

    		if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Accidente'){
    			@$result['ACCIDENTES'] += 1;	
    		}  

    		if($items['ID_ELEMENTO']==34 && $items['CONTESTACION']=='Excelente'){
    			@$result['EXCELENTE'] += 1;	
    		}else if($items['ID_ELEMENTO']==34 && $items['CONTESTACION']=='Bueno'){
    			@$result['BUENO'] += 1;
    		}else if($items['ID_ELEMENTO']==34 && $items['CONTESTACION']=='Regular'){
    			@$result['REGULAR'] += 1;
    		}else if($items['ID_ELEMENTO']==34 && $items['CONTESTACION']=='Malo'){    			
    			@$result['MALO'] += 1;
    		} 
    	}   		
    	return $result;
    }    
    
    public function exportservicesAction(){
    	try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
    		$cTurnos 	= new My_Model_Turnos();
    		$cServicios = new My_Model_Servicios();
    		$aElemtosInicio	= Array();
    		$aElemtosFin	= Array();
    					
			$content = '';
		    if(isset($this->_dataIn['strInput'])){
    			$idResult  = $this->_dataIn['strInput'];    			
    			$aDatainfo = $cTurnos->getData($idResult);
    			
    			$aDataInfoIn 	= $cTurnos->getDataInfoResult($aDatainfo['ID'],1);  
    			if(count($aDataInfoIn)>0){
    				$aElemtosInicio = $cTurnos->getElementos($aDataInfoIn['ID_RESULTADO']);	
    			}    			
    				            		
    			$aServicios 		= $cTurnos->getServicios($aDatainfo['ID']);
    			
    			if(count($aServicios)>0){
    				
				    require_once($this->_publicPath.'/html_pdf/html2pdf.class.php');
				    
				    ob_start();
				    include($this->_publicPath.'/layouts/reports/header_report.html');
				    $lHeader = ob_get_clean();
				    
				    ob_start();
				    include($this->_publicPath.'/layouts/reports/footer_report.html');
				    $lFooter = ob_get_clean();	
		
					$tittle  = 'CONTROL DE SERVICIOS GRATUITOS DE ASISTENCIA Y AUXILIO TURISTICO';		    
				    $lHeader = str_ireplace('0titulo0', $tittle, $lHeader);
	
				    foreach($aServicios as $key => $items){	
				    	$aElemtosForm 	= Array();    						
		    			$aElemtosForm 	= $cTurnos->getElementos($items['ID_RESULTADO']);
				    	$aElemtosServices= $this->processDataServices($aElemtosForm);
				    				    	
						$content .= '<page backtop="10mm" backbottom="10mm" backleft="20mm" backright="20mm">
								    '.$lHeader.'
								    '.$lFooter.'					   
								    <br>';
						
						$content .='<table style="width: 100%;border: solid 1px #000000;  margin-top:30px;border-radius: 2px;font-size:9px; " align="center">
									 <tbody>
									 <tr>
									 	<th width="645" colspan="6" style="text-align:center;background-color:#F2F2F2;">I.- UNIDAD Y SERVIDORES P&Uacute;BLICOS QUE BRINDAN EL SERVICIO</th>								 
									 </tr>
									 <tr>
									 	<td><b>Folio Bitacora</b></td><td>'.$aDatainfo['ID'].'</td>
									 	<td><b>Folio Servicio</b></td><td>'.$aDataInfoIn['ID_RESULTADO'].'</td>
										<td></td><td></td>
									 </tr>
									 <tr>								 	
									 	<td><b>Jefatura</b></td><td>'.$aDataInfoIn['N_SUCURSAL'].'</td>
										<td><b>Terminal</b>   </td><td>'.$aDataInfoIn['N_PHONE'].'</td>
										<td></td><td></td>
									 </tr>';
						
						$controlIt = 1;
						foreach($aElemtosInicio as $key => $itemsInicio){
							if($controlIt==1){
								$content .= '<tr>';
							}
								$content .= '<td><b>'.$itemsInicio['DESCIPCION'].'</b></td>'.
											'<td>'.   $itemsInicio['CONTESTACION'].'</td>';
							
							if($controlIt==3){
								$content .= '</tr>';
								$controlIt=1;
							}else{
								$controlIt++;
							}
						}
						$sUbicacion = explode(',',$items['UBICACION']);
						$sDireccion = ($items['UBICACION']!="Sin Ubicacion") ? @$sUbicacion[0].','.@$sUbicacion[1].'<br/>'.@$sUbicacion[2].','.@$sUbicacion[3]: 'Sin direccion'; 
						$content .= ($controlIt>1) ? '</tr>': '';
						$content .= '</tbody>
									  </table>';					
						
						$content .= '<table style="width: 100%;border: solid 1px #000000;  margin-top:10px;border-radius: 2px;font-size:9px; " align="center">
									 <tr><td style="width:25%;text-align:center;background-color:#F2F2F2;">II.- LUGAR DE SERVICIO</td>
									 	 <td style="width:25%;text-align:center;background-color:#F2F2F2;">III.- FECHA DE SERVICIO</td>
									 	 <td style="width:335px;text-align:center;background-color:#F2F2F2;">IV.- VEH&Iacute;CULO ATENDIDO</td></tr>
									 <tr>
										<td>'.$sDireccion.'</td>
										<td style="text-align:center;">'.$items['FECHA_CAPTURA_EQUIPO'].'</td>
										<td> 
											<table celspacing="1" style="font-size:9px; width:550px;">
												<tr style="border: solid .5px #000000;">
													<th style="text-align:center;background-color:#F2F2F2;">MARCA</th>
													<th style="text-align:center;background-color:#F2F2F2;">SUBMARCA</th>
													<th style="text-align:center;background-color:#F2F2F2;">A&Ntilde;O</th>
													<th style="text-align:center;background-color:#F2F2F2;">PLACAS</th>
													<th style="width:70px;text-align:center;background-color:#F2F2F2;">ESTADO</th>
												</tr>
												<tr>
													<td style="text-align:center;width:16%;">'.$aElemtosServices['MARCA'].'</td>
													<td style="text-align:center;width:10%;">'.$aElemtosServices['SUBMARCA'].'</td>
													<td style="text-align:center;width:5%;">'.$aElemtosServices['ANO'].'</td>
													<td style="text-align:center;">'.$aElemtosServices['PLACAS'].'</td>
													<td style="text-align:center;width:18%;">'.$aElemtosServices['ESTADO'].'</td>
												</tr>
											</table>
										</td>
									</tr></table>';
						
						$content .= '<table style="width: 100%;border: solid 1px #000000;  margin-top:10px;border-radius: 2px;font-size:9px; " align="center">
									 <tr><td style="width:50%;text-align:center;background-color:#F2F2F2;">V.- TURISTAS ATENDIDOS Y NACIONALIDAD</td>
									 	 <td style="width:55%;text-align:center;background-color:#F2F2F2;">VI.- TIPO DE SERVICIO</td></tr>
									 <tr>
									 	<td style="text-align:center;" align="center">								 	
											<table style="font-size:9px;width:500px;"">
												<tr style="border: solid .5px #000000;">
													<th style="text-align:center;background-color:#F2F2F2;">TOTAL</th>
													<th style="text-align:center;background-color:#F2F2F2;">HOMBRES</th>
													<th style="text-align:center;background-color:#F2F2F2;">MUJERES</th>
													<th style="text-align:center;background-color:#F2F2F2;">MEXICANA</th>
													<th style="text-align:center;background-color:#F2F2F2;">EXTRANJERA</th>
												</tr>
												<tr>
													<td style="width:12%;text-align:center;">'.$aElemtosServices['TOTAL_PERSONAS'].'</td>
													<td style="width:12%;text-align:center;">'.$aElemtosServices['TOTAL_H'].'</td>
													<td style="width:12%;text-align:center;">'.$aElemtosServices['TOTAL_M'].'</td>
													<td style="text-align:center;">'.$aElemtosServices['TOTAL_MX'].'</td>
													<td style="width:12%;text-align:center;">'.$aElemtosServices['TOTAL_EXT'].'</td>
												</tr>
											 </table>								 	
									 	
									 	</td>
									 	<td style="text-align:center;">
									 		<table style="font-size:8px; width:100%;">
												<tr>
													<td  style="width:300px;text-align:center;font-size:12px;font-weight:bold;">
														'.@$aElemtosServices['T_SERVICIO'].'
													</td>';								
						/*											<tr style="border: solid .5px #000000;">												
													<th style="text-align:center;background-color:#F2F2F2;">Informaci&oacute;n Tur&iacute;stica</th>
													<th style="text-align:center;background-color:#F2F2F2;">Primeros Auxilios</th>
													<th style="text-align:center;background-color:#F2F2F2;">Asistencia Mec&aacute;nica</th>
													<th style="text-align:center;background-color:#F2F2F2;">Accidente</th>
													<th style="text-align:center;background-color:#F2F2F2;">Arrastre</th>
												</tr>-->*/
						
						$content .=             '</tr>
												<tr>
													<td>'.$aElemtosServices['T_MOT'].'</td>												
												</tr>
											 </table>
									 	</td></tr></table>
									<div style="width: 105%;border: solid 1px #000000;  margin-top:10px;border-radius: 2px;font-size:10px;text-align:center; padding:5px;" > 
										<p>ESTIMADO TURISTA: la Secretar&iacute;a de Turismo a trav&eacute;s de los &Aacute;ngeles Verdes le proporciona gratuitamente servicios de orientaci&oacute;n tur&iacute;stica, asistencia y auxilio mec&aacute;nico de emergencia. No es responsabilidad de SECTUR el suministro y costo de refacciones, combustible &oacute; lubricantes requeridos para solventar la reparaci&oacute;n de su veh&iacute;culo. PARA MEJORAR NUESTROS SERVICIOS Y ACERCARNOS M&Aacute;S A USTED, le solicitamos atentamente nos proporcione la siguiente informaci&oacute;n:</p>
									</div>';  
						
						
						$content .= '<table style="width: 100%;border: solid 1px #000000;  margin-top:10px;border-radius: 2px;font-size:9px; " align="center">
									 <tr><td style="width:50%;text-align:center;background-color:#F2F2F2;">VII.- OPINI&Oacute;N DEL TURISTA POR EL SERVICIO RECIBIDO</td>
									 	 <td style="width:55%;text-align:center;background-color:#F2F2F2;">VIII.- DATOS DEL TURISTA</td></tr>
									 <tr>
									 	<td>
											<table  style="font-size:9px;">
												<tr style="border: solid .5px #000000;">
													<th style="text-align:center;background-color:#F2F2F2;">EXCELENTE</th>
													<th style="text-align:center;background-color:#F2F2F2;">BUENO</th>
													<th style="text-align:center;background-color:#F2F2F2;">REGULAR</th>
													<th style="text-align:center;background-color:#F2F2F2;">MALO</th>
												</tr>
												<tr>
													<td width="20" height="35" style="text-align:center;">'.$aElemtosServices['EXCELENTE'].'</td>
													<td width="20" style="text-align:center;">'.$aElemtosServices['BUENO'].'</td>
													<td width="20" style="text-align:center;">'.$aElemtosServices['REGULAR'].'</td>
													<td width="20" style="text-align:center;">'.$aElemtosServices['MALO'].'</td>
												</tr>
												<tr>
													<td colspan="4" width="255"> COMENTARIO &Oacute; SUGERENCIA</td>
												</tr>
												<tr>
													<td colspan="4" width="255"> '.$aElemtosServices['COMENTARIO'].'</td>
												</tr>
											 </table>								 									 	
									 	</td>								 
									 	<td>
									 		<table style="font-size:8px; width:100%;">
												<tr><td>NOMBRE Y APELLIDO:</td>	<td>'.$aElemtosServices['NOMBRE'].' '.$aElemtosServices['APPS'].'</td></tr>											
												<tr><td>CIUDAD:</td>			<td>'.$aElemtosServices['CIUDAD'].'</td></tr>
												<tr><td>ESTADO:</td>			<td>'.$aElemtosServices['ESTADO'].'</td></tr>
												<tr><td>PA&Iacute;S:</td>		<td>'.$aElemtosServices['PAIS'].'</td></tr>
												<tr><td>TELÉFONO:</td>			<td>'.$aElemtosServices['TEL'].'</td></tr>
												<tr><td>CORREO ELECTR&Oacute;NICO:</td><td>'.$aElemtosServices['MAIL'].'</td></tr>
											 </table>
									 	</td></tr></table>'; 					
				    	$content .= '</page>';	
				    }				
				    try
				    {
				    	$sNameFile = 'Volante_Servicios.pdf';
				    	header("Content-Disposition: attachment; $sNameFile");
				    	
				        $html2pdf = new HTML2PDF('P', 'A4', 'es', true, 'UTF-8', 3);
				        $html2pdf->pdf->SetDisplayMode('fullpage');
				        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
				        $html2pdf->Output($sNameFile);
				    }
				    catch(HTML2PDF_exception $e) {
				        echo $e;
				        exit;
				    }  
    			}else{
    				echo "Sin información";
    			}    			    		   		
    		}else{
    			$this->redirect("/reports/loogbook/index");	
    		}			    		
		}catch(Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
		}
    }
    
   public function processDataServices($aData){
    	$result = Array();
    	 
    	$result['TOTAL_PERSONAS']= 0;
    	$result['TOTAL_H']		= 0;
    	$result['TOTAL_M']		= 0;
    	$result['TOTAL_MX']		= 0;
    	$result['TOTAL_EXT']	= 0;
    	$result['MARCA'] 		= '';
    	$result['SUBMARCA'] 	= '';
    	$result['ANO'] 	  		= '';
    	$result['PLACAS'] 		= '';    	
		$result['ESTADO'] 		= '';
    	$result['EXCELENTE'] 	= '';
		$result['BUENO'] 	  	= '';
		$result['REGULAR'] 	  	= '';
		$result['MALO'] 	  	= '';
		$result['COMENTARIO']	= '';
		$result['T_ACT']		= '';
		$result['T_ARR']		= '';
		$result['T_AMEC']		= '';
		$result['T_ITUR']		= '';	
		$result['T_PA']			= '';
		$result['T_MOT']		= '';
		
		$result['NOMBRE']		= '';
		$result['APPS']			= '';
		$result['CIUDAD']		= '';
		$result['ESTADO']		= '';
		$result['PAIS']			= '';
		$result['TEL']			= '';
		$result['MAIL']			= '';
		
		$result['T_SERVICIO']	= '';
    		    	
    	foreach($aData as $key => $items){
    		if($items['ID_ELEMENTO']==25){
    			$result['MARCA'] 	= $items['CONTESTACION'];	
    		}else if($items['ID_ELEMENTO']==26){
				$result['SUBMARCA'] = $items['CONTESTACION'];	
    		}else if($items['ID_ELEMENTO']==27){
    			$result['ANO'] 	  	= $items['CONTESTACION'];	
    		}else if($items['ID_ELEMENTO']==28){
    			$result['PLACAS'] 	= $items['CONTESTACION'];	   
    		}else if($items['ID_ELEMENTO']==29){
    			$result['ESTADO'] 	= $items['CONTESTACION'];	   					
    		} 
    		
    		if($items['ID_ELEMENTO']==31 || $items['ID_ELEMENTO']==32 ){
    			@$result['TOTAL_PERSONAS'] += $items['CONTESTACION'];	
    		}
    		
			if($items['ID_ELEMENTO']==34 && $items['CONTESTACION']=='Excelente'){
    			@$result['EXCELENTE'] = "X";
    		}else if($items['ID_ELEMENTO']==34 && $items['CONTESTACION']=='Bueno'){
    			@$result['BUENO'] 	  = "X";
    		}else if($items['ID_ELEMENTO']==34 && $items['CONTESTACION']=='Regular'){
    			@$result['REGULAR']   = "X";
    		}else if($items['ID_ELEMENTO']==34 && $items['CONTESTACION']=='Malo'){    			
    			@$result['MALO']  	  = "X";
    		}     	

    		if($items['ID_ELEMENTO']==35){
    			$result['COMENTARIO'] 	= $items['CONTESTACION'];						
    		}
    		
    		if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Accidente'){
    			@$result['T_SERVICIO'] = "Accidente";
    		}else if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Arrastre'){
    			@$result['T_SERVICIO'] = "Arrastre";
    		}else if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Asistencia Mecánica'){
    			@$result['T_SERVICIO'] = "Asistencia Mecánica";
			}else if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Información Turistica'){
    			@$result['T_SERVICIO'] = "Información Turistica";
    		}else if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Primeros Auxilios'){    					
    			@$result['T_SERVICIO'] = "Primeros Auxilios";
			}else if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Accidente'){    					
    			@$result['T_SERVICIO'] = "Accidente";
    		}    		
    		/*
    	    if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Accidente'){
    			@$result['T_ACT'] = "X";
    		}else if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Arrastre'){
    			@$result['T_ARR'] = "X";
    		}else if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Asistencia Mecánica'){
    			@$result['T_AMEC'] = "X";
			}else if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Información Turistica'){
    			@$result['T_ITUR'] = "X";
    		}else if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Primeros Auxilios'){    					
    			@$result['T_PA'] = "X";
			}else if($items['ID_ELEMENTO']==13 && $items['CONTESTACION']=='Accidente'){    					
    			@$result['T_MOT'] = $items['CONTESTACION'];			
    		}	
    		
    		*/
    		
    		if($items['ID_ELEMENTO']==17){
				$result['NOMBRE']	= $items['CONTESTACION'];		
    		}
    		if($items['ID_ELEMENTO']==18){
				$result['APPS']		= $items['CONTESTACION'];		
    		}    		
    		if($items['ID_ELEMENTO']==19){
				$result['CIUDAD']	= $items['CONTESTACION'];		
    		}				
			if($items['ID_ELEMENTO']==20){	
				$result['ESTADO']	= $items['CONTESTACION'];		
			}	
			if($items['ID_ELEMENTO']==21){	
				$result['PAIS']		= $items['CONTESTACION'];		
			}			

			if($items['ID_ELEMENTO']==22){	
				$result['TEL']		= $items['CONTESTACION'];		
			}			
			if($items['ID_ELEMENTO']==23){	
				$result['MAIL']		= $items['CONTESTACION'];		
			}    	

			if($items['ID_ELEMENTO']==31){
				$result['TOTAL_H']	= $items['CONTESTACION'];		
    		}
    		if($items['ID_ELEMENTO']==32){
				$result['TOTAL_M']		= $items['CONTESTACION'];		
    		} 			
			
    	  	if($items['ID_ELEMENTO']==33 && $items['CONTESTACION'] == 'mexicana'){
    			@$result['TOTAL_MX']  += 1;	
    		}else if($items['ID_ELEMENTO']==33 && $items['CONTESTACION'] != 'mexicana'){
    			@$result['TOTAL_EXT'] += 1;	
    		}
    	}
    	return $result;
    }    
    
    public function historicoAction(){
    	try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
    		$cTurnos 	= new My_Model_Turnos();
    		$cServicios = new My_Model_Servicios();
			$cHistorico = new My_Model_Historico();
    					
			$content = '';
		    if(isset($this->_dataIn['strInput'])){
    			$idResult  = $this->_dataIn['strInput'];    			
    			$aDatainfo = $cTurnos->getData($idResult);    			
    			$aDataPhone= $cTurnos->getPhoneResult($idResult);    			
    			if(count($aDataPhone)>0){
 	    			$aDataHistorico = $cHistorico->getPositions($aDataPhone['ID_TELEFONO'],$aDataPhone['FECHA_INICIO'],$aDataPhone['FECHA_FIN']);	    			
	    			if(count($aDataHistorico)>0){	    				
	    				//$aResumen 	  = $cHistorico->getResumen($aDataHistorico);	    				
	    				$aStopTravels = $cHistorico->getStopTravels($aDataHistorico);
	    				
						$sCoordenadas = '';
						$aDataFirst   = Array();
						$aDataLast	  = Array();
						$totalParadas = 0;
						
						$sTimeStop 	  = 0;
						$sTimeRun	  = 0;
						$sTotalTime   = 0;
						
						$sDistancia	  = 0;
						if(count($aStopTravels)>1){
							foreach($aStopTravels as $key => $items){
		    					if($items['tipo']==0){
		    						$totalParadas++;	
		    						$sTimeStop += $items['tiempo'];																	    					
		    					}elseif($items['tipo']==1){
		    						$sTimeRun += $items['tiempo'];
		    					}
		    					
		    					if($sCoordenadas!=''){
		    						$sCoordenadas .= '|';
		    						$aDataLast  = $items['fin'];
		    					}else{	    						
		    						$aDataFirst = $items['inicio'];
		    					}
		    					$sCoordenadas .= $items['inicio']['LATITUD'].','.$items['inicio']['LONGITUD'];
		    				}							
						}else{
							$sCoordenadas .= $aStopTravels[0]['inicio']['LATITUD'].','.$aStopTravels[0]['inicio']['LONGITUD']."|".$aStopTravels[0]['fin']['LATITUD'].','.$aStopTravels[0]['fin']['LONGITUD'];
							$aDataLast  = $aStopTravels[0]['inicio'];
							$aDataFirst = $aStopTravels[0]['fin'];
						}
						$iTotalStop	= $sTimeRun *60;
						$iTotalRun  = $sTimeStop*60;
						$iGranTotal = $iTotalStop+ $iTotalRun;
						
						$iTotalStop = $cHistorico->formatSeconds($iTotalStop);
						$iTotalRun  = $cHistorico->formatSeconds($iTotalRun);
						$iGranTotal = $cHistorico->formatSeconds($iGranTotal);						
						$sDirefenciaT = $cHistorico->diferenciaTiempo($aDataFirst['FECHA_TELEFONO'],$aDataLast['FECHA_TELEFONO']);

					    require_once($this->_publicPath.'/html_pdf/html2pdf.class.php');
					    
					    ob_start();
					    include($this->_publicPath.'/layouts/reports/header_report.html');
					    $lHeader = ob_get_clean();
					    
					    ob_start();
					    include($this->_publicPath.'/layouts/reports/footer_report.html');
					    $lFooter = ob_get_clean();	
			
						$tittle  = 'HISTORICO DE VIAJE';		    
					    $lHeader = str_ireplace('0titulo0', $tittle, $lHeader);	    				
	    				
	    				
					    $content .= '<page backtop="10mm" backbottom="10mm" backleft="20mm" backright="20mm">
								    '.$lHeader.'
								    '.$lFooter.'					   
								    <br>';
					    
	    				$content .= '<table style="width: 50%;border: solid .5px #F2F2F2; margin-top:20px;" align="center">
							        <tr>
							            <td style="width: 40%; text-align: center;"><img src="http://maps.googleapis.com/maps/api/staticmap?size=400x415&sensor=true_or_false&path=color:0x0000ff|weight:5|'.$sCoordenadas.'" alt="" ><br><i>Recorrido '.$aDataPhone['FECHA_INICIO'].' - '.$aDataPhone['FECHA_FIN'].'</i></td>
							        </tr>
							    </table><br/>';
					    
	    				$content .= '<table style="width: 100%;border: solid .5px #F2F2F2;font-size:10px" align="center">
							        <tr>
							        	<td colspan="4" style="text-align:center;background-color:#F2F2F2;">
							        		<h6>RESUMEN DEL VIAJE</h6>
							        	</td>    							        
							        </tr>
							        <tr><td><b>Origen </b></td>
							        	<td colspan="3" >'.$aDataFirst['FECHA_TELEFONO'].' -'.$aDataFirst['UBICACION'].' </td></tr>
							        <tr><td><b>Destino</b></td>
							        	<td colspan="3" >'.$aDataLast['FECHA_TELEFONO'].' -'.$aDataLast['UBICACION'].' </td></tr>
							        <tr><td><b>Tiempo Total:</b></td>
							        	<td>'.$iGranTotal.' </td>
							        	<td><b>Distancia:</b></td>
							        	<td>'.$cHistorico->iDistancia.' Km. </td></tr>
							        	<tr><td><b>Tiempo de conducci&oacute;n:</b></td>
							        	<td>'.$iTotalRun.' </td>
							        	<td><b>Vel. Media:</b></td>
							        	<td>'.round($cHistorico->velAverage,2).' Km/h </td></tr>
							        	<tr><td><b>Tiempo de paro:</b></td>
							        	<td>'.$iTotalStop.' </td>
							        	<td><b>Paradas:</b></td>
							        	<td>'.$totalParadas.' </td></tr>
							    </table>';
	    								/*
							        	<tr><td><b>Tiempo medio paro:</b></td>
							        	<td>'.$aResumen['tiempoRalenti'].' </td>
							        	<td></td>
							        	<td></td></tr>*/	    		
	    						
						$aVelocidades = (isset($cHistorico->sDrawVels)) ?  $cHistorico->sDrawVels : 0;
	    				$content .= '<table style="width: 50%;border: solid .5px #F2F2F2; margin-top:20px;" align="center">
		    							<tr>
								        	<td colspan="4" style="text-align:center;background-color:#F2F2F2;">
								        		Grafica de Velocidades
								        	</td>    							        
								        </tr>
								        <tr>
								            <td style="width: 40%; text-align: center;"><img src="https://chart.googleapis.com/chart?chxt=x,y&cht=lc&chs=400x225&chco=76A4FB&chd=t:'.$aVelocidades.'" alt="" ><br></td>
								        </tr>
							    </table><br/>';		
	    				$content .= '</page>';
	    				
						$content .= '<page backtop="10mm" backbottom="10mm" backleft="20mm" backright="20mm">
							    '.$lHeader.'
							    '.$lFooter.'					   
							    <br>';

						$content .='<table style="width: 100%;border: solid .5px #F2F2F2;font-size:10px;margin-top:30px;">									
							        <tr>
							        	<td colspan="5" style="width:100%;text-align:center;background-color:#F2F2F2;">
							        		<h6>VIAJES</h6>
							        	</td>    							        
							        </tr>';	   

						$content .='<tr>
							        	<td style="text-align:center;background-color:#F2F2F2;">
							        		INICIO
							        	</td>    
							        	<td style="text-align:center;background-color:#F2F2F2;">
							        		FIN
							        	</td>	
							        	<td style="text-align:center;background-color:#F2F2F2;">
							        		DURACION
							        	</td>
							        	<td style="text-align:center;background-color:#F2F2F2;">
							        		DISTANCIA
							        	</td>
							        	<td style="width:20%;text-align:center;background-color:#F2F2F2;">							        	
							        		VELOCIDAD
							        	</td>		
							        </tr>';	  		
						
						foreach($aStopTravels as $key => $items){
							if($items['tipo']==1){
								$sUbicacion = explode(',',$items['fin']['UBICACION']);
								$sDireccion = ($items['fin']['UBICACION']!="Sin Ubicacion") ? @$sUbicacion[0].','.@$sUbicacion[1].'<br/>'.@$sUbicacion[2].','.@$sUbicacion[3]: 'Sin direccion';
								$sPromVel   = ($items['total']>0) ? ($items['velocidad']/$items['total']) : $items['velocidad']; 
								$content .='<tr>
							        	<td style="text-align:center;">
							        		'.$items['inicio']['FECHA_TELEFONO'].'
							        	</td>    
							        	<td style="text-align:center;">
							        		'.$items['fin']['FECHA_TELEFONO'].'
							        	</td>	
							        	<td style="text-align:center;">
							        		'.$cHistorico->formatSeconds($items['tiempo']*60).'
							        	</td>	
							        	<td style="text-align:center;">
							        		'.$items['distancia'].' kms.
							        	</td>	
							        	<td style="text-align:center;">
							        		'.round($sPromVel,2).' Km/h
							        	</td>		
							        </tr>';								
							}
						}
						$content .='</table>';
						$content .= '</page>';
						$content .= '<page backtop="10mm" backbottom="10mm" backleft="20mm" backright="20mm">
							    '.$lHeader.'
							    '.$lFooter.'					   
							    <br>';
						
						$content .='<table style="width: 100%;border: solid .5px #F2F2F2;font-size:10px;margin-top:30px;">									
							        <tr>
							        	<td colspan="4" style="width:100%;text-align:center;background-color:#F2F2F2;">
							        		<h6>PARADAS</h6>
							        	</td>    							        
							        </tr>';	   

						$content .='<tr>
							        	<td style="text-align:center;background-color:#F2F2F2;">
							        		INICIO
							        	</td>    
							        	<td style="text-align:center;background-color:#F2F2F2;">
							        		FIN
							        	</td>	
							        	<td style="text-align:center;background-color:#F2F2F2;">
							        		DURACION
							        	</td>
							        	<td style="width:50%;text-align:center;background-color:#F2F2F2;">							        	
							        		UBICACION
							        	</td>		
							        </tr>';	  		
						
						foreach($aStopTravels as $key => $items){
							if($items['tipo']==0){
								$sUbicacion = explode(',',$items['fin']['UBICACION']);
								$sDireccion = ($items['fin']['UBICACION']!="Sin Ubicacion") ? @$sUbicacion[0].','.@$sUbicacion[1].'<br/>'.@$sUbicacion[2].','.@$sUbicacion[3]: 'Sin direccion';
					
								$content .='<tr>
							        	<td>
							        		'.$items['inicio']['FECHA_TELEFONO'].'
							        	</td>    
							        	<td>
							        		'.$items['fin']['FECHA_TELEFONO'].'
							        	</td>	
							        	<td>
							        		'.$cHistorico->formatSeconds($items['tiempo']*60).'
							        	</td>	
							        	<td>
							        		'.$sDireccion .'
							        	</td>		
							        </tr>';								
							}
						}
						$content .='</table></page>';
						
					    try
					    {
					    	$sNameFile = 'Historico_Viaje.pdf';
					    	//header("Content-Disposition: attachment; $sNameFile");
					    	
					        $html2pdf = new HTML2PDF('P', 'A4', 'es', true, 'UTF-8', 3);
					        $html2pdf->pdf->SetDisplayMode('fullpage');
					        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
					        $html2pdf->Output($sNameFile);
					    }
					    catch(HTML2PDF_exception $e) {
					        echo $e;
					        exit;
					    }  	    				
	    			}else{
	    				$this->_redirect("/reports/logbook/index");
	    			}
    			}else{
    				$this->_redirect("/reports/logbook/index");
    			}    			    		
		    }
    	}catch(Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
		}
    } 
    
	public function exportkmlAction(){
		try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();			
			$cServicios		 = new My_Model_Servicios();
			$cTurnos		 = new My_Model_Turnos();
			$cHistorico 	 = new My_Model_Historico();
			
		    if(isset($this->_dataIn['strInput'])){
		    	$idTurno  = $this->_dataIn['strInput'];
		    	$aDataTurno = $cTurnos->getData($idTurno);
		    	
		    	$cHistorico = new My_Model_Historico();
		    	
    			$dataRecorrido		= $cHistorico->getHistoricoByUser($aDataTurno['ID_USUARIO'],$aDataTurno['FECHA_INICIO'],$aDataTurno['FECHA_FIN']); 			
    			if(count($dataRecorrido)>0){			
    				
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
										
					$boatListFolder = new KMLFolder('', 'Recorrido');
					$iControl = 0;
					$dateIn   = '';
					$dateFin  = '';
					$apolyLine = Array();
					
    				foreach($dataRecorrido as $items){
						if($iControl==0){
							$dateIn = $items['FECHA_TELEFONO'];
							$iControl=0;
						}
						$boatFollow = new KMLPlaceMark('',$items['FECHA_TELEFONO'], '', true);
						$boatFollow->setGeometry(new KMLPoint($items['LONGITUD'], $items['LATITUD'], 0));
						//$boatFollow->setStyleUrl('#plotStyle');
						$boatFollow->setTimePrimitive(new KMLTimeStamp('',$items['FECHA_TELEFONO']));
						$boatListFolder->addFeature($boatFollow);
						$dateFin  = $items['FECHA_TELEFONO'];
						
						$aPosition = Array($items['LONGITUD'], $items['LATITUD'],0);
						$apolyLine[] = $aPosition;
					}	
									
					$document->addFeature($boatListFolder);			
					$kml->setFeature($document);
					
					$nameFile  = "Historico_turno_".date("YmdHi").".kml";
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