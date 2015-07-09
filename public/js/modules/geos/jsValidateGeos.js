var map  = null;
var infoWindow;
var geocoder;
var infoLocation;
var markerOrigen,markerDestino;
var markers = [];
var bounds;
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var geos_points = [];
var geos_poins_polygon  = [];
var geos_options =  "";

var drawingManager=null;
var geos_polygon = null;

$().ready(function() {
    $('.noEnterSubmit').keypress(function(e){
        if ( e.which == 13 ) return false;        
        if ( e.which == 13 ) e.preventDefault();
    });    
    
    $("#formDbman").validate({
        rules: {
            inputSucursal       : "required",
            inputTipo           : "required",
            inputColor          : "required",
            inputDescripcion    : "required", 
            inputEstatus        : "required",
            inputClave          : "required"
        },
        // Se especifica el texto del mensaje a mostrar
        messages: {
            inputSucursal       : "Campo Requerido",
            inputTipo           : "Campo Requerido",
            inputColor          : "Campo Requerido",
            inputDescripcion    : "Campo Requerido",
            inputEstatus        : "Campo Requerido",
            inputClave          : "Campo Requerido"
        },
        
        submitHandler: function(form) {
            if($("#inputStatusDraw").val()=="nok"){
              alert("Favor de dibujar la georeferencia.");
              return false;
            }else{
              form.submit();      
            }
        }
    }); 

    $('.upperClass').keyup(function()
    {
        $(this).val($(this).val().toUpperCase());
    }); 

    initMapToDraw();
});

function initMapToDraw(){
    infoWindow = new google.maps.InfoWindow;
    directionsDisplay = new google.maps.DirectionsRenderer();
    geocoder = new google.maps.Geocoder();

    var mapOptions = {
        zoom: 5,
        center: new google.maps.LatLng(19.435113686545755,-99.13316173010253)
    };

    map = new google.maps.Map(document.getElementById('map'),mapOptions);    

    var valInputOrigen      = (document.getElementById('inputSearch'));
    var autCompleteOrigen    = new google.maps.places.Autocomplete(valInputOrigen);

    google.maps.event.addListener(autCompleteOrigen, 'place_changed', function() {      
        var place = autCompleteOrigen.getPlace();
        if (!place.geometry) {
          return;
        }

        map.setZoom(15);
        map.setCenter(place.geometry.location); 
        map.panTo(place.geometry.location); 
    });  
  
    bounds = new google.maps.LatLngBounds();    


    if($("#catId").val()>0){
      var latlngbounds = new google.maps.LatLngBounds( );

      $("#btnDraw").hide('slow');
      $("#btnClear").show('slow');  

      geos_polygon = new google.maps.Polygon(geos_options);
      geos_polygon.setMap(map); 

      for (i = 0; i < geos_poins_polygon.length; i++) {
        latlngbounds.extend( geos_poins_polygon[i] );
      }
      map.fitBounds( latlngbounds );
    }


    drawingManager = new google.maps.drawing.DrawingManager({
      drawingMode : null,
      drawingControl : false,
      drawingControlOptions : {
        position : null,
        drawingModes : [google.maps.drawing.OverlayType.POLYGON]
      },
          polylineOptions: {
            editable: true
          },
      polygonOptions : {
        strokeColor : "#FF0000",
        strokeOpacity : 0.8,
        strokeWeight : 2,
        fillColor : "#FF0000",
        fillOpacity : 0.35
      }
    });

    drawingManager.setMap(map);

  google.maps.event.addListener(geos_polygon.getPath(), 'set_at', function() {
      $('#inputPoints').html("");

      var contentString='';
      var polygonBounds = geos_polygon.getPath();
      var xy;
      var firstposition = '';
      for (var i = 0; i < polygonBounds.length; i++) {
          xy = polygonBounds.getAt(i);

          if(contentString==""){
              firstposition  = (','+xy.lat().toFixed(6)+" "+xy.lng().toFixed(6));
          }
          contentString += (contentString!="") ? ',':'';
          contentString += xy.lat().toFixed(6)+" "+xy.lng().toFixed(6);        
      }

      contentString += firstposition;

      $('#inputPoints').html(contentString);
    });    
}

function removeMap(optionMarker){
    if(optionMarker==0 && markerOrigen!=null){      
        markerOrigen.setMap(null);
    }else if(optionMarker==1 && markerDestino!=null){       
        markerDestino.setMap(null);
    }
}

function toggleBounce() {
  if (marker.getAnimation() != null) {
    marker.setAnimation(null);
  } else {
    marker.setAnimation(google.maps.Animation.BOUNCE);
  }
}

function geos_start_draw(){   
  geos_clear_geoc();
  $("#btnDraw").hide('slow');
  $("#btnClear").show('slow');  
  
  drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);

  google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
    drawingManager.setDrawingMode(null);
    polygon.setEditable(true);
    geos_polygon = polygon;

    $("#btnDraw").hide('slow');
    $("#btnClear").show('slow');

    $("#inputStatusDraw").val("ok");

    var contentString='';
    var polygonBounds = polygon.getPath();
    var xy;
    var firstposition = '';
    for (var i = 0; i < polygonBounds.length; i++) {
        xy = polygonBounds.getAt(i);

        if(contentString==""){
            firstposition  = (','+xy.lat().toFixed(6)+" "+xy.lng().toFixed(6));
        }
        contentString += (contentString!="") ? ',':'';
        contentString += xy.lat().toFixed(6)+" "+xy.lng().toFixed(6);        
    }

    contentString += firstposition;

    $('#inputPoints').html(contentString);
  });    
}

function geos_clear_geoc() {
  $("#inputStatusDraw").val("nok");
  if (geos_polygon) {
    geos_polygon.setMap(null);
    geos_polygon.setEditable(false);
    geos_polygon = null;
    $("#btnDraw").show('slow');
    $("#btnClear").hide('slow');
  }
}

function drawinit(){
    geos_polygon = new google.maps.Polygon(geos_options);
    geos_polygon.setMap(map); 

    for (i = 0; i < geos_poins_polygon.length; i++) {
      latlngbounds.extend( geos_poins_polygon[i] );
    }
    map.fitBounds( latlngbounds );  
}