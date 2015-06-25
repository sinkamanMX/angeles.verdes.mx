<?php
/**
  * Example File
  *
  * @package kmlInstaller
  */

/**
  * KML definition file inclusion
  */
include_once('kml.class.php');
$kml = new KML('Recorrido Historico');

$document = new KMLDocument('Recorrido', 'Historico');

/**
  * Style definitions
  */

$style = new KMLStyle('boatStyle');
$style->setIconStyle('images/fish.png', 'ffffffff', 'normal', 1);
$style->setLineStyle('ffffffff', 'normal', 2);
$document->addStyle($style);

$style = new KMLStyle('navintStyle');
$style->setIconStyle('images/navint.png', 'ffffffff', 'normal', 1);
$style->setLineStyle('ff0000ff', 'normal', 3);
$document->addStyle($style);

$style = new KMLStyle('plotStyle');
$style->setIconStyle('images/small.png', 'ff00ff00', 'normal', 0.2);
$document->addStyle($style);

$style = new KMLStyle('portStyle');
$style->setIconStyle('images/port.png');
$document->addStyle($style);

$style = new KMLStyle('polyStyle');
$style->setPolyStyle('660000ff');
$document->addStyle($style);

/**
  * File adds
  */
/*$kml->addFile('images/navint.png', 'images/navint.png');
$kml->addFile('images/icone.png', 'images/icone.png');
$kml->addFile('images/small.png', 'images/small.png');
$kml->addFile('images/fish.png', 'images/fish.png');
$kml->addFile('images/port.png', 'images/port.png');*/


$boatListFolder = new KMLFolder('', 'Pocisiones');

$boatFollow = new KMLPlaceMark('', '1', '', true);
$boatFollow->setGeometry(new KMLPoint( 4, 3, 0));
$boatFollow->setStyleUrl('#plotStyle');
$boatFollow->setTimePrimitive(new KMLTimeStamp('','2008-05-01'));
$boatListFolder->addFeature($boatFollow);

$boatFollow = new KMLPlaceMark('', '2', '', true);
$boatFollow->setGeometry(new KMLPoint( 2, 4, 0));
$boatFollow->setStyleUrl('#plotStyle');
$boatFollow->setTimePrimitive(new KMLTimeStamp('','2008-05-05'));
$boatListFolder->addFeature($boatFollow);

$boatFollow = new KMLPlaceMark('', '3', '', true);
$boatFollow->setGeometry(new KMLPoint(-1, 3, 0));
$boatFollow->setStyleUrl('#plotStyle');
$boatFollow->setTimePrimitive(new KMLTimeStamp('','2008-05-15'));
$boatListFolder->addFeature($boatFollow);

$boatFollow = new KMLPlaceMark('', '4', '', true);
$boatFollow->setGeometry(new KMLPoint(-1, 2, 0));
$boatFollow->setStyleUrl('#plotStyle');
$boatFollow->setTimePrimitive(new KMLTimeStamp('','2008-05-25'));
$boatListFolder->addFeature($boatFollow);

$boatTrace = new KMLPlaceMark(null, 'Recorrido', '', true);
$boatTrace->setGeometry (new KMLLineString( Array (
                           array ( 4, 3,0),
                           array ( 2, 4,0),
                           array (-1, 3,0),
                           array (-1, 2,0)
                        ), true, '', true)
                     );


$boatTrace->setTimePrimitive(new KMLTimeStamp('','2008-05-01','2008-05-25'));
$boatTrace->setStyleUrl('#boatStyle');
$boatListFolder->addFeature($boatTrace);


$document->addFeature($boatListFolder);

$kml->setFeature($document);




/**
  * Output result
  */

//echo '<pre>';
//echo htmlspecialchars($kml->output('S'));
//echo '</pre>';

//echo $kml->output('S');


$kml->output('F', 'output/recorrido_123_RH1234.kml');
//$kml->output('Z', 'output/test.kmz');

?>
