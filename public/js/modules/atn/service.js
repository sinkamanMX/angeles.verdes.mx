var sSucursal=-1;

$( document ).ready(function() {
	$(".chzn-select").chosen();
	$('.graphCircle').circliful();

    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var dateInter  = parseInt(nowTemp.getMonth())+1;  
    var todayMonth = (dateInter<10) ? "0"+dateInter : dateInter;
    var todayDay   = (nowTemp.getDate()<10) ? "0"+nowTemp.getDate(): nowTemp.getDate();        

    if($("#inputFechaIn").val()==""){
      $("#inputFechaIn").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay);      
    }

    if($("#inputFechaFin").val()==""){
      $("#inputFechaFin").val(nowTemp.getFullYear()+"-"+todayMonth+"-"+todayDay);
    }
    
    var checkin = $('#inputFechaIn').datetimepicker({
        format: "yyyy-mm-dd",
        showMeridian: false,
        autoclose: true,
        todayBtn: true,
        minView: 2,
    }).on('changeDate', function(ev) {
      if(ev.date.valueOf() > $('#inputFechaFin').datetimepicker('getDate').valueOf()){
        $('#inputFechaFin').datetimepicker('setDate', ev.date);   
      }

      $('#inputFechaFin').datetimepicker('setStartDate', ev.date);      
      $('#inputFechaFin').prop('disabled', false);
      $('#inputFechaFin')[0].focus();      
    });

    var checkout = $('#inputFechaFin').datetimepicker({
        format: "yyyy-mm-dd",
        showMeridian: false,
        autoclose: true,
        todayBtn: true,
        minView: 2,
    }).on('changeDate', function(ev) {
      /*if(ev.date.valueOf() < $('#inputFechaIn').datetimepicker('getDate').valueOf()){
        $('#inputFechaIn').datetimepicker('setDate', ev.date);   
      }*/
      $('#inputFechaIn').datetimepicker('setEndDate', ev.date);
    });	

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

    $('[data-toggle="tooltip"]').tooltip(); 

    $('#iFrameDetCita').on('load', function () {        
        $('#loader').hide();
        $('#iFrameDetCita').show();
    });          
});

function getReport(){
	$( "#FormData" ).submit();
}

function setStatus(idStatus){
  $("#inputStatus").val(idStatus);
  getReport();
}

function getReportAll(){
  var inputFecIn  = $("#inputFechaIn").val();
  var inputFecFin = $("#inputFechaFin").val(); 
  var idSucursal  = $("#cboInstalacion").val(); 
  var idTecnico   = $("#inputTecnicos").val();
  var strOpt      = $("#optReg").val();
  var iStatus     = $("#inputStatus").val();
  var bType       = $("#cboTypeSearch").val();

  var url = "/atn/services/exportall?optReg="+strOpt+"&cboInstalacion="+idSucursal+"&inputTecnicos="+idTecnico+"&inputFechaIn="+inputFecIn+"&inputFechaFin="+inputFecFin+"&inputStatus="+iStatus+"&cboTypeSearch="+bType;
  window.open(url, '_blank');
} 

function showPosition(idDate){
    $("#myModalinfoVis").modal("show");        
    $('#iFrameDetCita').attr('src','/atn/services/posistiondate?strInput='+idDate);   
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