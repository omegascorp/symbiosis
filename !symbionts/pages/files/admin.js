$(document).ready(function(){
    $('.symbionts-pages-admin-main .admin-widget-list').adminList({
	'sort': 'Pages.dbSort',
	'reload': 'Pages.pages',
	'change': 'Pages.change',
	'changeLoad': function(tab){
	    $(this).find('.templates .template').click(function(){
		$.ajax({
		    'data': {
			'symbiont': 'Pages.readTemplate',
			'template': $(this).attr('data-template')
		    },
		    'success': $.proxy(function(r){
			if(r==undefined) return;
			$(this).find('.subtemplates').html('');
			r.sort(function(i,j){
			    if(i['template']>j['template']){
				return 1;
			    }
			    else if(i['template']<j['template']){
				return -1;
			    }
			    return 2;
			});
			var current=$(this).find('.templates').attr('data-current');
			for(key in r){
			    val=r[key];
			    var template=$('<div class="subtemplate admin-radio admin-block" data-template="'+val.template+'"><span class="admin-icon"></span><span class="admin-text">'+val.title+'</span></div>')
			    if(current==val.template){
				template.addClass('admin-selected');
			    }
			    $(this).find('.subtemplates').append(template);
			}
			if($(this).find('.subtemplates .subtemplate.admin-selected').length==0){
			    $(this).find('.subtemplates .subtemplate:first').addClass('admin-selected');
			}
			Admin.init($(this).find('.subtemplates'));
			$(this).find('.templates').attr('data-current', $(this).find('.subtemplates .subtemplate.admin-selected').attr('data-template'));
			
			$(this).find('.subtemplates .admin-block').click(function(){
			    $(this).closest('.admin-layout-50').find('.templates').attr('data-current', $(this).attr('data-template'));
			});
			
		    }, $(this).closest('.symbionts-pages-admin-change').get(0))
		});
	    });
	    $(this).find('.templates .template.admin-selected').click();
	},
	'changeSave': function(tab){
	    var aliasG=$(this).find('.alias input:not(.admin-tabs .alias input)').val()!='';
	    var aliasL=true;
	    $(this).find('.admin-tab alias input').each(function(){
		aliasL=aliasL&&$(this).val()!='';
	    });
	    if(!$(this).find('.admin-tab alias input').length) aliasL=false;
	    if(!aliasG&&!aliasL){
		var offset=$(this).find('.admin-button-save').offset();
		var popup=new AdminLayerTooltip({
		    'x': offset.top,
		    'y': offset.left,
		    'content': $(this).find('.emptyAlias').html(),
		    'type': 'bottom',
		    'tail': 'left'
		});
		return false;
	    }
	    var data={};
	    data['symbiont']='Pages.dbChange';
	    data['id']=$(this).find('.symbionts-pages-admin-change').attr('data-id');
	    data['position']=$(this).find('.symbionts-pages-admin-change').attr('data-position');
	    data['alias']=$(this).find('.alias input:not(.admin-tabs .alias input)').val();
	    data['title']=$(this).find('.title input:not(.admin-tabs .title input)').val();
	    data['keywords']=$(this).find('.keywords input:not(.admin-tabs .keywords input)').val();
	    data['description']=$(this).find('.description input:not(.admin-tabs .description input)').val();
	    data['parentId']=$(this).find('.parent select').val();
	    data['redirectId']=$(this).find('.redirect select').val();
	    data['accessLevel']=$(this).find('.accessLevel select').val();
	    data['template']=$(this).find('.templates').attr('data-current');
	    data['isHome']=$(this).find('.isHome').hasClass('admin-checked')?1:0;
	    data['is404']=$(this).find('.is404').hasClass('admin-checked')?1:0;
	    data['isActive']=$(this).find('.isActive').hasClass('admin-checked')?1:0;
	    data['isHidden']=$(this).find('.isHidden').hasClass('admin-checked')?1:0;
	    data['languages']={};
	    $(this).find('.admin-tab').each(function(){
		data['languages'][$(this).attr('data-id')]={
		    'title': $(this).find('.title input').val(),
		    'alias': $(this).find('.alias input').val(),
		    'keywords': $(this).find('.keywords input').val(),
		    'description': $(this).find('.description input').val()
		}
	    });
	    
	    $.ajax({
		'data':data,
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
	'remove': 'Pages.delete',
	'removeOk': 'Pages.dbDelete'
    });
    
    /*
    $('.symbionts-pages-admin-main.admin-list').data('admin-list', {
	'sort': 'Pages.dbSort',
	'reload': 'Pages.admin.isGlobal=false',
	'change': 'Pages.change',
	'changeLoad': function(widget){
	    
	},
	'changeOk': function(widget){
	    var change=widget;
	    var data={};
	    data['symbiont']='Pages.dbChange';
	    data['alias']=change.find('.alias input:not(.tabs .alias input)').val();
	    data['title']=change.find('.title input:not(.tabs .title input)').val();
	    data['keywords']=change.find('.keywords input:not(.tabs .keywords input)').val();
	    data['description']=change.find('.description input:not(.tabs .description input)').val();
	    data['parentId']=change.find('.parent select').val();
	    data['redirectId']=change.find('.redirect select').val();
	    data['accessLevel']=change.find('.accessLevel select').val();
	    data['template']=change.find('.templateName').val();
	    data['id']=change.find('.id:not(.tab .id)').val();
	    data['position']=change.find('.position').val();
	    data['isHome']=change.find('.isHome').hasClass('ui-state-active')?1:0;
	    data['is404']=change.find('.is404').hasClass('ui-state-active')?1:0;
	    data['isActive']=change.find('.isActive').hasClass('ui-state-active')?1:0;
	    data['isHidden']=change.find('.isHidden').hasClass('ui-state-active')?1:0;
	    data['languages']={};
	    change.find('.tab').each(function(){
		data['languages'][$(this).find('.id').val()]={
		    'title': $(this).find('.title input').val(),
		    'alias': $(this).find('.alias input').val(),
		    'keywords': $(this).find('.keywords input').val(),
		    'description': $(this).find('.description input').val()
		}
	    });
	    
	    $.ajax({
		'data':data,
		'success':function(result){
		    if(result.success!=undefined){
			widget.adminWidgetRemove();
			$('.admin-widget-main .admin-list').adminListReload();
		    }
		    else if(result.error!=undefined){
			
		    }
		}
	    });
	},
	'remove': 'Pages.delete',
	'removeOk': 'Pages.dbDelete'
    });
    $('.admin-dialog-add').hide();
    $('.admin-button-split').click(function(){
	$('.admin-dialog-add').show();
	return false;
    });
    $(document).click(function(){
	$('.admin-dialog-add').hide();
    });
    $('.admin-dialog-add .text').click(function(){
	if($('.admin-widget-addText').length){
	    $('.admin-widget').adminWidgetHide();
	    $('.admin-widget-addText').adminWidgetShow();
	}
	else{
	    $.ajax({
		'data': {
		    'symbiont': 'Pages.addText',
		    'link': $.symbiosis.link
		},
		'dataType': 'html',
		'success': function(content){
		    $('.admin-widget').adminWidgetHide();
		    var widget=$(content);
		    $('.admin-widget:last').after(widget);
		    widget.adminWidget();
		    widget.adminUI();
		    widget.find('.tabs').tabs();
		    widget.find('.admin-button-save')
		    .click(function(){
			var change=$(this).closest('.admin-widget');
			data={};
			data['languages']={};
			widget.find('.tabs .tab').each(function(){
			    var id=$(this).attr('data-id');
			    var language={};
			    language['title']=$(this).find('.title').val();
			    language['alias']=$(this).find('.alias').val();
			    var index=$(this).find('.admin-wysiwyg').attr('data-redactor');
			    language['content']=$.adminRedactors[index].getCodeEditor();
			    data['languages'][id]=language;
			});
			data['symbiont']='Pages.dbAddText';
			data['template']=change.find('.template').val();
			data['alias']=change.find('.alias:last').val();
			$.ajax({
			    'data':data,
			    'success':function(result){
				if(result.success!=undefined){
				    widget.adminWidgetRemove();
				    $('.admin-widget-main .admin-list').adminListReload();
				}
				else if(result.error!=undefined){
				    
				}
			    }
			});
		    });
		    widget.find('.admin-button-cancel')
		    .click(function(){
			$('.admin-widget-main').adminWidgetShow();
			$(this).closest('.admin-widget').adminWidgetRemove();
		    });
		    widget.find('input:first').focus();
		}
	    });
	}
    });
    $('.admin-dialog-add .blog').click(function(){
	if($('.admin-widget-addBlog').length){
	    $('.admin-widget').adminWidgetHide();
	    $('.admin-widget-addBlog').adminWidgetShow();
	}
	else{
	    $.ajax({
		'data': {
		    'symbiont': 'Pages.addBlog',
		    'link': $.symbiosis.link
		},
		'dataType': 'html',
		'success': function(content){
		    $('.admin-widget').adminWidgetHide();
		    var widget=$(content);
		    $('.admin-widget:last').after(widget);
		    widget.adminWidget();
		    widget.adminUI();
		    widget.find('.tabs').tabs();
		    widget.find('.admin-button-save')
		    .click(function(){
			var change=$(this).closest('.admin-widget');
			data={};
			data['languages']={};
			change.find('.tab').each(function(){
			    data['languages'][$(this).find('.id').val()]={
				'title': $(this).find('.title input').val(),
				'alias': $(this).find('.alias input').val()
			    }
			});
			data['alias']=change.find('.alias input:not(.tabs .alias input)').val();
			data['title']=change.find('.title input:not(.tabs .title input)').val();
			data['symbiont']='Pages.dbAddBlog';
			data['template']=change.find('.template').val();
			$.ajax({
			    'data':data,
			    'success':function(result){
				if(result.success!=undefined){
				    widget.adminWidgetRemove();
				    $('.admin-widget-main .admin-list').adminListReload();
				}
				else if(result.error!=undefined){
				    
				}
			    }
			});
		    });
		    widget.find('.admin-button-cancel')
		    .click(function(){
			$('.admin-widget-main').adminWidgetShow();
			$(this).closest('.admin-widget').adminWidgetRemove();
		    });
		    widget.find('input:first').focus();
		}
	    });
	}
    });
    //$('.symbionts-pages-admin-main.admin-list').adminList();
    */
});