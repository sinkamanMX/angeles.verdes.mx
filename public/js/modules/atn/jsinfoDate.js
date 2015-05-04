var map = null;
var geocoder;
var infoWindow;
var infoLocation;
var markers = [];

$( document ).ready(function() {
	initMapToDraw();
});

function initMapToDraw(){
    var mapOptions = {
      zoom: 5,
      center: new google.maps.LatLng(24.52713, -104.41406),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
	map 	 = new google.maps.Map(document.getElementById('Map'),mapOptions);
	geocoder = new google.maps.Geocoder();
	bounds 	 = new google.maps.LatLngBounds();
	infoWindow = new google.maps.InfoWindow;
	printPoints();
}

function printPoints(){	
	var iLongDate		= $("#inputLonDate").val();
	var iLatDate		= $("#inputLatDate").val();
	var sDirDate		= $("#inputDireccion").val();

	if(iLongDate!="" && iLongDate!="0.000000" && iLatDate!="" && iLatDate!="0.000000"){
    	var latitude  = iLatDate; 
    	var longitude = iLongDate; 

    	content='<table width="350" class="table-striped" >'+    			
    			'<tr><td style="text-align:center;"><b>Dirección</b></td></tr><tr><td align="left">'+sDirDate+'</td><tr>'+
    			'</table>';	    	
		var markerTable = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(latitude,longitude),
			title: 	'Dirección de la Cita',
			icon: 	'/images/repair.png'
		});
		markers.push(markerTable);
		infoMarkerTable(markerTable,content);	
		setTecnico()
	}else if(sDirDate!=""){	
		var address = 'MX,'+sDirDate;
	 	geocoder.geocode( { 'address': address}, function(results, status) {
	      if(status=='OK'){
	      		var listLatLon = results[0].geometry.location;

				var markerTable = new google.maps.Marker({
				    map: map,
				    position: listLatLon,
				    title: 	'Posición aproximada',
					icon: 	'/images/repair.png'
			    });

    			content='<table width="350" class="table-striped" >'+    			
    				'<tr><td style="text-align:center;"><b>Dirección</b></td></tr>'+
    				'<tr><td style="text-align:center;"><b>Posición aproximada</b></td></tr>'+'<tr><td align="left">'+sDirDate+'</td><tr>'+
    				'</table>';	 
    			markers.push(markerTable);   
			    infoMarkerTable(markerTable,content);
			    setTecnico()			      				
	    	}
		});
	}
	
}

function setTecnico(){
	var iLongTecnico	= $("#inputLongitudTecnico").val();
	var iLatTecnico		= $("#inputLatitudTecnico").val();
	var sDataTecnico	= $("#inputData").val();

	if(sDataTecnico!=""){
		var travelInfo  = sDataTecnico.split('|');

		if(travelInfo[3]!="null" && travelInfo[4]!="null"){
	    	var latitude  = travelInfo[3]; 
	    	var longitude = travelInfo[4]; 

			var content='<table width="350" class="table-striped" ><tr><td align="right"><b>Evento</b></td><td width="200" align="left">'+travelInfo[2]+'</td><tr>'+
	    			'<tr><td align="right"><b>Fecha</b></td><td align="left">'+travelInfo[1]+' </td><tr>'+	    			
	    			'<tr><td align="right"><b>Velocidad</b></td><td align="left">'+travelInfo[5]+' kms/h.</td><tr>'+
	    			'<tr><td align="right"><b>Bateria</b></td><td align="left">'+travelInfo[6]+' %</td><tr>'+
	    			'<tr><td align="right"><b>Tipo GPS</b></td><td align="left">'+travelInfo[7]+' </td><tr>'+
	    			'<tr><td align="right"><b>Ubicación</b></td><td align="left">'+travelInfo[9]+'</td><tr>'+
	    			'</table>';	

	    	var infocontent='<tr><td>Fecha</td><td align="left"><b>'+travelInfo[1]+'</b></td><tr>'+	    			
	    					'<tr><td>Velocidad</b></td><td align="left"><b>'+travelInfo[5]+' kms/h.</b></td><tr>'+
	    					'<tr><td>Bateria</b></td><td align="left"><b>'+travelInfo[6]+' %</b></td><tr>'+
	    					'<tr><td>Tipo GPS</b></td><td align="left"><b>'+travelInfo[7]+' </b></td><tr>'+
	    					'<tr><td>Ubicación</b></td><td align="left"><b>'+travelInfo[9]+'</b></td><tr>';	    			

			$("#divDataPosition").append(infocontent);    			
			var markerTecnico = new google.maps.Marker({
				map: map,
				position: new google.maps.LatLng(latitude,longitude),
				title: 	travelInfo[1],
				icon: 	'/assets/images/carMarker.png'
			});
			markers.push(markerTecnico);
			infoMarkerTable(markerTecnico,content);
			fitBoundsToVisibleMarkers()
		}else{
	    	var infocontent= '<tr><td colspan="2" style="text-align:center"><b>El técnico no cuenta con información</b></td></tr>';
			$("#divDataPosition").append(infocontent);  
	    }
	}else{
    	var infocontent= '<tr><td colspan="2" style="text-align:center"><b>El técnico no cuenta con información</b></td></tr>';
		$("#divDataPosition").append(infocontent);  
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