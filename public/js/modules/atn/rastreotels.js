var map = null;
var geocoder;
var infoWindow;
var infoLocation;
var markers = [];
var bounds;
var arrayTravels="";
var mon_timer=60;
var startingOp=false;
var aSelected=Array();
var sSucursal=-1;

$( document ).ready(function() {	
	$('.graphCircle').circliful();
	drawTable();	 	
	$('[data-toggle="tooltip"]').tooltip(); 

	setTimeout('submitForm()', 180000);
});

function drawTable(){	
    $('#dataTable').dataTable( {
        /*"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p> >T<'clear'>lfrtip",*/
        "sDom": "<'row'<'span6'l> >T<'clear'>lfrtip",
        "sPaginationType": "bootstrap",
        "bDestroy": true,
        "bLengthChange": false,
        "bPaginate": true,
        "bFilter": true,
        "bSort": true,
        "bJQueryUI": true,
        "iDisplayLength": 10,      
        "bProcessing": true,
        "bAutoWidth": true,
        "bSortClasses": false,
          "oLanguage": {
              "sInfo": "",
              "sEmptyTable": "",
              "sInfoEmpty" : "",
              "sInfoFiltered": "",
              "sLoadingRecords": "Leyendo información",
              "sProcessing": "Procesando",
              "sSearch": "Buscar:",
              "sZeroRecords": "Sin registros",
              "oPaginate": {
                "sPrevious": "Anterior",
                "sNext": "Siguiente"
              }          
          }
          ,
        "tableTools": {
            "aButtons": [
                {
                    "sExtends": "print",
                    "sButtonText": " Imprimir <i class='icon-print'></i>",
                    "sInfo"      : "<h6>Impresión</h6><p><button class='btn btn-primary' onClick='printPage()'>Da click Aqui Para Imprimir</button><br/><br/>Para salir oprime la tecla Esc.</p> "
                }               
            ]
        }
    } ); 	
}

function reDrawMap(){
	if(map ==null){
		initMapToDraw();
    $('#dataTable2').dataTable( {
        "sDom": "<'row'<' 'l><' 'f>r>t<'row'<' 'i><' 'p>>",
        "sPaginationType": "bootstrap",
        "bDestroy": true,
        "bLengthChange": false,
        "bPaginate": true,
        "bFilter": false,
        "bSort": true,
        "bJQueryUI": true,
        "iDisplayLength": 5,      
        "bProcessing": true,
        "bAutoWidth": true,
        "bSortClasses": false,
          "oLanguage": {
              "sInfo": "",
              "sEmptyTable": "",
              "sInfoEmpty" : "",
              "sInfoFiltered": "",
              "sLoadingRecords": "Leyendo información",
              "sProcessing": "Procesando",
              "sSearch": "Buscar:",
              "sZeroRecords": "Sin registros",
              "oPaginate": {
                "sPrevious": "Anterior",
                "sNext": "Siguiente"
              }          
          }
    } );  		
	}else{
		setTimeout('resize()', 500);
	}
}

function resize(){
	google.maps.event.trigger(map,'resize');
	map.setCenter(map.getCenter()); 
	map.setZoom( map.getZoom() );
}

