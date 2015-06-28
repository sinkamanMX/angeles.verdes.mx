var map = null;
var geocoder;
var infoWindow;
var infoLocation;
var markers = [];
var markersGeo = [];
var bounds;
var arrayTravels="";
var mon_timer=60;
var startingOp=false;
var aSelected=Array();
var sSucursal=-1;

$( document ).ready(function() {	
	$('.graphCircle').circliful();
    $('#iFrameModalMapa').on('load', function () {        
      $('#loader').hide();
      $('#iFrameModalMapa').show();
    });       
	drawTable();	 	
	$('[data-toggle="tooltip"]').tooltip(); 

	//setTimeout('submitForm()', 180000);
});

function drawTable(){	
   $('.datatable-tools table').dataTable({
      dom: '<"datatable-header"Tfl>t<"datatable-footer"ip>',
      "bDestroy": true,
      "bLengthChange": false,  
      tableTools: {
        sRowSelect: "single",
        sSwfPath: "/libs/media/swf/copy_csv_xls_pdf.swf",
        aButtons: [
          {
            sExtends:    'print',
            sButtonText: '<i class="icon-print2"></i> Imprimir',
            sButtonClass: 'btn btn-default',
            sInfo: "<h6>Impresión</h6><p><button class='btn btn-primary' onClick='printPage()'>Da click Aqui Para Imprimir</button><br/><br/>Para salir oprime la tecla Esc.</p> "
          }          
        ]
      }
      });  
}

function reDrawMap(){
	if(map ==null){
		initMapToDraw();
    $('#dataTable2').dataTable({
        //"sDom": "Tflt<'row DTTTFooter'<'col-sm-10'i><'col-sm-2'p>>",
        pagingType: 'simple',
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', 
        "bDestroy": true,
        "bLengthChange": false,  
        "bFilter": true,
        "bSort": true,
        "iDisplayLength": 5,
        "bProcessing": true,
        "bAutoWidth": false,
        "bSortClasses": false,
            "oLanguage": {
                "sInfo": "Mostrando _TOTAL_ registros (_START_ a _END_)",
                "sEmptyTable": "Sin registros.",
                "sInfoEmpty" : "Sin registros.",
                "sInfoFiltered": " - Filtrado de un total de  _MAX_ registros",
                "sLoadingRecords": "Leyendo información",
                "sProcessing": "Procesando",
                "sSearch": "Buscar:",
                "sZeroRecords": "Sin registros",
                "oPaginate": {
                  "sFirst"   : "Inicio",
                  "sPrevious": "Anterior",
                  "sNext": "Siguiente",
                  "sLast" : "Fin"
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
              icon: 	'/assets/images/'+sIcono+'.png'
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

var aTypeSelected = Array();

function validateCheck(){
    aTypeSelected = [];
    var selected = '';    
    $('#divMyOptions input[type=checkbox]').each(function(){
        if (this.checked) {
            aTypeSelected.push($(this).val());
        }
    });       
    showTypeObject()
}

function showTypeObject(){
    clearMarkers();
  var strData = $("#divOtrosGeo").html();
      strData = $.trim(strData);


  var aDataExplode = strData.split("!");
  for(var i=0;i<aDataExplode.length;i++){
      var infoElement = aDataExplode[i].split("|");
      if(jQuery.inArray($.trim(infoElement[0]), aTypeSelected)>-1){
        if(infoElement[1]=='G'){
          drawPoint(infoElement);
        }else if(infoElement[1]=='C'){
          drawGeo(infoElement);
        }else if(infoElement[1]=='R'){          
          drawRoute(infoElement);
        }
      }
  }
}

function drawPoint(aElement){
    var markerTable = null;
    if(aElement[6]!="null" && aElement[7]!="null" ){
        var content = aElement[2];

        var Latitud  = parseFloat(aElement[6])
        var Longitud = parseFloat(aElement[7])
        var sIcono   = aElement[3];

        markerTable = new google.maps.Marker({
          map: map,
          position: new google.maps.LatLng(Latitud,Longitud),
          title:  content,
          icon:   '/assets/images/icons/'+sIcono
        });
        markersGeo.push(markerTable);
        infoMarkerTable(markerTable,content);   
        bounds.extend( markerTable.getPosition() );
    }
}

function clearMarkers(){
  if(markersGeo || markersGeo.length>-1){
    for (var i = 0; i < markersGeo.length; i++) {
            markersGeo[i].setMap(null);
    } 
    markersGeo = [];
  }
}

function drawGeo(aElement){
      var arrayGeoInfoLats = null;
          arrayGeoInfoLats = aElement[8].split('¬');
      var geos_points_polygon = [];

      for(j=0;j<arrayGeoInfoLats.length;j++){
            var latlon = arrayGeoInfoLats[j].split('*');

            var Latit =  parseFloat(latlon[0]);
            var Longit = parseFloat(latlon[1]);
            var pointmarker = new google.maps.LatLng(Latit,Longit);
            geos_points_polygon.push(pointmarker);                        
        }

      var geos_options = {
            paths: geos_points_polygon,
            strokeColor: aElement[5],
            strokeOpacity: 0.8,
            strokeWeight: 3,
            title:  aElement[2],
            fillColor: aElement[5],
            fillOpacity: 0.35
      }         
      
      var geos_polygon = new google.maps.Polygon(geos_options);
      geos_polygon.setMap(map);
      markersGeo.push(geos_polygon);
}

function drawRoute(aElement){
      var arrayGeoInfoLats = null;
          arrayGeoInfoLats = aElement[8].split('¬');
      var geos_points_polyline = [];

      for(j=0;j<arrayGeoInfoLats.length;j++){
            var latlon = arrayGeoInfoLats[j].split('*');

            var Latit =  parseFloat(latlon[0]);
            var Longit = parseFloat(latlon[1]);
            var pointmarker = new google.maps.LatLng(Latit,Longit);
            geos_points_polyline.push(pointmarker);                        
        }

        var geos_polygon = new google.maps.Polyline({
          path: geos_points_polyline,
          strokeColor: aElement[5],
          strokeOpacity: 1.0,
          strokeWeight: 3
        });                
      geos_polygon.setMap(map);
      markersGeo.push(geos_polygon);
}