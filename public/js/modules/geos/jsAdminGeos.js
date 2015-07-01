$().ready(function() {
    $('#iFrameModalMapa').on('load', function () {        
      $('#loader').hide();
      $('#iFrameModalMapa').show();
    }); 	

	$('#myModalMapa').on('hidden.bs.modal', function () {
		location.reload();    
	})    
});

function importGeos(){
    $('#iFrameModalMapa').attr('src','/admin/geos/importdata');
    $("#myModalMapa").modal("show");
}