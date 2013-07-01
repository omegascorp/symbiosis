$(document).ready(function(){
    $('.symbionts-categories-admin-main .admin-widget-list').adminList({
	'sort': 'Categories-Admin.dbSort',
	'reload': 'Categories-Admin.categories.for='+$('.symbionts-categories-admin').attr('data-for'),
	'change': 'Categories-Admin.change.for='+$('.symbionts-categories-admin').attr('data-for'),
	'changeLoad': function(tab){
	    
	},
	'changeSave': function(tab){
	    var data={};
	    data['symbiont']='Categories-Admin.dbChange'
	    if($(this).find('.admin-tabs').length){
		data['languages']={};
		$(this).find('.admin-tab').each(function(){
		    id=$(this).attr('data-id');
		    language={};
		    language['title']=$(this).find('.title input').val();
		    language['alias']=$(this).find('.alias input').val();
		    data['languages'][id]=language;
		});
	    }
	    else{
		data['title']=$(this).find('.title input').val();
	    }
	    data['alias']=$(this).find('.alias input:not(.admin-tab .alias input)').val();
	    data['id']=$(this).find('.symbionts-categories-admin-change').attr('data-id');
	    data['parentId']=$(this).find('.parent select').val();
	    data['for']=$('.symbionts-categories-admin').attr('data-for');
	    $.ajax({
		'data': data,
		'success':$.proxy(function(result){
		    if(result.success!=undefined){
			//widget.adminWidgetRemove();
			//$('.admin-widget-main .admin-list').adminListReload();
		    }
		    else if(result.error!=undefined){
			var offset=$(this).find('.admin-button-save').offset();
			var popup=new AdminLayerTooltip({
			    'x': offset.top,
			    'y': offset.left,
			    'content': result.error,
			    'type': 'bottom',
			    'tail': 'left'
			});
		    }
		}, this)
	    });
	    return true;
	},
	'remove': 'Categories-Admin.delete',
	'removeOk': 'Categories-Admin.dbDelete'
    });
    /*
    $('.symbionts-categories-admin-main .admin-list').data('admin-list', {
	'sort': 'Categories-Admin.dbSort',
	'reload': 'Categories-Admin.main.isGlobal=false.for="'+$('.symbionts-categories-admin-main .for').val()+'".title="'+$('.symbionts-categories-admin-main h1').html()+'"',
	'change': 'Categories-Admin.change.for="'+$('.symbionts-categories-admin-main .for').val()+'"',
	'changeOk': function(widget){
	    var data={};
	    data['symbiont']='Categories-Admin.dbChange'
	    if(widget.find('.tabs').length){
		data['languages']={};
		widget.find('.tab').each(function(){
		    id=$(this).find('.id').val();
		    language={};
		    language['title']=$(this).find('.title input').val();
		    language['alias']=$(this).find('.alias input').val();
		    data['languages'][id]=language;
		});
	    }
	    else{
		data['title']=widget.find('.title input').val();
	    }
	    data['alias']=widget.find('.alias input:not(.tab .alias input)').val();
	    data['id']=widget.find('.id:first').val();
	    data['for']=widget.find('.for').val();
	    $.ajax({
		'data': data,
		'success':function(result){
		    if(result.success!=undefined){
			$('.admin-widget-main .admin-list').adminListReload();
			widget.adminWidgetRemove();
		    }
		    else if(result.error!=undefined){
			
		    }
		}
	    });
	},
	'changeLoad': function(widget){
	    widget.find('.tabs').tabs();
	},
	'remove': 'Categories-Admin.delete',
	'removeOk': 'Categories-Admin.dbDelete'
    });
    $('.symbionts-categories-admin-main .admin-button-config').click(function(){
	var symbiont=$(this).attr('data-settings');
	var dialog=$.adminDialog(null, {x:'center', y:'middle', 'width': 300, 'height': 300});
	dialog.addClass('symbionts-categories-settings');
	$.ajax({
	    'data': {
		'symbiont': symbiont
	    },
	    'dataType': 'html',
	    'success':function(r){
		var div=$(r);
		dialog.find('.ui-widget-content').append(div);
	    }
	});
    });
    */
});