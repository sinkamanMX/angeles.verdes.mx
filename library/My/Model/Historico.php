<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Historico extends My_Db_Table
{
	protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'PROD_HISTORICO_POSICION';
	protected $_primary = 'ID_POSICION';
	
	public $iDistancia = 0;
	public $velAverage = 0;
	public $sDrawVels  = '';
	
	public function getPositions($idPhone,$dDateIn,$dDateEnd){
 		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT IF(H.VELOCIDAD>0 ,1,0) AS TIPO, E.DESCRIPCION_EVENTO, H.ID_POSICION, H.FECHA_TELEFONO, H.TIPO_GPS, H.LATITUD, H.LONGITUD ,H.VELOCIDAD,H.ANGULO, H.UBICACION,H.ID_EVENTO
				FROM PROD_HISTORICO_POSICION H
				INNER JOIN PROD_EVENTOS E ON H.ID_EVENTO = E.ID_EVENTO
				WHERE H.FECHA_TELEFONO BETWEEN '$dDateIn' AND '$dDateEnd'
				 AND H.ID_TELEFONO = $idPhone
				ORDER BY H.FECHA_TELEFONO ASC";
    	
    	$query   = $this->query($sql);   
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;	
	}
	
	public function calculateDistancia($fLatitud,$fLongitud,$fLatitudD, $fLongituD){		
 		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT DISTANCIA($fLatitud,$fLongitud,$fLatitudD, $fLongituD) AS IDISTANCIA;";
    	$query   = $this->query($sql);   
		if(count($query)>0){		  
			$result = $query[0]['IDISTANCIA'];			
		}	
        
		return $result;	
	}	
	
	public function diferenciaTiempo($fechaInicio,$fechaFin){
		$result= Array();		
		$formato = 'Y-m-d H:i:s';
		$fecha = DateTime::createFromFormat($formato, $fechaFin);		
				
		$date = new DateTime($fechaInicio);
		$now  = new DateTime($fechaFin);

		//$result =  $date->diff($now)->format("%H:%I:%S");
		$aDiferencia =  $date->diff($now);
		
		
		$result  =  $aDiferencia->days * 24 * 60;
		$result += $aDiferencia->h * 60;
		$result += $aDiferencia->i;
		//echo $result.' minutes';
		
		return $result;		 
	}
	
	function distancia_entre_puntos($lat_a,$lon_a,$lat_b,$lon_b){
    	$lat_a = $lat_a * pi() / 180;
    	$lat_b = $lat_b * pi() / 180;
    	$lon_a = $lon_a * pi() / 180;
    	$lon_b = $lon_b * pi() / 180;
    	/**/
    	$angle = cos($lat_a) * cos($lat_b);
    	$xx = sin(($lon_a - $lon_b)/2);
    	$xx = $xx*$xx;
    	/**/
    	$angle = $angle * $xx;
    	$aa = sin(($lat_a - $lat_b)/2);
    	$aa = $aa*$aa;
    	$angle = sqrt($angle + $aa);
        //$salida = arco_seno($angle);
        $salida = asin($angle);
        /*gps_earth_radius = 6371*/
    	$salida = $salida * 2;
    	$salida = $salida * 6371;
		
		$salida = round($salida*100)/100;
    	return $salida;
  }	
  
	public function formatSeconds($secs){
		if ($secs<0) return false;
		$m = (int)($secs / 60); $s = $secs % 60;
		$h = (int)($m / 60); $m = $m % 60;
		$h = ($h<10) ? "0".$h : $h;
		$m = ($m<10) ? "0".$m : $m;
		$s = ($s<10) ? "0".$s : $s;
		return $h.":".$m.":".$s;
	}	
	
	public function getStopTravels($aHistorico){
		$aProcessData= Array();		
		$aControl	 = Array();
		$iControl    = 0;
		$velControl    = 0;
		$velTotalRows  = 0;
				
		if(count($aHistorico)>0){
			$controlResumen = Array();
			foreach($aHistorico as $element){
				if($element['VELOCIDAD']>0){
					$velControl += $element['VELOCIDAD'];
					$velTotalRows++;
					$this->sDrawVels .= ($this->sDrawVels!="") ? ',': '';
					$this->sDrawVels .= $element['VELOCIDAD'];
				}
				
				if(count($aProcessData)==0){
					$aProcessData[$iControl]['tipo']  = $element['TIPO'];			
					$aProcessData[$iControl]['inicio']= $element;					
					$aControl	= $element;					
				}else{												
					if($element['TIPO'] == @$aControl['TIPO']){
						$diferenciaTiempo 	= $this->diferenciaTiempo($aControl['FECHA_TELEFONO'],$element['FECHA_TELEFONO']);
						$distancia 			= $this->distancia_entre_puntos($aControl['LATITUD'],$aControl['LONGITUD'],
																   $element['LATITUD'],$element['LONGITUD']);
																   
						$aProcessData[$iControl]['distancia']	= $distancia;
						$aProcessData[$iControl]['tiempo']  	= $diferenciaTiempo;
						$aProcessData[$iControl]['fin']  		= $element;
						@$aProcessData[$iControl]['total']    	+= 1;
						@$aProcessData[$iControl]['velocidad']	+= $element['VELOCIDAD'];
						$this->iDistancia += $distancia;
						@$aProcessData[$iControl]['total']    	+= 1;
						@$aProcessData[$iControl]['velocidad']	+= $element['VELOCIDAD'];
					}else{
						if(!isset($aProcessData[$iControl]['fin'])){
							$aProcessData[$iControl]['distancia'] = 0;
							$aProcessData[$iControl]['tiempo']    = 0;							
							$aProcessData[$iControl]['fin']  	  = $aProcessData[$iControl]['inicio']; 
							@$aProcessData[$iControl]['total']    = 0;
							@$aProcessData[$iControl]['velocidad']= 0;
						}
						$iControl++;
						$aProcessData[$iControl]['tipo']  = $element['TIPO'];		
						$aProcessData[$iControl]['inicio']= $element;
						$aControl	= $element;			
					}
				}
			}
			
			$this->velAverage = ($velTotalRows>0) ? ($velControl/$velTotalRows) : 0 ;
		}
		return $aProcessData;
	}
}