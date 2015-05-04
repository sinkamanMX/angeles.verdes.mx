$().ready(function(){
    var pageHeigth = (($(".page-content-inner").height())/2)-20;
    var oTable = $('#tableEvents').dataTable({
        //"sDom": "Tflt<'row DTTTFooter'<'col-sm-10'i><'col-sm-2'p>>",
        "scrollY": pageHeigth+"px",
        "scrollCollapse": true,
        pagingType: 'full_numbers',
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', 
        "bDestroy": true,
        "bLengthChange": false,  
        "bFilter": true,
        "bSort": true,
        "iDisplayLength": 10,      
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
});