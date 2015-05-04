var validationControl =  null;

$().ready(function(){
    validateFormA();
/*
    $("#tableElements .deleteLink").on("click",function() {
        var td = $(this).parent();
        var tr = td.parent();
        //change the background color to red before removing
        tr.css("background-color","#FF3700");

        tr.fadeOut(400, function(){
            tr.remove();
        });
    });    */
});


function validateFormA(){  
    $('#formDbman').bootstrapValidator({
        live: 'true',
        excluded: [':disabled'],
        resetFormData: true,
        feedbackIcons: {
            valid: 'icon-checkmark-circle',
            invalid: 'icon-cancel-circle',
            validating: 'icon-spinner7'
        },
        fields: {
            inputTitulo: {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            },
            inputDescripcion: {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            },
            inputOrden: {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    },
                    numeric: {
                        message: 'Este campo acepta solo números'
                    },
                }
            },
            inputEstatus: {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            },
            inputLocate: {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            },
            inputPhotos: {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            },
            inputQrs: {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            },
            inputFirma: {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            },     
        }
    }).on('success.form.fv', function(e) {
        $('.loading-container').removeClass('loading-inactive');
            e.preventDefault();
            var $form = $(e.target);
            var fv = $form.data('FormDataGral');
                fv.defaultSubmit();
        });    
}

function addFieldForm(){
    var countElement = $("#inputCountElements").val();        
    var cboStatus  = $("#divSelectStatus").html();
    var cboOptions = $("#divSelectOptions").html();
    var cboTipo    = $("#divSelectTypes").html();

    $('#FormData3 tr:last').before('<tr><td>--</td><td>'+
                                            '<input name="aElements['+countElement+'][id]" type="hidden" value="-1"/>'+
                                            '<input id="inputOp'+countElement+'"      name="aElements['+countElement+'][op]" type="hidden" value="new"/>'+
                                            '<input id="inputElement'+countElement+'" name="aElements['+countElement+'][orden]" type="text" class="input-inline form-control col-xd-2"  value=""  autocomplete="off">'+                                                 
                                        '</td><td>'+ 
                                            '<select id="inputTipo'+countElement+'" name="aElements['+countElement+'][tipo]">'+
                                                cboTipo+                                         
                                            '</select>'+                                             
                                        '</td><td>'+
                                            '<input id="inputDesc'+countElement+'" name="aElements['+countElement+'][desc]" type="text" class="input-inline form-control col-xd-2"  value=""  autocomplete="off">'+                                                
                                        '</td><td>'+ 
                                            '<select id="inputStat'+countElement+'" name="aElements['+countElement+'][status]">'+
                                                cboStatus+                                         
                                            '</select>'+
                                        '</td><td>'+ 
                                            '<select id="inputStat'+countElement+'" name="aElements['+countElement+'][requerido]">'+
                                                cboOptions+
                                            '</select>'+
                                        '</td><td>'+ 
                                            '<select id="inputStat'+countElement+'" name="aElements['+countElement+'][validacion]">'+
                                                cboOptions+
                                            '</select>'+
                                        '</td><td>'+
                                            '<div class="col-xs-12 no-margin-l">'+
                                                    '<div class="col-xs-6 no-margin-l">'+
                                                        '<button class="btn btn-icon btn-default" onClick="showCloseOptions('+countElement+');return false;"><i id="spanOptions'+countElement+'" class="icon-arrow-down10"></i></button>'+
                                                    '</div>'+
                                                    '<div class="col-xs-6 no-margin-l">'+
                                                        '<button class="btn btn-icon btn-default deleteLink" onClick="deleteFieldForm(this,'+countElement+');return false;"><i class="icon-cancel-circle"></i></button>'+
                                                    '</div>'+                                               
                                            '</div>'+ 
                                        '</td></tr>'+
                                        '<tr id="trOptions'+countElement+'" style="background-color:#f5f5f5;display:none;">'+
                                            '<td colspan="4">'+
                                                '<div id="divOptions'+countElement+'" style="">'+
                                                    '<textarea id="inputOps'+countElement+'" name="aElements['+countElement+'][options]"  rows="4" class="col-xs-12 no-padding"></textarea>'+
                                                    'Opciones (Delimitados por comas <i>ej:uno,dos,tres</i>):'+
                                                '</div>'+
                                            '</td>'+
                                            '<td class="text-right" style="">Depende de (# elemento)</td>'+
                                            '<td>'+
                                                '<input id="inputDepend'+countElement+'" name="aElements['+countElement+'][depend]" type="text" class="input-inline form-control col-xs-8 no-padding"  value=""  autocomplete="off"/>'+
                                            '</td>'+
                                            '<td class="text-right" style="">Cuando sea</td>'+
                                            '<td>'+
                                                '<input id="inputCuando'+countElement+'" name="aElements['+countElement+'][when]" type="text" class="input-inline form-control col-xs-8 no-padding"  value=""  autocomplete="off"/>'+
                                            '</td>'+
                                        '</tr>').fadeIn("slow");
    countElement++;
    $("#inputCountElements").val(countElement);
}

function deleteFieldForm(objectTable,idInput){   
    $("#inputOp"+idInput).val('del');
    var td = $(objectTable).parent().parent().parent();    
    var tr = td.parent();
        tr.fadeOut(400, function(){
            tr.hide('slow');
            $("#trOptions"  +idInput).hide('slow');
        });
}

function showCloseOptions(idInput){
    var open  = $("#spanOptions"+idInput).hasClass('icon-arrow-down10');
    var close = $("#spanOptions"+idInput).hasClass('icon-arrow-up10');
    if(open && close == false){
        $("#spanOptions"+idInput).removeClass('icon-arrow-down10').addClass('icon-arrow-up10');
        $("#trOptions"  +idInput).show();
    }
    
    if(close && open == false){
        $("#spanOptions"+idInput).removeClass('icon-arrow-up10').addClass('icon-arrow-down10');
        $("#trOptions"  +idInput).hide();        
    }
}

var aTypeShowOpts = [ "2","3","4","5","6","7","8","9","19","11","12" ];

function onChangeSelect(valueInput,ObjectInput){
    var bExist = jQuery.inArray(valueInput,aTypeShowOpts);
    if(bExist>-1){
        $("#trOptions"+ObjectInput).show('slow');
    }else{
        $("#trOptions"+ObjectInput).hide('slow');
    }
}