function getoptionsCbo(idCboTo,classObject,idObject,chosen,options){      
    revalidate('input'+idCboTo);
    $("#div"+idCboTo).html('<img id="loader1" class="col-xs-offset-4" src="/images/loading.gif" alt="loading gif"/>');
    $('#input'+idCboTo).find('option').remove().end().hide('slow');
    var classChosen = (chosen) ? 'chosen-select': '';
    var claseFind   = (options=='coloniaO') ? 'colonia': options;
    var optionSelect= (options!='') ? 'getoptionsCbo("'+options+'","'+claseFind+'",this.value,false,"");': '';
    var optsCP      = (idCboTo=='colonia' || idCboTo=='coloniaO') ? 'getCPdir(this.value,"'+idCboTo+'");': '';
    $.ajax({
        url: "/main/functions/getselect",
        type: "GET",
        data: { catId : idObject, 
                oprDb : classObject },
        success: function(data) {   
            $("#div"+idCboTo).html("");
            if(data!="no-info"){
                $('#input'+idCboTo).append('<option value="">Seleccionar una opción</option>'+data+'</select>');
            }else{
                $('#input'+idCboTo).append('<option value="">Sin Información</option>');
            }
            $('#input'+idCboTo).show('slow');            
        }
    });        
}

function goToMainModule(){
    var moduleUrl = $("#inputModule").val();
    location.href = moduleUrl;  
}

function getContentDiv(sNameDiv,sUrlContent,sParams){
    $('#'+sNameDiv).html('<img id="loader1" class="col-xs-offset-4" src="/images/loading.gif" alt="loading gif"/>');
    $('#'+sNameDiv).load(sUrlContent + sParams);
}

function optionAll(inputCheck){
    if(inputCheck){
        $('.chkOn').prop('checked', true);         
    }else{
        $('.chkOn').prop('checked', false);
    }
}

function validateListCheks(sNameForm){
    var selected = '';    
    $('#'+sNameForm+' input[type=checkbox]').each(function(){
        if (this.checked) {
            selected += $(this).val()+', ';
        }
    }); 

    if (selected != ''){
        $("#"+sNameForm).submit();
    }else{
        $.jGrowl('Debe de seleccionar al menos una opción', { sticky: false, theme: 'growl-error', header: '¡Atención!' ,life: 3000 });        
        //Notify('Debe de seleccionar al menos una opción', 'top-right', '5000', 'danger', 'fa-exclamation-circle', true); 
    }       

    return false;    
}

function showNotification(sType,sMessage){
    if(sType=="registerOk"){
        $.jGrowl('Los Datos se almacenaron correctamente.', { sticky: false, theme: 'growl-success', header: '¡Atención!' ,life: 3000 });        
        //Notify('Los Datos se almacenaron correctamente.', 'top-right', '5000', 'success', 'fa-check', true);
    }
}

function setTabSelected(iCurrentTab){
    $("#strTabSelected").val(iCurrentTab);
}

function setValueInput(sValue,inputFile){
    $("#"+inputFile).val(sValue);
}

function validateRangeHour(nameInputMin,nameInputmax,checkType){
    var nowTemp = new Date();    

    var valueMin = $("#"+nameInputMin).val().split(':');
    var valueMax = $("#"+nameInputmax).val().split(':');

    var tDateIn  = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(),valueMin[0],valueMin[1]);
    var tDateEnd = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(),valueMax[0],valueMax[1]);

    if(tDateIn.valueOf()>tDateEnd.valueOf() && checkType==1){
        $("#"+nameInputmax).val($("#"+nameInputMin).val());
    }

    if(tDateIn.valueOf()>tDateEnd.valueOf() && checkType==2){
        $("#"+nameInputMin).val($("#"+nameInputmax).val());
    }
}