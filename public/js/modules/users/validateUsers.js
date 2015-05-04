$().ready(function() {
    $( "#FormData" ).submit(function( event ) {
      event.preventDefault();
    });

    var formData = $('#FormData');
    var dError   = $('.alert-danger' , formData);
    var dSucess  = $('.alert-success', formData);

    formData.validate({
        errorElement: 'span', //default input error message container
        errorClass  : 'help-block', // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        ignore      : "",
        rules: {
            inputSucursal:      "required",
            inputUsuario:   "required",
            inputPassword:  "required",
            inputPasswordC: {
                required: true,
                equalTo: "#inputPassword",
            },  
            inputNombre :   "required",
            inputApps   :   "required",          
            inputEstatus:   "required",
            inputOperaciones:"required",
            inputEmail  : {
                required: true,
                email: true
            },              
            inputMovil    : {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10
            }       
        },
        messages: {
            inputSucursal   : "Campo Requerido",
            inputUsuario    : "Campo Requerido",
            inputPassword   : "Campo Requerido",
            inputNombre     : "Campo Requerido",
            inputApps       : "Campo Requerido",
            inputEstatus    : "Campo Requerido",
            inputOperaciones: "Campo Requerido",
            inputEmail      : {
                required: "Campo Requerido",
                email: "Debe de ingresar un mail válido"
            },
            inputPasswordC  : {
                required    : "Campo Requerido",
                equalTo     : "La contraseña no coincide."
            }, 
            inputMovil    : {
                required  : "Campo Requerido",
                number    : "Este campo acepta solo números",
                minlength : "El Teléfono debe de ser de 10 dígitos",
                maxlength : "El Teléfono debe de ser de 10 dígitos"
            },                           
        },

        invalidHandler: function (event, validator) { //display error alert on form submit              
            /*dSucess.hide();
            dError.show();
            App.scrollTo(dError, -200);*/
            var shortCutFunction= 'error';
            var msg             = "El registro contiene uno o mas errores, favor de verificarlos.";
            var title           = "Atención!"

            toastr.options = {
              "closeButton": true,
              "debug": false,
              "positionClass": "toast-top-right",
              "showDuration": "1000",
              "hideDuration": "1000",
              "timeOut": "5000",
              "extendedTimeOut": "1000",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"
            }
            var $toast = toastr[shortCutFunction](msg, title);            
        },

        highlight: function (element) { // hightlight error inputs
            $(element)
                .closest('.form-group').addClass('has-error'); // set error class to the control group
        },

        unhighlight: function (element) { // revert the change done by hightlight
            $(element)
                .closest('.form-group').removeClass('has-error'); // set error class to the control group
        },

        success: function (label) {
            label
                .closest('.form-group').removeClass('has-error'); // set success class to the control group
        },

        submitHandler: function (form) {
            App.startPageLoading();         
            $('.btnSave').attr("disabled", true);
            form.submit();             
        } 
    });

    if($("#catId").val()>-1){
        $("#inputPassword").rules("remove", "required");
        $("#inputPasswordC").rules("remove", "required");          
    }     
});

function optionAll(inputCheck){
    if(inputCheck){
        $('.chkOn').prop('checked', true);         
    }else{
        $('.chkOn').prop('checked', false);
    }
}

function addValidatePass(valueInput){
    if(valueInput!=""){
        $("#inputPassword").rules("add",  {required:true});
        $("#inputPasswordC").rules("add", {required: true,equalTo: "#inputPassword"});   
    }else{
        $("#inputPassword").rules("remove", "required");
        $("#inputPasswordC").rules("remove", "required");
    }
}