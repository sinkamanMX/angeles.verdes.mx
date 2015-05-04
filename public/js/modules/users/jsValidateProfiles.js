$().ready(function(){
    var pageHeigth = (($(".page-content-inner").height())/2)-40;
    var oTable = $('.table').dataTable({
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

function selectOption(inputCheck,nameInput){
    if(inputCheck){
        $("#divCheckini"+nameInput).show('slow');
    }else{
        $("#divCheckini"+nameInput).hide('slow');
    }
}
/*
function unselectAll(){

    if(inputCheck){
        $('.chkOn').prop('checked', true);         
    }else{
        $('.chkOn').prop('checked', false);
    }    
}
*/
function validateListCheksCustom(sNameForm){
    var selected = '';    
    $('#'+sNameForm+' input[type=checkbox]').each(function(){
        if (this.checked) {
            selected += $(this).val()+', ';
        }
    }); 

    if (selected != ''){
        validateList(sNameForm);
    }else{
        $.jGrowl('Debe de seleccionar al menos un modulo', { sticky: false, theme: 'growl-error', header: '¡Atención!' ,life: 3000 });        
        //Notify('Debe de seleccionar al menos un modulo', 'top-right', '5000', 'danger', 'fa-exclamation-circle', true); 
    }       

    return false;    
}

function validateList(sNameForm){
    var selected = '';    
    $('#'+sNameForm+' input[type=radio]').each(function(){
        if (this.checked) {
            selected += $(this).val()+', ';
        }
    }); 

    if (selected != ''){        
        $("#"+sNameForm).submit();
    }else{
        $.jGrowl('Debe de seleccionar el modulo de inicio', { sticky: false, theme: 'growl-error', header: '¡Atención!' ,life: 3000  });                
        //Notify('Debe de seleccionar el modulo de inicio', 'top-right', '5000', 'danger', 'fa-exclamation-circle', true); 
    }

    return false;    
}