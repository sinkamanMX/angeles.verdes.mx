$( document ).ready(function() {
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
            inputSerie   : {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            },   
            inputColor  : {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            }, 
            inputAno    : {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            }, 
            inputPlacas : {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            }, 
            inputMarca  : {
                validators: {
                    notEmpty: {
                        message: 'Campo requerido'
                    }
                }
            }, 
            inputModelo : {
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
        
    $('.upper').keyup(function()
    {
        $(this).val($(this).val().toUpperCase());
    });    
});


function revalidate(inputName){
    $('#FormData').bootstrapValidator('updateStatus', inputName, 'NOT_VALIDATED');
}