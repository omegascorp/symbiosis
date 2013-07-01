$(document).ready(function(){
    $('.symbionts-menu-admin .admin-widget-list').adminList({
	'sort': 'Menu-Admin.dbSort',
	'reload': 'Menu-Admin.items',
	'change': 'Menu-Admin.change',
	'changeLoad': function(tab){
	    
	},
	'changeSave': function(tab){
	    var data={};
	    data['symbiont']='Menu-Admin.dbChange';
	    data['id']=$(this).find('.symbionts-menu-admin-change').attr('data-id');
	    
	    if($(this).find('.admin-tabs').length){
		data['languages']={};
		$(this).find('.admin-tab').each(function(){
		    language={};
		    language['title']=$(this).find('.title').val();
		    language['alias']=$(this).find('.alias').val();
		    data['languages'][$(this).attr('data-id')]=language;
		});
	    }
	    else{
		data['title']=$(this).find('.title').val();
	    }
	    data['alias']=$(this).find('.alias:not(.admin-tab .alias)').val();
	    $.ajax({
		'data': data,
		'success':$.proxy(function(result){
		    if(result.success!=undefined){
			
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
	'remove': 'Menu-Admin.delete',
	'removeOk': 'Menu-Admin.dbDelete'
    });
});
