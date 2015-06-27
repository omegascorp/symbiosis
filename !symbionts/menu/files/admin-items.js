$(document).ready(function(){
    $('.symbionts-menu-admin-items-main .admin-widget-list').adminList({
	'sort': 'Menu-AdminItems.dbSort',
	'reload': 'Menu-AdminItems.items',
	'change': 'Menu-AdminItems.change',
	'changeLoad': function(tab){
	    
	},
	'changeSave': function(tab){
	    var data={};
	    data['symbiont']='Menu-AdminItems.dbChange';
	    data['menuId']=$(this).find('.symbionts-menu-admin-items-change').attr('data-menuId');
	    data['id']=$(this).find('.symbionts-menu-admin-items-change').attr('data-id');
	    data['languages']={};
	    $(this).find('.admin-tab').each(function(){
		language={};
		language['title']=$(this).find('.title').val();
		language['link']=$(this).find('.link').val();
		data['languages'][$(this).attr('data-id')]=language;
	    });
	    data['parentId']=$(this).find('.parent').val();
	    data['menuId']=$('.symbiosis-menu-admin-items').attr('data-id');
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
	'remove': 'Menu-AdminItems.delete',
	'removeOk': 'Menu-AdminItems.dbDelete'
    });
});
