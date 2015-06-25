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
     initMapToDraw()
});

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
	var pointsInfo = $("#positions").html().split("|");
		content    =  pointsInfo[2];

        var Latitud  = parseFloat(pointsInfo[0])
        var Longitud = parseFloat(pointsInfo[1])

        markerTable = new google.maps.Marker({
          map: map,
          position: new google.maps.LatLng(Latitud,Longitud),
          title:    pointsInfo[2],
          icon: 	'/assets/images/carMarker.png'
        });

        infoMarkerTable(markerTable,content);
        map.setZoom(13);
        map.panTo(markerTable.getPosition());  
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