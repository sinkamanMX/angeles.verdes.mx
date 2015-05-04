$( document ).ready(function() {
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    $('#inputDate').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
          onRender: function(date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
          }
    }).on('changeDate', function(ev) {
		getHorariosCbo(this.value);
	});

    $('#FormData').bootstrapValidator({
        live: 'true',
        excluded: [':disabled'],
        resetFormData: true,
        feedbackIcons: {
            valid: 'icon-checkmark-circle',
            invalid: 'icon-cancel-circle',
            validating: 'icon-spinner7'
        },
        fields: {
            inputhorario   : {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            },   
            inputContacto  : {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            }, 
            inputDate: {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            }
        }
    }).on('success.form.fv', function(e) {
        $('.loading-container').removeClass('loading-inactive');
            e.preventDefault();
            var $form = $(e.target);
            var fv = $form.data('FormDataGral');
                fv.defaultSubmit();
        });       


    $('.upper').keyup(function()
    {
        $(this).val($(this).val().toUpperCase());
    });           	
}); 


function getHorariosCbo(inDate){    
    $("#divhorario").html('<img id="loader1" class="col-xs-offset-4" src="/images/loading.gif" alt="loading gif"/>');
    $('#inputhorario').find('option').remove().end().hide('slow');
    $.ajax({
        url: "/callcenter/newservice/gethorarios",
        type: "GET",
        data: { dateID : inDate },
        success: function(data) { 
            $("#divhorario").html("");
            if(data!="no-info"){
                $('#inputhorario').append('<option value="">Seleccionar una opción</option>'+data+'</select>');
            }else{
                $('#inputhorario').append('<option value="">Sin Información</option>');
            }
            $('#inputhorario').show('slow');    
        }
    });     
}   