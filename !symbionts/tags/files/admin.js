$(document).ready(function(){
    $('.symbionts-tags-admin-main .admin-list').data('admin-list', {
	'sort': 'Tags-Admin.dbSort',
	'reload': 'Tags-Admin.main.isGlobal=false',
	'change': 'Tags-Admin.change',
	'changeOk': function(widget){
	    var data={};
	    data['symbiont']='Tags-Admin.dbChange'
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
	'remove': 'Tags-Admin.delete',
	'removeOk': 'Tags-Admin.dbDelete'
    });
});