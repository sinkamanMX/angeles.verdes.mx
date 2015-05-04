var sDate     = '';
var lastView;
var srcByDate = "/atn/main/getcitaspendientes?iType=1";
var srcbyHour = "/atn/main/getcitaspendientes?iType=2";

$( document ).ready(function() {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaDay'
        },
        lang: 'es',
        editable: false,
        eventLimit: true, // allow "more" link when too many events
        events: {
            url: srcByDate,
            error: function() {
                $('#script-warning').show();
            }
        },
        loading: function(bool) {
            $('#loading').toggle(bool);
        },   
        dayClick: function(date, jsEvent, view) {
            $('#calendar').fullCalendar( 'removeEventSource', srcByDate )
            $('#calendar').fullCalendar( 'addEventSource', srcbyHour  ); 
            $('#calendar').fullCalendar( 'changeView', 'agendaDay' );     
            $('#calendar').fullCalendar( 'gotoDate', date );
        },
        eventClick: function (event) {
            showDates(event.IDS);
            return false;
        }                 
    }).on('click', '.fc-month-button', function(){
        $('#calendar').fullCalendar( 'removeEventSource', srcbyHour );
        $('#calendar').fullCalendar( 'addEventSource', srcByDate );
    }).on('click', '.fc-agendaDay-button', function() {      
        $('#calendar').fullCalendar( 'removeEventSource', srcByDate )
        $('#calendar').fullCalendar( 'addEventSource', srcbyHour );
    }); 

    $('#iFrameDetCita').on('load', function () {        
        $('#loader').hide();
        $('#iFrameDetCita').show();
    });      
    $('#iFrameModaCita').on('load', function () {        
        $('#loader2').hide();
        $('#iFrameModaCita').show();
    });      
    $('#frameList').on('load', function () {        
        $('#loader3').hide();
        $('#frameList').show();
    });              
});

function searchDialog(){    
    $('#loader2').show();
    $('#iFrameModaCita').hide();  
    $('#iFrameModaCita').attr('src','/atn/main/searchcitas');
    $("#MyModalSearch").modal("show");
}

function showDetail(idDate){
    $('#loader').show();  
    $('#loader2').show();  
    $('#loader3').show();  

    $('#frameList').hide();
    $("#mModalList").modal("hide");
    
    $('#iFrameDetCita').hide();  
    $("#MyModalSearch").modal("hide");


    $("#myModalinfoVis").modal("show");        
    $('#iFrameDetCita').attr('src','/atn/main/citadetalle?strInput='+idDate);    
}

function closeDetail(){
    $("#MyModalSearch").modal("hide");
    $("#myModalinfoVis").modal("hide");    
    loadCalendar()
}

function loadCalendar(){
    $('#calendar').fullCalendar('refetchEvents');
}

function showDates(sDates){
    $('#loader3').show();  
    $('#frameList').hide();  
    $("#mModalList").modal("show");        
    $('#frameList').attr('src','/atn/main/getlistdates?strInput='+sDates);  
}