$().ready(function() {
    $('#iFrameSearch').on('load', function () {        
        $('#loader1').hide();
        $('#iFrameSearch').show();
    }); 
    $('#myModalinfoVis').bind('hide', function () {
	   location.href='/atn/autorizacion/index';
	 });
});

function showDetail(idDate){
	$('#loader1').show();
	$('#iFrameSearch').hide(); 
    $("#myModalinfoVis").modal("show");        
    $('#iFrameSearch').attr('src','/atn/autorizacion/citadetalle?strInput='+idDate);    
}

function closeDetail(){
	$("#myModalinfoVis").modal("hide");  
}