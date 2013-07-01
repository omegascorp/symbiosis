$(document).ready(function(){
    SNotesLoad(function(func){
        SNotesLoaded=true;
        var diff = $(document).height()-$(this).scrollTop()-$(window).height();
        if(diff<200&&$(document).height()>SNotesLock){
            SNotesLoad(func);
        }
    });
});
function SNotesLoad(func){
    var order=$('.symbionts-notes-category').attr('data-order');
    var id=$('.symbionts-notes-category').attr('data-id');
    var template=$('.symbionts-notes-category').attr('data-template');
    var symbiont='Notes.notes.order='+order+'.id='+id;
    
    if($('.symbionts-notes-category .note').length){
	var last=$('.symbionts-notes-category .note:last');
	if(order==0) symbiont+='.start="'+last.attr('data-position')+'"';
	if(order==1) symbiont+='.start="'+last.attr('data-date')+'"';
	if(order==2) symbiont+='.start="'+last.find('.title').text()+'"';
    }
    if(template) symbiont+='|'+template;
    $.ajax({
	'data':{
	    'symbiont': symbiont,
	    'link': $.symbiosis.link
	},
	'dataType': 'html',
	'success':function(r){
	    var div=$('<div>'+r+'</div>');
	    $('.symbionts-notes-category .notes').append(div.children());
	    if(func!=undefined) func(func);
	}
    });
}
var SNotesLock=0;
var SNotesLoaded=false;
$(window).scroll(function(){
    var diff = $(document).height()-$(this).scrollTop()-$(window).height();
    if(diff<200&&$(document).height()>SNotesLock&&SNotesLoaded){
        SNotesLock=$(document).height();
        SNotesLoad();
    }
});