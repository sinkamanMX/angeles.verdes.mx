function setValueForm(inputValue){
	$("#optImp").val(inputValue);
	$("#formOptions").submit();
}

$().ready(function() {	

});

function checkfile(sender) {
    var validExts = new Array(".xlsx", ".xls");
    var fileExt = sender.value;
    fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
    if (validExts.indexOf(fileExt) < 0) {
      alert("Invalid file selected, valid files are of " +
               validExts.toString() + " types.");
       $("#imageProfile").val("");
       $("#btnSend").hide('slow');
      return false;
    }else{
    	$("#btnSend").show('slow');
    	return true;	
    	
    } 
}