function initMapToDraw(){
	infoWindow = new google.maps.InfoWindow;
    var mapOptions = {
      zoom: 5,
      center: new google.maps.LatLng(24.52713, -104.41406),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
	map = new google.maps.Map(document.getElementById('Map'),mapOptions);
	bounds = new google.maps.LatLngBounds();
	printPoints();
}

function printPoints(){
	var pointsTel = $("#positions").html().split("!");
	for(var i=0;i<pointsTel.length;i++){
      	var travelInfo = pointsTel[i].split('|');
        var markerTable = null;
        if(travelInfo[4]!="null" && travelInfo[5]!="null" ){
            content='<table width="350" class="table-striped" >'+  
                '<tr><td align="right"><b>Hora</b></td><td width="200" align="left">'+travelInfo[1]+'</td><tr>'+
                '<tr><td align="right"><b>Tipo GPS</b></td><td width="200" align="left">'+travelInfo[2]+'</td><tr>'+
                '<tr><td align="right"><b>Evento</b></td><td width="200" align="left">'+travelInfo[3]+'</td><tr>'+
                '<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[6]+' kms/h.</td><tr>'+
                '<tr><td align="right"><b>Bateria</b></td><td align="left">'+travelInfo[7]+' %</td><tr>'+
                '<tr><td align="right"><b>Ubicación</b></td><td align="left">'+travelInfo[8]+'</td><tr>'+
                '</table>';
            var Latitud  = parseFloat(travelInfo[4])
            var Longitud = parseFloat(travelInfo[5])
            var sEstatus = travelInfo[9]; 
            var sIcono   = (sEstatus=='OK') ? 'carMarker': 'carOff';

            markerTable = new google.maps.Marker({
              map: map,
              position: new google.maps.LatLng(Latitud,Longitud),
              title:  travelInfo[1],
              icon: 	'/images/'+sIcono+'.png'
            });
            markers.push(new google.maps.LatLng(Latitud,Longitud));
            infoMarkerTable(markerTable,content);   
            bounds.extend( markerTable.getPosition() );
        }
	}

    if(arrayTravels.length>1){ 
        map.fitBounds(bounds);  
    }else if(arrayTravels.length==1){
        map.setZoom(13);
        map.panTo(markerTable.getPosition());  
    }
}

function fitBoundsToVisibleMarkers() {
	if(markers.length>0){
	    for (var i=0; i<markers.length; i++) {
			bounds.extend( markers[i].getPosition() );
	    }
	    if(markers.length==1){
			map.setZoom(13);
		  	map.panTo(markers[0].getPosition());
	    }else{
			map.fitBounds(bounds);
	    }
	}
}

function infoMarkerTable(marker,content){	
    google.maps.event.addListener(marker, 'click',function() {
      if(infoWindow){infoWindow.close();infoWindow.setMap(null);}
      var marker = this;
      var latLng = marker.getPosition();
      infoWindow.setContent(content);
      infoWindow.open(map, marker);
      map.setZoom(13);
	  map.setCenter(latLng); 
	  map.panTo(latLng);     
	});
}

function centerTel(idValue){
	var dataTel    = $("#divTel"+idValue).html();
	var travelInfo = dataTel.split('|');
	
	var content     = '';
    var markerTable2 = null;
    if(travelInfo[4]!="null" && travelInfo[5]!="null" ){
        content='<table width="350" class="table-striped" >'+  
            '<tr><td align="right"><b>Hora</b></td><td width="200" align="left">'+travelInfo[1]+'</td><tr>'+
            '<tr><td align="right"><b>Tipo GPS</b></td><td width="200" align="left">'+travelInfo[2]+'</td><tr>'+
            '<tr><td align="right"><b>Evento</b></td><td width="200" align="left">'+travelInfo[3]+'</td><tr>'+
            '<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[6]+' kms/h.</td><tr>'+
            '<tr><td align="right"><b>Bateria</b></td><td align="left">'+travelInfo[7]+' %</td><tr>'+
            '<tr><td align="right"><b>Ubicación</b></td><td align="left">'+travelInfo[8]+'</td><tr>'+
            '</table>';
        var latitude  = parseFloat(travelInfo[4])
        var longitude = parseFloat(travelInfo[5])
        var positionLatLon  = new google.maps.LatLng(latitude,longitude);
		HandleInfoWindow(positionLatLon,content);
	}
}

function HandleInfoWindow(latLng, content) {
	if(infoWindow){infoWindow.close();infoWindow.setMap(null);}
    infoWindow.setContent(content);
    infoWindow.setPosition(latLng);
    infoWindow.open(map);
	map.setZoom(13);
	map.setCenter(latLng); 
	map.panTo(latLng);     
}

function submitForm(){
	$( "#FormData" ).submit();
}

function setStatus(idStatus){
  $("#inputStatus").val(idStatus);
  submitForm();
}
function getReport(idValue){
    $('#iFrameModalMapa').attr('src','/atn/rastreo/reporte?strInput='+idValue);
    $("#myModalMapa").modal("show");
}

function printPage() {
    $('#dataTable').print({
        globalStyles: false,
        mediaPrint: true,
        stylesheet: "/css/tablePrint.css",
        iframe: true,
        noPrintSelector: ".avoid-this"
    });
}

function getReportAll(){
  var inputStatus  = $("#inputStatus").val();
  var inputSucursal = $("#inputSucursal").val(); 

  var url = "/atn/rastreo/exportall?optReg=search&inputStatus="+inputStatus+"&inputSucursal="+inputSucursal;
  window.open(url, '_blank');
} 