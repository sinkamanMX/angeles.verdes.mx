$( document ).ready(function() {   

  $('#dataTable').dataTable( {
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

function beforeDelete(idtableRow){
  $("#inputDelete").val(idtableRow);  
    bootbox.confirm("¿Realmente desea eliminar este registro?", function (result) {
    if (result) {
      deleteRow();
    }
  });
}         

function deleteRow(){ 
  $('body').append('<div class="overlay"><div class="opacity"></div><i class="icon-spinner2 spin"></i></div>');

	 var idItem 	  = $("#inputDelete").val();
	 var moduleUrl = $("#inputModule").val();   
   $( "#spinner-default" ).spinner();
   $('.loading-container').removeClass('loading-inactive');
    $.ajax({
        url 	  : "/dbman/json/operations",
        type    : "GET",
        dataType: 'json',
        data    : { catId : idItem,
            	      optReg: 'delete',
                    ssIdource: moduleUrl},
        success: function(data) {
            $('.overlay').fadeIn(150);
            var result = data.answer;
            $('.loading-container').addClass('loading-inactive');            

            if(result == 'deleted'){
              $.jGrowl('El registro fue eliminado correctamente.', { sticky: false, theme: 'growl-success', header: '¡Atención!' ,life: 3000 });        
                /*Notify('El registro fue eliminado correctamente.', 'top-right', '5000', 'success', 'fa-check', true); */
                location.reload();                
            }else{
              $.jGrowl('Ocurrio un error al eliminar el registro.', { sticky: false, theme: 'growl-error', header: '¡Atención!' ,life: 3000 });        
                //Notify('Ocurrio un error al eliminar el registro.', 'top-right', '5000', 'warning', 'fa-warning', true);
            }            
        }
    });
}