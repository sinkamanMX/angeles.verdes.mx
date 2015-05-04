$().ready(function() {
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var dateInter  = parseInt(nowTemp.getMonth())+1;  
    var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
    var todayDay   = (nowTemp.getDate()<10) ? "0"+nowTemp.getDate(): nowTemp.getDate();        

    if($("#inputFechaIn").val()==""){
      $("#inputFechaIn").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay);      
    }

    if($("#inputTimeBegin").val()==""){
      $("#inputTimeBegin").val( "00:00");      
    }

    if($("#inputTimeEnd").val()==""){
      $("#inputTimeEnd").val("23:59");
    }    

    var checkin = $('#inputFechaIn').datetimepicker({
        format: "yyyy-mm-dd",
        showMeridian: false,
        autoclose: true,
        todayBtn: true,
        minView : 2,
        maxView : 2,
        startView: 2,
        startDate: $("#inputFechaIn").val(),
    });

    var hourIn = $('#inputTimeBegin').datetimepicker({
        format: "hh:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: false,
        minView : 0,
        maxView : 1,
        startView: 1,
    }).on('changeDate', function(ev) {
        if($('#inputTimeBegin').val() > $('#inputTimeEnd').val()){
            $('#inputTimeEnd').val($('#inputTimeBegin').val());
        }
    });

    var hourEnd = $('#inputTimeEnd').datetimepicker({
        format: "hh:ii",
        showMeridian: false,
        autoclose: true,
        todayBtn: false,
        minView : 0,
        maxView : 1,
        startView: 1,
    }).on('changeDate', function(ev) {
        if($('#inputTimeEnd').val() < $('#inputTimeBegin').val()){
            $('#inputTimeBegin').val($('#inputTimeEnd').val());
        }        
    });

    $("#FormData").validate({
        rules: {
            inputFechaIn    :      "required",    
            inputTipo       :      "required",     
            inputComment    :      "required",
            inputHorario    :      "required",
            infoUnit        :      "required",
            inputRevision   :      "required"
        },
        messages: {                          
            inputFechaIn    :      "Campo Requerido",        
            inputTipo       :      "Campo Requerido",            
            inputComment    :      "Campo Requerido",
            inputHorario    :      "Campo Requerido",
            infoUnit        :      "Selecciona una unidad",
            inputRevision   :      "Campo Requerido"
        },
        submitHandler: function(form) {
            form.submit();
        }
    }); 

    $('.dataTable').dataTable( {
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
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
              "sInfo": "Mostrando _TOTAL_ registros (_START_ a _END_)",
              "sEmptyTable": "Sin registros.",
              "sInfoEmpty" : "Sin registros.",
              "sInfoFiltered": " - Filtrado de un total de  _MAX_ registros",
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
});

function backToMain(){
  var mainPage = $("#hRefLinkMain").val();
  location.href= mainPage;
}

function modifyFields(){
    $("#btnSaveOk").hide('slow');
    $("#btnModify").hide('slow');
    $("#btnSave").show('slow');
    $("#btnSaveCancel").show('slow');
    $("#inputFechaIn").prop( "disabled", false );
    $("#inputHorario").prop( "disabled", false );
    $("#inputTipo").prop( "disabled", false );
    $("#inputHorario2").prop( "disabled", false );
    $("#bOperation").val('modify');
    $("#inputRevision").prop( "disabled", false );
    $("#inputRevision").html("");
}

function cancelModify(){
    location.reload();   
}
