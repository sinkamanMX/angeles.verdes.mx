var lastMessage  = -1;
var bChekMessages=true;

$( document ).ready(function() {
	$("#chat-link").click(function () {
	    $('.page-chatbar').toggleClass('open');
	    $("#chat-link").toggleClass('open');
	});

	$('.page-chatbar .chatbar-messages .back').on('click', function (e) {
	    $('.page-chatbar .chatbar-contacts').show();
	    $('.page-chatbar .chatbar-messages').hide();
	});

	scrolling();
	checkMessages();
});

function getConversation(inputContacto,Message,action){
    $('.page-chatbar').addClass('open');
    $("#chat-link").addClass('open');	
    $('.page-chatbar .chatbar-contacts').hide();
    $('.page-chatbar .chatbar-messages').show();	

    $("#txtInput").val(inputContacto);  
    $('#inputMessage').attr("disabled", true);
    $('#divChatMessage').html('<div class="loaderSecondary"></div>').addClass('background-blue');

    var iTimeShow = $("#txtInputTime").val();

    $.ajax( {
      type: "POST",
      url: "/main/messages/chatmessages",
      data: {catId: inputContacto, inputMsg: Message, optReg: action, iTime : iTimeShow},
        success: function(data) {
          	$('#divChatMessage').html(data).removeClass('background-blue');
          	setTimeout(function() {
                         scrolling()
                    }, 100);	
			$('.page-chatbar .chatbar-messages .back').on('click', function (e) {
			    $('#divChatContacts').show();
			    $('#divChatMessage').hide();	
			});           	
          	checkMessages(); 
        }
    }); 
}

function sendMessage(){
	bChekMessages=false;
	$('#inputMessage').attr("disabled", true);

	var inputCatId = $("#txtInput").val();
	var sMessage   = $("#inputMessage").val();

  	$.ajax( {
    	type: "POST",
    	url: "/main/messages/sendmessages",
    	data: {idSendTo: inputCatId, inputMsg: sMessage, optReg: 'sendMessage'},
    	dataType : 'json',    
  		success: function(data){
    		var iStatus = data.ResponseStatus;

        	$('#inputMessage').attr("disabled", false);
          	$("#inputMessage").val("");

	        if(iStatus!=1){
	        	Notify('¡Atención!<br/>Ocurrio un error al enviar el mensaje.', 'top-right', '5000', 'danger', 'fa-bolt', true);
	        }else{ 
	          var dataMensaje = data.DataMessage;
	          var MsgLeido    = '';

	          if(dataMensaje.LEIDO !="" && dataMensaje.PROCESADO!="LEIDO"){
	              MsgLeido = '<img src="/images/icons/double.png"/>';
	          }else if(dataMensaje.PROCESADO !="" && dataMensaje.PROCESADO!="NULL"){
	              MsgLeido =  '<img src="/images/icons/single.png"/>';
	          }else{
	              MsgLeido = '<i class="fa fa-clock-o"></i>';
	          } 

		        $("#txtContactFirst").val(dataMensaje.ID);
		        $("#txtLastMessage").val(dataMensaje.ID_MENSAJE);
		        $("#txtNameFirst").val(dataMensaje.NOMBRE);	          

	          $('<li class="message">'+
			     	'<div class="message-info">'+
			        	'<div class="bullet"></div>'+
			            '<div class="contact-name">Yo</div>'+
			            '<div class="message-time">'+												
				            + MsgLeido +
				            + dataMensaje.CREADO+
						'</div>'+
			        '</div>'+
					'<div class="message-body">'
			        	+ dataMensaje.MENSAJE+
					'</div>'+
				'</li>').hide().appendTo( "#chatsMessages" ).fadeIn('slow');	          
	        }
	        
		    bChekMessages=true;
			setTimeout(function(){ 
		        checkMessages()
		    }, 20000);
  		}
  	});
}

