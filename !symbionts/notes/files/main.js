$(document).ready(function(){
    SNotesLoad();
});
function SNotesLoad(func){
    var order=$('.symbionts-notes-category').attr('data-order');
    var id=$('.symbionts-notes-category').attr('data-id');
    var template=$('.symbionts-notes-category').attr('data-template');
    var page=$('.symbionts-notes-category').attr('data-page');
    var symbiont='#Notes.notes';
    if(template){
	symbiont+='.'+template;
    }
    var attrs='id='+id;
    attrs+=',order='+order;
    attrs+=',page='+page;
    
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