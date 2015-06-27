//Admin UI 0.0.14
$(document).ready(function(){
    $('.admin-widget-icon').hover(function(){
        if(!$(this).hasClass('active')){
            $(this).find('.max').show();
        }
        $(this).addClass('hover');
    },function(){
        if(!$(this).hasClass('active')) $(this).find('.max').hide();
        $(this).removeClass('hover');
    });
    
    $('.admin-widget-block').hover(function(){
        $(this).addClass('hover');
    },function(){
        $(this).removeClass('hover');
    });
    
    $('.admin-widget').adminWidget();
    $(document).adminUI();
});
(function($){
    $.adminHash=true;
    $.fn.adminUI=function(){
	$(this).find('.admin-hover').adminHover();
	$(this).find('.admin-button').button();
	$(this).find('.admin-button-disabled').button('disable');
	$(this).find('.admin-button-add').button({icons: {primary:'ui-icon-plusthick'}});
	$(this).find('.admin-button-edit').button({icons: {primary:'ui-icon-pencil'}});
	$(this).find('.admin-button-delete').button({icons: {primary:'ui-icon-trash'}});
	$(this).find('.admin-button-cancel').button({icons: {primary:'ui-icon-closethick'}});
	$(this).find('.admin-button-config').button({icons: {primary:'ui-icon-wrench'}});
	$(this).find('.admin-button-back').button({icons: {primary:'ui-icon-arrow-1-w'}});
	$(this).find('.admin-button-save').button({icons: {primary:'ui-icon-check'}});
	$(this).find('.admin-button-split').button({icons: {primary:'ui-icon-triangle-1-s'}, text: false});
	$(this).find('.admin-button-refresh').button({icons: {primary:'ui-icon-refresh'}});
	$(this).find('.admin-button-message').button({icons: {primary:'ui-icon-comment'}});
	$(this).find('.admin-buttonset').buttonset();
	if($.fn.redactor!=undefined){
	    $(this).find('.admin-wysiwyg').redactor({
		'base': $.symbiosis.url,
		'toolbar': 'default',
		'imageUpload': 'ajax.php?symbiont=Filemanager.upload&path=redactor/&input=file&return=html&overwrite=false',
		'lang': $.symbiosis.language,
		'resize': true,
		'imageGetJson': 'files/redactor/plugins/images.php'
	    });
	}
	$(this).find('.admin-tabs').adminTabs();
	$(this).adminTranslit();
	t=$(this);
	/*
	setTimeout(function(){
	    t.find('.admin-list').adminList();
	    t.find('.admin-table').adminTable();
	    t.find('.admin-grid').adminGrid();
	}, 0);
	*/
	return this;
    };
    $.fn.adminWidget=function(isHidden){
	if(isHidden==undefined) isHidden=false;
	$(this).find('.admin-widget-header').click(function(){
	    var absconder=$(this).parent().find('.admin-widget-absconder');
	    if(absconder.css('display')!='none'){
		$(this).parent().adminWidgetHide();
	    }
	    else{
		$(this).parent().adminWidgetShow();
	    }
	});
	if(isHidden){
	    $(this).find('.admin-widget-absconder').hide();
	    $(this).css({
		'opacity': '0.2'
	    });
	}
    };
    $.fn.adminWidgetHide=function(){
	$(this).find('.admin-widget-absconder').slideUp();
	$(this).animate({
	    'opacity': '0.2'
	});
    };
    $.fn.adminWidgetShow=function(){
	$(this).find('.admin-widget-absconder').slideDown();
	$(this).animate({
	    'opacity': '1'
	});
    };
    $.fn.adminWidgetRemove=function(){
	$(this).slideUp('fast', function(){
	    $(this).remove();
	});
	
    };
    $.fn.adminPulsate=function(stop){
	var object=$(this);
	if(stop==undefined) stop=false;
	$(this).attr('stop', stop);
	if(stop) return;
	function show(){
	    object.animate({
		'opacity': 1
	    }, function(){
		if($(this).attr('stop')=='false'){
		    hide();
		}
	    });
	}
	function hide(){
	    object.animate({
		'opacity': 0.41
	    }, function(){
		show();
	    });
	}
	hide();
    };
    $.adminDialog=function(html, position){
	position=$.extend({
	    'width': 0,
	    'height': 0,
	    'x': 0,
	    'y': 0
	},position);
	var dialog=$(
	'<div class="admin-dialog">'+
	    '<div class="ui-widget-overlay"></div>'+
	    '<div class="ui-widget ui-widget-content ui-corner-all"></div>'+
	    '<div class="ui-widget-shadow ui-corner-all"></div>'+
	'</div>');
	$('body').append(dialog);
	var overly=dialog.find('.ui-widget-overlay').height($(document).height()).click(function(){
	    $(this).parent().remove();
	});
	var content=dialog.find('.ui-widget-content');
	content.append(html);
	var width=position.width?position.width:content.width();
	var height=position.height?position.height:content.height();
	var left=position.x;
	var top=position.y;
	var type='absolute';
	var horizontal=null;
	switch(left){
	    case 'left': left=0; horizontal='left'; break;
	    case 'right': left=$(window).width()-width-40; horizontal='right'; break;
	    case 'center': left=$(window).width()/2-width/2; horizontal='center'; break;
	}
	switch(top){
	    case 'top': top=0; type='fixed'; break;
	    case 'bottom': top=$(window).height()-height-40; type='fixed'; break;
	    case 'middle': top=$(window).height()/2-height/2; type='fixed'; break;
	}
	dialog.data('horizontal', horizontal);
	if(left+width>$(document).width()){
	    left=$(document).width()-width-40;
	}
	content.css({
	    'left': left+'px',
	    'top': top+'px',
	    'width': width+'px',
	    'minHeight': height+'px',
	    'position': type
	});
	var shadow=dialog.find(".ui-widget-shadow");
	shadow.css({
	    'left': content.css('left'),
	    'top': content.css('top'),
	    'width': content.width()+'px',
	    'height': content.height()+'px',
	    'position': type
	});
	return dialog;
    }
    $.fn.adminHover=function(){
        $(this).hover(function(){
	    $(this).addClass('admin-over');
	},function(){
	    $(this).removeClass('admin-over');
	});
        return this;
    };
    $.fn.adminList=function(){
	$(this).each(function(){
	    $(this).find('.admin-button-edit').button('disable');
	    $(this).find('.admin-button-delete').button('disable');
	    
	    var options=$(this).data('admin-list');
	    
	    $(this).adminEditable(options);
	    
	    if($(this).attr('data-sortable')=="false") return;
	    
	    $(this).find('ul').sortable({
		handle: '.sort',
		placeholder: 'admin-list-placeholder',
		start: function(){
		    $(this).find('ul').hide();
		    $(this).removeClass('admin-selected, admin-over');
		},
		stop: function(){
		    $(this).find('ul').show();
		},
		update: function(){
		    var sort=[];
		    $('.admin-list li').each(function(){
			var id=$(this).attr('data-id');
			if($(this).find('.id').langth) id=$(this).find('.id').html();
			sort.push(id);
		    });
		    $.ajax({
			'data':{
			    'symbiont': options.sort,
			    'sort': sort
			},
			'success':function(){
			    
			}
		    });
		}
	    });
	    
	});
	var hash=location.hash.substring(1);
	if(hash&&$.adminHash&&$('.admin-list li[data-alias='+hash+'] .edit').length){
	    $('.admin-list li[data-alias='+hash+'] .edit').click();
	    $.adminHash=false;
	}
    };
    $.fn.adminListReload=function(){
	var options=$(this).data('admin-list');
	$.ajax({
	    'data':{
		'symbiont': options.reload,
		'link': $.symbiosis.link
	    },
	    'dataType': 'html',
	    'success':function(result){
		var main=$(result);
		main.find('.admin-list').data('admin-list', options);
		var isHidden=main.find('admin-widget-absconder').css('display')=='none'?true:false;
		var oldMain=$('.admin-widget-main');
		oldMain.before(main);
		oldMain.remove();
		main.adminUI();
		main.adminWidget(isHidden);
		main.adminWidgetShow();
	    }
	});
    };
    $.fn.adminTableReload=function(){
	var options=$(this).data('admin-table');
	$.ajax({
	    'data':{
		'symbiont': options.reload,
		'link': $.symbiosis.link
	    },
	    'dataType': 'html',
	    'success':function(result){
		var main=$(result);
		main.find('.admin-table').data('admin-table', options);
		var isHidden=main.find('admin-widget-absconder').css('display')=='none'?true:false;
		var oldMain=$('.admin-widget-main');
		oldMain.before(main);
		oldMain.remove();
		main.adminUI();
		main.adminWidget(isHidden);
		main.adminWidgetShow();
	    }
	});
    };
    
    $.fn.adminButtons=function(){
        if($(this).find('.admin-selected').length){
            $(this).find('.admin-button-edit').button('enable');
            $(this).find('.admin-button-delete').button('enable');
        }
        else{
            $(this).find('.admin-button-edit').button('disable');
            $(this).find('.admin-button-delete').button('disable');
        }
    }
    $.fn.adminTable=function(){
	$(this).each(function(){
	    $(this).find('thead td, tbody td.edit, tbody td.delete').addClass('ui-state-default').hover(function(){
		$(this).addClass('ui-state-hover');
	    },function(){
		$(this).removeClass('ui-state-hover');
	    });
	    
	    $(this).find('tbody tr')
	    .adminHover()
	    .click(function(e){
		selected=$(this).hasClass('admin-selected');
		count=$(this).closest('.admin-table').find('.admin-selected').length;
		if(!e.ctrlKey){
		    $(this).closest('.admin-table').find('tr').removeClass('admin-selected');
		}
		if(selected&&(count==1||e.ctrlKey)){
		    $(this).removeClass('admin-selected');
		}
		else{
		    $(this).addClass('admin-selected');
		}
		$(this).closest('.admin-table').adminButtons();
	    })
	    .find('.ui-button').click(function(){
		return false;
	    });
	    
	    $(this).find('thead td').click(function(){
		if($(this).attr('data-sortable')=="false") return;
		AdminTableSortIndex=$(this).index();
		$(this).parent().find('td').not($(this)).removeClass('ui-state-active').find('span').remove();
		if(!$(this).find('span').length){
		    $(this).append('<span>↑</span>');
		    AdminTableSortAsc=true;
		}
		else if($(this).find('span').html()=='↑'){
		    $(this).find('span').html('↓');
		    AdminTableSortAsc=false;
		}
		else{
		    $(this).find('span').html('↑');
		    AdminTableSortAsc=true;
		}
		$(this).addClass('ui-state-active');
		tr=$(this).closest('table').find('tbody tr');
		tr.sort(AdminTableSort);
		$(this).closest('table').find('tbody').append(tr);
	    });
	    
	    $(this).find('.admin-button-edit').button('disable');
	    $(this).find('.admin-button-delete').button('disable');
	    
	    var options=$(this).data('admin-table');
	    $(this).adminEditable(options);
	});
	function AdminTableSort(i, j){
	    var first=$(i).find('td').eq(AdminTableSortIndex);
	    var second=$(j).find('td').eq(AdminTableSortIndex);
	    first=first.attr('data-sort')?first.attr('data-sort'):first.text();
	    second=second.attr('data-sort')?second.attr('data-sort'):second.text();
	    if(first>second){
		return AdminTableSortAsc?1:-1;
	    }
	    if(first<second){
		return AdminTableSortAsc?-1:1;
	    }
	    return 2;
	}
	var AdminTableSortIndex=0;
	var AdminTableSortAsc=true;
    };
     $.fn.adminGrid=function(){
	$(this).each(function(){
	    $(this).find('.admin-button-edit').button('disable');
	    $(this).find('.admin-button-delete').button('disable');
	    
	    var options=$(this).data('admin-grid');
	    
	    $(this).adminEditable(options);
	    
	    if($(this).attr('data-sortable')=="false") return;
	    $(this).find('ul').sortable({
		placeholder: 'admin-grid-placeholder',
		start: function(){
		    $(this).find('ul').hide();
		    $(this).removeClass('admin-selected, admin-over');
		},
		stop: function(){
		    $(this).find('ul').show();
		},
		update: function(){
		    var sort=[];
		    $('.admin-grid li').each(function(){
			sort.push($(this).attr('data-id'));
		    });
		    $.ajax({
			'data':{
			    'symbiont': options.sort,
			    'sort': sort
			},
			'success':function(){
			    
			}
		    });
		}
	    });
	    
	});
	var hash=location.hash.substring(1);
	$('.admin-grid li[data-alias='+hash+'] .edit').click();
    };
    $.fn.adminGridReload=function(){
	var options=$(this).data('admin-grid');
	if(typeof(options.reload)=='function'){
	    options.reload();
	}
	else{
	    $.ajax({
		'data':{
		    'symbiont': options.reload,
		    'link': $.symbiosis.link
		},
		'dataType': 'html',
		'success':function(result){
		    var main=$(result);
		    main.find('.admin-grid').data('admin-grid', options);
		    var isHidden=main.find('admin-widget-absconder').css('display')=='none'?true:false;
		    var oldMain=$('.admin-widget-main');
		    oldMain.before(main);
		    oldMain.remove();
		    main.adminUI();
		    main.adminWidget(isHidden);
		    main.adminWidgetShow();
		}
	    });
	}
    };
    $.fn.adminEditable=function(options){
	options=$.extend({
	    'sort': '',
	    'reload': '',
	    'change': '',
	    'changeOk': function(){},
	    'changeCancel': function(){},
	    'changeLoad': function(){},
	    'remove': '',
	    'removeOk': ''
	},options);
	function change(id){
		var clss='.admin-widget-';
		if(options.uniq) clss+=options.uniq+'-';
		clss+=id;
		
		if($(clss).length){
		    $('.admin-widget:not('+clss+')').adminWidgetHide();
		    $(clss).adminWidgetShow();
		}
		else{
		    $.ajax({
			'data':{
			    'symbiont': options.change,
			    'link': $.symbiosis.link,
			    'id': id
			},
			'dataType': 'html',
			'success':function(content){
			    $('.admin-widget').adminWidgetHide();
			    var widget=$(content);
			    $('.admin-widget:last').after(widget);
			    widget.adminWidget();
			    widget.adminUI();
			    widget.find('.admin-button-save')
			    .click(function(){
				options.changeOk($(this).closest('.admin-widget'));
			    });
			    widget.find('.admin-button-cancel')
			    .click(function(){
				$('.admin-widget-main').adminWidgetShow();
				$(this).closest('.admin-widget').adminWidgetRemove();
				options.changeCancel($(this).closest('.admin-widget'));
			    });
			    if(options.changeLoad!=undefined) options.changeLoad(widget);
			    widget.find('input:first').focus();
			}
		    });
		}
	    }
	    function remove(id, position){
		var clss='.admin-widget-';
		if(options.uniq) clss+=options.uniq+'-';
		clss+='main';
		
		if(typeof(id)=='object'){
		    current=$();
		    for(key in id){
			val=id[key];
			current=current.add(clss+' [data-id='+val+']');
		    }
		}
		else{
		    current=$(clss+' [data-id='+id+']');
		}
		$.ajax({
		    'data':{
			'symbiont': options.remove,
			'id': id
		    },
		    'dataType': 'html',
		    'success':function(result){
			div=$(result);
			div.find('.yes').button().click(function(){
			    $.ajax({
				'data':{
				    'symbiont': options.removeOk,
				    'id': id
				},
				'success':function(result){
				    current.removeClass('admin-selected');
				    if(current.closest('.admin-grid').length){
					current.remove();
				    }
				    else{
					current.slideUp('fast', function(){
					    $(this).remove();
					});
				    }
				    $('.admin-dialog-delete').remove();
				    $('.admin-widget-main').adminButtons();
				}
			    });
			});
			div.find('.no').button().click(function(){
			    $('.admin-dialog-delete').remove();
			});
			dialog=$.adminDialog(div, position);
			dialog.addClass('admin-dialog-delete');
		    }
		});
	    }
	    $(this).find('.edit').click(function(){
		var id=$(this).closest('li, tr').attr('data-id');
		change(id);
	    });
	    $(this).find('.admin-button-add').click(function(){
		change(0);
	    });
	    $(this).find('.delete').click(function(event){
		var id=$(this).closest('li, tr').attr('data-id');
		remove(id, {x: event.pageX, y: event.pageY});
	    });
	    $(this).find('.admin-button-edit').click(function(){
		$('.admin-widget-main [data-id].admin-selected').each(function(){
		    var id=$(this).attr('data-id');
		    change(id);
		});
	    });
	    $(this).find('.admin-button-delete').click(function(event){
		ids=[];
		$('.admin-widget-main .admin-selected').each(function(){
		    ids.push($(this).attr('data-id'));
		});
		remove(ids, {x: event.pageX, y: event.pageY});
	    });
	    $(this).bind('change', function(e, id){
		change(id);
	    });
	    
	    //For list
	    $(this).find('li')
	    .addClass('ui-corner-all')
	    .adminHover()
	    .click(function(e){
		selected=$(this).hasClass('admin-selected');
		count=$(this).closest('.admin-list,.admin-grid').find('li.admin-selected').length;
		if(!e.ctrlKey){
		    $(this).closest('.admin-list,.admin-grid').find('li').removeClass('admin-selected');
		}
		if(selected&&(count==1||e.ctrlKey)){
		    $(this).removeClass('admin-selected');
		}
		else{
		    $(this).addClass('admin-selected');
		}
		$(this).closest('.admin-list,.admin-grid').adminButtons();
		return false;
	    })
	    .find('.ui-button').click(function(){
		return false;
	    });
	    
	    $(this).find('ul li a').click(function(){
		window.location.replace($(this).attr('href'));
		return false;
	    });
    };
    $.fn.adminTabs=function(){
	$(this).each(function(){
	    $(this).find('.admin-tabs-nav a').click(function(){
		$(this).closest('.admin-tabs-nav').find('li').removeClass('current');
		$(this).parent().addClass('current');
		var hash=$(this).attr('href');
		hash=$.symbiosisHash(hash.substr(hash.indexOf('#')+1));
		$(this).closest('.admin-tabs').find('.admin-tab').hide();
		$(this).closest('.admin-tabs').find('.admin-tab[data-tab='+hash['tab']+']').show();
	    });
	    $(this).find('.admin-tab').hide();
	    
	    if($.symbiosis.hash['tab']==undefined){
		$(this).find('.admin-tabs-nav a:first').click();
	    }
	    else{
		$(this).find('.admin-tabs-nav li[data-tab='+$.symbiosis.hash['tab']+'] a').click();
	    }
	});
    };
    $(window).bind('hashchange', function() {
        if($.symbiosis.hash['tab']==undefined){
	    $(this).find('.admin-tabs-nav a:first').click();
	}
	else{
	    $(this).find('.admin-tabs-nav li[data-tab='+$.symbiosis.hash['tab']+'] a').click();
	}
    });
    $.fn.adminTranslit=function(){
	var source=$(this).find('.admin-source');
	source.keyup(function(){
	    var index=$(this).attr('admin-translit');
	    var receiver=$('.admin-receiver-'+index);
	    text=$(this).val().toLowerCase();
	    res='';
	    translit=$.adminTranslit[index];
	    if(translit==undefined) res=text;
	    for(key in text){
		val=text[key];
		if(translit[val]!=undefined){
		    res+=translit[val];
		}
	    }
	    receiver.val(res);
	});
    }
    $.adminTranslit={};
    $(window).resize(function(){
	$('.admin-dialog').each(function(){
	    var horizontal=$(this).data('horizontal');
	    if(horizontal==null) return;
	    var content=$(this).find('.ui-widget-content');
	    var width=content.width();
	    var left=0;
	    switch(horizontal){
		case 'left': left=0; break;
		case 'right': left=$(document).width()-width-40; break;
		case 'center': left=$(document).width()/2-width/2; break;
	    }
	    content.css({
		'left': left+'px'
	    });
	    var shadow=$(this).find(".ui-widget-shadow");
	    shadow.css({
		'left': content.css('left')
	    });
	});
    });
})(jQuery);