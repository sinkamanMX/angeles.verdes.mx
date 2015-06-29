var map  = null;
var infoWindow;
var geocoder;
var infoLocation;
var markerOrigen,markerDestino;
var markers = [];
var bounds;
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();

$().ready(function() {
    $('.noEnterSubmit').keypress(function(e){
        if ( e.which == 13 ) return false;
        //or...
        if ( e.which == 13 ) e.preventDefault();
    });    
    
    $("#formDbman").validate({
        rules: {
            inputSucursal       : "required",
            inputTipo           : "required",
            inputDescripcion    : "required",   
            inputLatOrigen      : {
                required: true,
                number: true
            },
            inputLonOrigen      : {
                required: true,
                number: true
            },
            inputRadio          : {
                required: true,
                number: true
            },
            inputEstatus        : "required"
        },
        // Se especifica el texto del mensaje a mostrar
        messages: {
            inputSucursal       : "Campo Requerido",
            inputTipo           : "Campo Requerido",
            inputDescripcion    : "Campo Requerido",
            inputLatOrigen      : {
                required : "Campo Requerido",
                number: "Este campo acepta solo números"
            },
            inputLonOrigen      : {
                required : "Campo Requerido",
                number: "Este campo acepta solo números"
            },
            inputRadio          : {
                required : "Campo Requerido",
                number: "Este campo acepta solo números"
            },
            inputEstatus        : "Campo Requerido",
        },
        
        submitHandler: function(form) {
            form.submit();    
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

        $("#inputLatOrigen").val(place.geometry.location.lat());
        $("#inputLonOrigen").val(place.geometry.location.lng());
        if( $("#inputLatOrigen").val()!="" && $("#inputLatOrigen").val()!="0" &&
            $("#inputLonOrigen").val()!="" && $("#inputLonOrigen").val()!="0"
            ){
            setMarker(0);   
        }
    });  
  
    bounds = new google.maps.LatLngBounds();        
    if($("#optReg").val()=='update'){
        setMarker(0);
    }
}

function setMarker(optionMarker){
    var latMarker = 0;
    var lonMarker = 0;
    var position  = null;
    removeMap(optionMarker);

    if(optionMarker==0){            
        latMarker   = $("#inputLatOrigen").val();
        lonMarker   = $("#inputLonOrigen").val();
        position    = new google.maps.LatLng(latMarker, lonMarker);

        markerOrigen = new google.maps.Marker({
            map: map,
            position: position,
            draggable:true,
            animation: google.maps.Animation.DROP,
            title:  "Origen"
        }); 

        google.maps.event.addListener(markerOrigen, 'click', toggleBounce); 

        google.maps.event.addListener(markerOrigen, "dragend", function(event) {
            $("#inputLatOrigen").val(event.latLng.lat());
            $("#inputLonOrigen").val(event.latLng.lng());
            if( $("#inputLatOrigen").val()!="" && $("#inputLatDestino").val()!="0" &&
                $("#inputLonOrigen").val()!="" && $("#inputLonOrigen").val()!="0"
                ){
                setMarker(0);                        
            }
        });   
        $("#btnClean").show('slow');
    }

    map.setZoom(18);
    map.panTo(position);     
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