function checkMessages(){
	if(bChekMessages){
		bChekMessages=false;
		lastMessage = $("#txtLastMessage").val();
	  	$.ajax( {
		    type: "POST",
		    url: "/main/messages/checkmessage",
		    dataType : 'json',
		    success: function(data) {
			    var sAnswer = data;

			    if(typeof(sAnswer.ID_MENSAJE) != "undefined" && sAnswer.ID_MENSAJE !== null) {	
				      if(sAnswer.ID_MENSAJE != lastMessage){
				        if ($('#aList'+sAnswer.ID).length > 0) {
				           $('#aList'+sAnswer.ID).remove();
				        }

				        var sMsg = sAnswer.MENSAJE;
				        var sTimeAgo = '';
				        if(sAnswer.HAGODAYS>0){
				          sTimeAgo = 'Mas de '+sAnswer.HAGODAYS+' dia (s).';
				        }else if(sAnswer.HAGOHOURS>0){
				          sTimeAgo = 'Hace '+sAnswer.HAGOHOURS +' hora (s).';
				        }else if(sAnswer.HAGOMINS>0){          
				          sTimeAgo = 'Hace '+sAnswer.HAGOMINS  +' minuto (s).';
				        }else{
				          sTimeAgo = 'Menos de 1 minuto.';
				        }
				        
				        $("#txtContactFirst").val(sAnswer.ID);
				        $("#txtLastMessage").val(sAnswer.ID_MENSAJE);
				        $("#txtNameFirst").val(sAnswer.NOMBRE);

		                $('<li class="contact" id="aList'+sAnswer.ID+'" onClick="getConversation('+sAnswer.ID+',\' \',\' \');">'+
							'<div class="contact-avatar">'+
		                     	'<img src="/images/icons/icon-user.png" />'+
		                    '</div>'+
		                    '<div class="contact-info">'+
		                        '<div class="contact-name">'+sAnswer.NOMBRE+'</div>'+
		                        '<div class="contact-status">'+
									'<div class="online"></div>'+
		                            '<div class="status">Disponible</div>'+
		                        '</div>'+
		                        '<div class="last-chat-time">'+
		                            '<span class="aTimeAgo">'+
		                             '<i class="fa fa-clock-o"></i>'+ sTimeAgo +
		                        '</div>'+
		                    '</div>'+
		                '</li>').hide().prependTo( "#classContacts" ).fadeIn('slow');

				        var idSelect = $("#txtInput").val();

				        if(data.ID==idSelect){		          
				          	var dataMensaje = sAnswer.MENSAJE;
				          	var MsgLeido    = '';

				          	if(data.OPTION=='IN'){
								$('<li class="message reply">'+
			                        '<div class="message-info">'+
			                            '<div class="bullet"></div>'+
			                            '<div class="contact-name">'+sAnswer.NOMBRE+'</div>'+
			                            '<div class="message-time">'+sAnswer.CREADO+'</div>'+
			                        '</div>'+
			                        '<div class="message-body">'+ sAnswer.MENSAJE+
			                        '</div>'+
			                    '</li>').hide().appendTo( "#chatsMessages" ).fadeIn('slow');
				          	}else if(data.OPTION=='OUT'){
								$('<li class="message">'+
			                        '<div class="message-info">'+
			                            '<div class="bullet"></div>'+
			                            '<div class="contact-name">'+sAnswer.NOMBRE+'</div>'+
			                            '<div class="message-time">'+sAnswer.CREADO+'</div>'+
			                        '</div>'+
			                        '<div class="message-body">'+ sAnswer.MENSAJE+
			                        '</div>'+
			                    '</li>').hide().appendTo( "#chatsMessages" ).fadeIn('slow');			          		
				          	}
		                    
				          	scrolling();
				        }else{
				        	var MessageAlert = '<div onClick="getConversation('+sAnswer.ID+',\' \',\' \');">'+
				        						'<img width="24px" height="24px" src="/images/icons/icon-user.png">'+
				        						'<span style="padding-left:10px;">'+sAnswer.NOMBRE+' te ha enviado un mensaje</span></div>';

							Notify(MessageAlert, 'top-right', '5000', 'blue', 'fa-envelope-o', true);					        
				        }
				    }
			    }

			    bChekMessages=true;
				setTimeout(function(){ 
			        checkMessages()
			    }, 20000);
	    	}
		});  		
	}
}

function scrolling(){
	$('.chatbar-contacts .contacts-list').slimscroll({
	    position: 'right',
	    size: '4px',
	    color: themeprimary,
	    height: $(window).height() - 86,
	});	

    var cont = $('.chats');
    var list = $('.messages-list', cont);
	$('.chatbar-messages .messages-list').slimscroll({
	    scrollTo: list.height(),
	    position: 'right',
	    size: '4px',
	    color: themeprimary,
	    height: $(window).height() - 250,
	});   

    $('.messages-list', cont).slimScroll({
        scrollTo: list.height()
    }); 
}

function backToContacts(){
    $('#divChatContacts').show();
    $('#divChatMessage').hide();	
}