$().ready(function() {
    $('#iFrameImport').on('load', function () {        
      $('#loader').hide();
      $('#iFrameImport').show();
    }); 	

	$('#myModalImport').on('hidden.bs.modal', function () {
		location.reload();    
	})    
});

function importData(){
	var urlAction = $("#inputImport").val();
    $('#iFrameImport').attr('src', urlAction);
    $("#myModalImport").modal("show");
}