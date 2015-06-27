$(document).ready(function(){
    $('.symbionts-languages-admin-main .admin-widget-list').adminList({
	'sort': 'Languages-Admin.dbSort',
	'reload': 'Languages-Admin.items',
	'change': 'Languages-Admin.change',
	'changeLoad': function(tab){
	    
	},
	'changeSave': function(tab){
	    var abbr=$(this).find('.abbr').val();
	    var title=$(this).find('.title').val();
	    var titleEn=$(this).find('.titleEn').val();
	    var code=$(this).find('.code').val();
	    var id=$(this).find('.symbionts-languages-admin-change').attr('data-id');
	    $.ajax({
		'data': {
		    'symbiont': 'Languages-Admin.dbChange',
		    'abbr': abbr,
		    'title': title,
		    'titleEn': titleEn,
		    'code': code,
		    'id': id
		},
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
	'remove': 'Languages-Admin.delete',
	'removeOk': 'Languages-Admin.dbDelete'
    });
    
    $('.symbionts-languages-admin-main .enabled')
    .click(function(){
        var id=$(this).closest('.admin-item').attr('data-id');
	var enabled=$(this).hasClass('admin-checked')?1:0;
        $.ajax({
	    'data':{
                'symbiont': 'Languages-admin.dbEnabled',
                'id': id,
                'enabled': enabled
            },
	    'success':function(){
		
	    }
	});
    });
    
    $('.symbionts-languages-admin-main .default')
    .click(function(){
        var id=$(this).closest('.admin-item').attr('data-id');
        $.ajax({
	    'data':{
                'symbiont': 'Languages-admin.dbDefault',
                'id': id
            },
	    'success':function(){
		
	    }
	});
    });
    /*
    $('.symbionts-languages-admin-main .admin-list').data('admin-list', {
	'sort': 'Languages-admin.dbSort',
	'reload': 'Languages-Admin.main.isGlobal=false',
	'change': 'Languages-admin.change',
	'changeOk': function(widget){
	    var abbr=widget.find('.abbr').val();
	    var title=widget.find('.title').val();
	    var titleEn=widget.find('.titleEn').val();
	    var code=widget.find('.code').val();
	    var id=widget.find('.id').val();
	    $.ajax({
		'data':{
		    'symbiont': 'Languages-admin.dbChange',
		    'abbr': abbr,
		    'title': title,
		    'titleEn': titleEn,
		    'code': code,
		    'id': id
		},
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
	'remove': 'Languages-admin.delete',
	'removeOk': 'Languages-admin.dbDelete'
    });
    
    $('.symbionts-languages-admin-main .enable')
    .click(function(){
        id=$(this).prev().val();
        enabled=$(this).hasClass('ui-state-active')?1:0;
        $.ajax({
	    'data':{
                'symbiont': 'Languages-admin.dbEnabled',
                'id': id,
                'enabled': enabled
            },
	    'success':function(){
		
	    }
	});
    });
    
    $('.symbionts-languages-admin-main .default')
    .click(function(){
        var id=$(this).prev().val();
        $.ajax({
	    'data':{
                'symbiont': 'Languages-admin.dbDefault',
                'id': id
            },
	    'success':function(){
		
	    }
	});
    });
    */
});