$(document).ready(function(){
    $('.symbionts-notes-admin-main .admin-widget-list').adminList({
	'sort': 'Notes-Admin.dbSort',
	'reload': 'Notes-Admin.notes.id='+$('.symbionts-notes-admin').attr('data-id')+'.order='+$('.symbionts-notes-admin-main').attr('data-order'),
	'change': 'Notes-Admin.change',
	'changeLoad': function(tab){
	    $(this).find('.lightedit').lightedit({
		'top-var':'Admin.top',
		'language': $.symbiosis.language
	    });
	},
	'changeSave': function(tab){
	    var data={};
	    data=$(this).find('.lightedit').lighteditGet();
	    data['symbiont']='Notes-Admin.dbChange';
	    data['id']=$(this).find('.symbionts-notes-admin-change').attr('data-id');
	    data['categoryId']=$(this).closest('.symbionts-notes-admin').attr('data-id');
	    data['date']=$(this).find('.date-input .year').val()+'-'+$(this).find('.date-input .month').val()+'-'+$(this).find('.date-input .day').val()+' '+$(this).find('.date-input .hour').val()+':'+$(this).find('.date-input .minute').val();
	    /*
	    data['tags']=[];
	    widget.find('.symbionts-tags-admin-button .tag').each(function(){
		data['tags'].push($(this).attr('data-id'));
	    });
	    if(data['tags'].length==0) data['tags']='';
	    if(widget.find('.image').attr('data-changed')=='true'){
		data['image']=widget.find('.image').attr('data-image');
	    }
	    */
	    
	    $.ajax({
		'data': data,
		'success':function(result){
		    if(result.success!=undefined){
			//$('.admin-widget-main .admin-list').adminListReload();
			//widget.adminWidgetRemove();
		    }
		    else if(result.error!=undefined){
			//widget.find('.alias').addClass('error');
		    }
		}
	    });
	    return true;
	},
	'remove': 'Categories-Admin.delete',
	'removeOk': 'Categories-Admin.dbDelete',
	'action-settings': function(){
	    if(Admin.tabExists('settings')){
		Admin.tabShow('settings');
	    }
	    else{
		Admin.tabAdd('settings', 'Loading');
		$.ajax({
		    'data': {
			'symbiont': 'Notes-Admin.settings',
			'id': $('.symbionts-notes-admin').attr('data-id')
		    },
		    'dataType': 'html',
		    'success':function(result){
			var html=$(result);
			Admin.init(html);
			html.find('.admin-button-save').click(function(){
			    var order=$('.symbionts-notes-admin-settings .admin-selected').attr('data-type');
			    $('.symbionts-notes-admin-main').attr('data-order', order);
			    $.ajax({
				'data': {
				    'symbiont': 'Notes-Admin.dbSettings',
				    'id': $('.symbionts-notes-admin').attr('data-id'),
				    'settings': {
					'order': order
				    }
				},
				'success':function(){
				    var sortable=$('.symbionts-notes-admin-main .admin-widget-list').attr('data-sortable');
				    var list=$('.symbionts-notes-admin-main .admin-widget-list').data('admin');
				    list.options.reload='Notes-Admin.notes.id='+$('.symbionts-notes-admin').attr('data-id')+'.order='+order;
				    if(sortable=='true'&&order!=0){
					list.unsortable();
				    }
				    else if (sortable=='false'&&order==0){
					list.sortable();
				    }
				    list.reload();
				    Admin.tabRemove('settings');
				}
			    });
			});
			html.find('.admin-button-cancel').click(function(){
			    Admin.tabRemove('settings');
			});
			Admin.tabSet('settings', $('.admin-widget-icon-settings span').html(), html);
			Admin.tabShow('settings');
		    }
		});
	    }
	}
    });
    /*
    $('.symbionts-notes-admin-main .admin-list').data('admin-list', {
	'sort': 'Notes-Admin.dbSort',
	'reload': 'Notes-Admin.main.isGlobal=false',
	'change': 'Notes-Admin.change',
	'changeOk': function(widget){
	    var data={};
	    data=widget.find('.lightedit').lighteditGet();
	    data['symbiont']='Notes-Admin.dbChange';
	    data['id']=widget.attr('data-id')!='0'?widget.attr('data-id'):undefined;
	    //data['alias']=widget.find('.alias:last').val();
	    data['categoryId']=widget.closest('.symbionts-notes-admin').find('.categoryId').val();
	    data['date']=widget.find('.date').val();
	    data['tags']=[];
	    widget.find('.symbionts-tags-admin-button .tag').each(function(){
		data['tags'].push($(this).attr('data-id'));
	    });
	    if(data['tags'].length==0) data['tags']='';
	    if(widget.find('.image').attr('data-changed')=='true'){
		data['image']=widget.find('.image').attr('data-image');
	    }
	    
	    $.ajax({
		'data': data,
		'success':function(result){
		    if(result.success!=undefined){
			$('.admin-widget-main .admin-list').adminListReload();
			widget.adminWidgetRemove();
		    }
		    else if(result.error!=undefined){
			widget.find('.alias').addClass('error');
		    }
		}
	    });
	    window.onbeforeunload = function(evt) {
		return;
	    }
	},
	'changeLoad': function(widget){
	    window.onbeforeunload = function(evt) {
		return " ";
	    }
	    
	    widget.find('.date').datetimepicker({
		'dateFormat': 'yy-mm-dd',
		'timeFormat': 'hh:mm:ss',
		'showSecond': true
	    });
	    $( "#datepicker" ).datepicker("option", $.datepicker.regional[$.symbiosis.code]);
	    
	    widget.find('.lightedit').lightedit();
	    widget.find('.image').click(function(){
		var dialog=$.adminDialog($('.install-template').children().clone(), {x:'center', y:'200', 'width': 600, 'height': 400});
		dialog.addClass('symbionts-notes-filemanager');
		$.ajax({
		    'data': {
			'symbiont': 'Filemanager.mini.path="'+$('.symbionts-notes-admin-main').attr('data-path')+'"'
		    },
		    'dataType': 'html',
		    'success': function(result){
			var div=$(result).adminUI();
			div.find('.files .file').click(function(){
			    var path=$(this).closest('.files').attr('data-path');
			    var name=$(this).attr('data-name');
			    var cover='uploads'+path+'.128/'+name;
			    var file=path+name;
			    widget.find('.image img').attr('src', cover);
			    widget.find('.image').attr('data-image', file);
			    widget.find('.image').attr('data-changed', 'true');
			    $('.symbionts-notes-filemanager').remove();
			    return false;
			});
			$('.symbionts-notes-filemanager .ui-widget-content').append(div);
		    }
		});
	    });
	    
	},
	'changeCancel': function(widget){
	    window.onbeforeunload = function(evt) {
		return;
	    }
	},
	'remove': 'Notes-Admin.delete',
	'removeOk': 'Notes-Admin.dbDelete'
    }).adminList();
    $('.symbionts-notes-admin-main .admin-button-config').click(function(){
	var dialog=$.adminDialog($('.install-template').children().clone(), {x:'center', y:'middle', 'width': 400, 'height': 200});
	dialog.addClass('symbionts-notes-dialog');
	$.ajax({
	    'data': {
		'symbiont': 'Notes-Admin.settings',
		'id': $('.symbionts-notes-admin-main .categoryId').val()
	    },
	    'dataType': 'html',
	    'success': function(result){
		var div=$(result).adminUI();
		$('.symbionts-notes-dialog .ui-widget-content').append(div);
		div.find('li').click(function(){
		    $(this).parent().find('li').removeClass('admin-selected');
		    $(this).addClass('admin-selected');
		});
		div.find('.admin-button-cancel').click(function(){
		    $('.symbionts-notes-dialog').remove();
		});
		div.find('.admin-button-save').click(function(){
		    var dialog=$(this).closest('.symbionts-notes-dialog');
		    var settings={};
		    settings['order']=dialog.find('.order li.admin-selected').attr('data-type');
		    settings['template']=dialog.find('.template li.admin-selected').attr('data-type');
		    settings['coverWidth']=dialog.find('.cover .width input').val();
		    settings['coverHeight']=dialog.find('.cover .height input').val();
		    settings['path']=dialog.find('.path input').val();
		    var id=$('.symbionts-notes-admin-main .categoryId').val();
		    $.ajax({
			'data': {
			    'symbiont': 'Notes-Admin.dbSettings',
			    'settings': settings,
			    'id': id
			},
			'success': function(){
			    $('.admin-widget-main .admin-list').adminListReload();
			    $('.symbionts-notes-dialog').remove();
			}
		    });
		});
	    }
	});
    });
    SNotesAdminLoad(function(){
	var diff = $(document).height()-$(this).scrollTop()-$(window).height();
	SNotesAdminLock=$(window).height();
	if(diff<200&&$(document).height()>SNotesAdminLock){
	    SNotesAdminLock=$(document).height();
	    SNotesAdminLoad();
	}
    });
    SNotesAdminTop=$('.symbionts-notes-admin .controls').offset()["top"];
});
function SNotesAdminLoad(func){
    var order=$('.symbionts-notes-admin-main').attr('data-order');
    var id=$('.symbionts-notes-admin-main .categoryId').val();
    var symbiont='Notes-Admin.notes.order='+order+'.id='+id;
    
    var start='';
    if($('.symbionts-notes-admin-main ul li').length){
	var last=$('.symbionts-notes-admin-main ul li:last');
	if(order==0){ start=last.attr('data-position'); }
	if(order==1){ start=last.attr('data-date'); }
	if(order==2){ start=last.find('.title').text(); }
	if(start){
	    symbiont+='.start="'+start+'"';
	}
    }
    
    $.ajax({
	'data':{
	    'symbiont': symbiont
	},
	'dataType': 'html',
	'success':$.proxy(function(r){
	    if(this[0]==''&&$('.symbionts-notes-admin-main ul li').length!=0) return;
	    var div=$('<div>'+r+'</div>');
	    div.adminUI();
	    div.adminEditable($('.symbionts-notes-admin-main .admin-list').data('admin-list'));
	    $('.symbionts-notes-admin-main ul').append(div.children());
	    if(func!=undefined) func();
	}, [start])
    });
}

var SNotesAdminLock=0;
var SNotesAdminTop=0;
var SNotesAdminLast=null;
$(window).scroll(function(){
    var diff = $(document).height()-$(this).scrollTop()-$(window).height();
    if(diff<200&&$(document).height()>SNotesAdminLock){
        SNotesAdminLock=$(document).height();
        SNotesAdminLoad();
    }
    if($(this).scrollTop()>SNotesAdminTop){
	$('.symbionts-notes-admin-main .controls').css({
	    'position': 'relative',
	    'top': $(this).scrollTop()-SNotesAdminTop
	});
    }
    else{
	$('.symbionts-notes-admin-main .controls').css({
	    'position': 'static',
	    'top': 'auto'
	});
    }
    */
});