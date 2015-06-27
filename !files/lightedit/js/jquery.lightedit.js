/*
 * LightEdit 0.1.11
 * lightedit.omegascorp.net
 */
(function ($) {
    //jQuery initialisation
    $.fn.lightedit=function(options){
	var lightedits=[];
        $(this).each(function(){
	    if($(this).data('lightedit')==undefined){
		lightedits.push(new Lightedit($(this), options));
	    }
	    else{
		lightedits.push($(this).data('lightedit'));
	    }
        });
	return lightedits;
    };
    $.fn.lighteditGet=function(){
	return $(this).eq(0).data('lightedit').get();
    }
    $.fn.lighteditSet=function(values){
	return $(this).eq(0).data('lightedit').set(values);
    }
    $.lightedit=function(){
	return $('.lightedit.lightedit-current').data('lightedit');
    }
    var LighteditFunctions={
	//Full screen
	'full': function(element){
	    if(element.hasClass('lightedit-active')){
		$('html,body').css('overflow','auto');
		element.removeClass('lightedit-active');
		this.element.removeClass('lightedit-full');
		this.element.css({
		    'left': 'auto',
		    'top': 'auto',
		    'height': 'auto',
		    'width': 'auto'
		});
		if(this.scroll!=undefined){
		    $(window).scrollLeft(this.scroll[0]);
		    $(window).scrollTop(this.scroll[1]);
		}
	    }
	    else{
		this.scroll=[$(window).scrollLeft(), $(window).scrollTop()];
		$('html,body').css('overflow','hidden');
		element.addClass('lightedit-active');
		this.element.addClass('lightedit-full');
		outerVertical=parseInt(this.element.css('padding-top'))+parseInt(this.element.css('padding-bottom'));
		outerHorizontal=parseInt(this.element.css('padding-left'))+parseInt(this.element.css('padding-right'));
		this.element.css({
		    'left': (-(this.offsetParent(this.element).left))+'px',
		    'top': (-(this.offsetParent(this.element).top))+'px',
		    'width': ($(window).width()-outerHorizontal)+'px',
		    'height': ($(window).height()-outerVertical)+'px'
		});
		$(window).scrollLeft(0);
		$(window).scrollTop(0);
	    }
	    this.updateToolbar();
	},
	//View source
	'html': function(element){
	    if(element.hasClass('lightedit-active')){
		element.removeClass('lightedit-active');
		this.element.find('.lightedit-text').each(function(){
		    $(this).find('iframe').show();
		    $(this).find('textarea').hide();
		});
		this.updateHtml();
		//this.current.historySave();
	    }
	    else{
		element.addClass('lightedit-active');
		this.element.find('.lightedit-text').each(function(){
		    $(this).find('iframe').hide();
		    $(this).find('textarea').show().height($(this).find('iframe').height());
		});
		this.updateText();
	    }
	    $('.lightedit').each(function(){
		$(this).data('lightedit').updateToolbar();
	    });
	},
	//Insert table row to top
	'table-insert-row-top': function(element){
	    var length=this.current.doc.find('table td.lightedit-active').parent().find('td').length;
	    var tr=$('<tr></tr>');
	    for(i=0; i<length; i++){
		tr.append('<td><br/></td>');
	    }
	    this.current.doc.find('table td.lightedit-active').parent().before(tr);
	    this.current.update();
	    this.current.updateHeight();
	    this.current.historySave();
	},
	//Insert table row to bottom
	'table-insert-row-bottom': function(element){
	    var length=this.current.doc.find('table td.lightedit-active').parent().find('td').length;
	    var tr=$('<tr></tr>');
	    for(i=0; i<length; i++){
		tr.append('<td><br/></td>');
	    }
	    this.current.doc.find('table td.lightedit-active').parent().after(tr);
	    this.current.update();
	    this.current.updateHeight();
	    this.current.historySave();
	},
	//Insert table column to left
	'table-insert-col-left': function(element){
	    var index=this.current.doc.find('table td.active').index();
	    this.current.doc.find('table td.active').closest('table').find('tr').each(function(){
		$(this).find('td').eq(index).before('<td><br/></td>');
	    });
	    this.current.update();
	    this.current.updateHeight();
	    this.current.historySave();
	},
	//Insert table column to right
	'table-insert-col-right': function(element){
	    var index=this.current.doc.find('table td.lightedit-active').index();
	    this.current.doc.find('table td.lightedit-active').closest('table').find('tr').each(function(){
		$(this).find('td').eq(index).after('<td><br/></td>');
	    });
	    this.current.update();
	    this.current.updateHeight();
	    this.current.historySave();
	},
	//Delete table row
	'table-delete-row': function(element, lightedit){
	    this.current.doc.find('table td.lightedit-active').parent().remove();
	    this.current.update();
	    this.current.updateHeight();
	    this.current.historySave();
	},
	//Delete table column
	'table-delete-col': function(element, lightedit){
	    var index=this.current.doc.find('table td.lightedit-active').index();
	    this.current.doc.find('table td.lightedit-active').closest('table').find('tr').each(function(){
		$(this).find('td').eq(index).remove();
	    });
	    this.current.update();
	    this.current.updateHeight();
	    this.current.historySave();
	},
	//Delete table
	'table-delete': function(element, lightedit){
	    var index=this.current.doc.find('table td.lightedit-active').closest('table').remove();
	    this.current.update();
	    this.current.updateHeight();
	    this.current.historySave();
	}
    }
    //Initialisation
    var Lightedit = function(element, options){
	this.element=element;
	this.options=options=$.extend({
	    'base': '',
            'path':'',
            'toolbar':'default',
	    'styles': 'default',
	    'language': 'en',
	    'height': element.height()?element.height():100,
	    'formating': 'formating.js',
	    'plugin-image': {
		'upload': 'files/lightedit/plugins/upload.php',
		'images': 'files/lightedit/plugins/images.php'
	    },
	    'top-var': '',
	    'top': 0,
	    'save': function(){
		setTimeout($.proxy(function(){
		    this.element.find('.lightedit-saved').fadeOut();
		}, this), 500);
		return true;
	    },
	    'autosave': 0,
	    'hidden': false
        },options);
	if($('base').length&&!this.options.base){
	    this.options.base=$('base').attr('href');
	}
	if(options.path==''){
	    $('script').each(function(){
		var src=$(this).attr('src');
		if(src){
		    var regexp = new RegExp(/js\/jquery.lightedit(\.min)?\.js(\?.*)?/);
		    if(src.match(regexp)) options.path = src.replace(regexp, '');
		}
	    });
	}
	this.language=[];
	this.toolbar=[];
	this.styles=[];
	this.current=null;
	this.data={};
	this.top=0;
	this.init();
    };
    //Methods
    Lightedit.prototype = {
	//Init lightedit
	init: function(){
	    var count=0;
	    $('.lightedit').each(function(){
		if($(this).data('lightedit')!=undefined){
		    count++;
		}
	    });
	    if(count==0) this.element.addClass('lightedit-current');
	    this.element.addClass('lightedit');
	    this.element.data('lightedit', this);
	    
	    //Init text
	    this.element.find('textarea').each($.proxy(function(i, element){
		new LighteditText(this, $(element), i==0?true:false);
	    }, this));
	    
	    //Init tabs
	    if(this.element.find('.lightedit-tabs').length){
		this.element.find('.lightedit-tab').hide().first().show();
		this.element.find('.lightedit-tabs').after('<div class="lightedit-nav"><ul></ul></div>');
		this.element.find('.lightedit-tab').each($.proxy(function(i, element){
		    this.element.find('.lightedit-nav ul').append('<li><a href="">'+$(element).attr('data-title')+'</a></li>');
		},this));
		this.element.find('.lightedit-nav li:first').addClass('lightedit-current');
		this.element.find('.lightedit-nav a').click($.proxy(function(event){
		    var element=$(event.currentTarget);
		    var index=element.parent().index();
		    this.element.find('.lightedit-tab').hide().find('.lightedit-text.lightedit-current').removeClass('lightedit-current');
		    this.element.find('.lightedit-tab').eq(index).show().find('.lightedit-text:first').addClass('lightedit-current');
		    this.element.find('.lightedit-nav li').removeClass('lightedit-current');
		    element.parent().addClass('lightedit-current');
		    $('.lightedit').each(function(){
			var lightedit=$(this).data('lightedit');
			lightedit.updateToolbar();
		    });
		    
		    return false;
		}, this));
	    }
	    else{
		this.element.append('<div class="lightedit-nav"></div>');
	    }
	    this.element.find('.lightedit-nav').append('<div class="lightedit-saved" style="display:none;">Saved</div>');
	    
	    //Init inputs
	    this.element.find('input[type=text]').wrap('<div class="lightedit-input-container">');
	    this.element.find('input[type=text]').wrap('<div class="lightedit-input">');
	    this.element.find('.lightedit-input-container').each(function(){
		var title=$(this).find('input').attr('title');
		$(this).prepend('<label>'+title+'</label>');
	    });
	    
	    //Init selects
	    this.element.find('select').wrap('<div class="lightedit-select-container">');
	    this.element.find('select').wrap('<div class="lightedit-select">');
	    this.element.find('.lightedit-select-container').each(function(){
		var title=$(this).find('select').attr('title');
		$(this).prepend('<label>'+title+'</label>');
	    });
	    
	    //Init dialogs and dropdowns
	    this.element.append($('<div class="lightedit-dropdown" style="display: none;"></div>'));
	    this.element.append($('<div class="lightedit-overlay" style="display: none;"></div>'));
	    this.element.append($('<div class="lightedit-dialog" style="display: none;"></div>'));
	    this.element.find('.lightedit-overlay').click($.proxy(function(){
		this.dialogClose();
	    },this));
	    
	    //Load files
	    this.load([
		this.options.path+'languages/'+this.options.language+'.json',
		this.options.path+'toolbars/'+this.options.toolbar+'.json',
		this.options.path+'styles/'+this.options.styles+'.json'
	    ],[
		"language",
		"toolbar",
		"styles"
	    ], function(){
		//Init toolbar
		var toolbar=$('<div class="lightedit-toolbar"><ul></ul></div>');
		for(key in this.toolbar){
		    var val=this.toolbar[key];
		    var tool=$('<li><a href="" data-title="'+this.language[val.title]+'"></a></li>');
		    tool.addClass('lightedit-tool-'+val.title);
		    if(val.separator==true){
			tool.addClass("lightedit-separator");
		    }
		    if(val.screen!=undefined){
			tool.addClass('lightedit-screen-'+val.screen);
		    }
		    if(val.align!=undefined){
			tool.addClass('lightedit-align-'+val.align);
		    }
		    if(val.exec){
			tool.find('a').data('exec', val.exec);
			tool.find('a').data('param', val.param!=undefined?val.param:null);
		    }
		    else if(val.func!=undefined){
			tool.find('a').data('function', val.func);
		    }
		    else if(val.dropdown){
			dropdown=$('<ul></ul>');
			for(k in val.dropdown){
			    v=val.dropdown[k];
			    item=$('<li><a href=""></a></li>');
			    item.addClass('lightedit-tool-'+v.title);
			    if(v.exec!=undefined){
				item.find('a').html(this.language[v.title]);
				item.find('a').data('exec', v.exec);
				item.find('a').data('param', v.param!=undefined?v.param:null);
			    }
			    else if(v['class']!=undefined){
				item.find('a').html(v.title);
				item.find('a').data('exec', 'class');
				item.find('a').data('param', v['class']);
			    }
			    else if(v.func!=undefined){
				item.find('a').html(this.language[v.title]);
				item.find('a').data('function', v.func);
			    }
			    else if(v.plugin!=undefined){
				item.find('a').html(this.language[v.title]);
				item.find('a').data('plugin', v.plugin);
			    }
			    if(v.separator==true){
				item.addClass("lightedit-separator");
			    }
			    dropdown.append(item);
			}
			dropdown.hide();
			tool.append(dropdown);
		    }
		    else if(val.plugin){
			tool.find('a').data('plugin', val.plugin);
		    }
		    tool.children('a').hover(function(){
			var lightedit=$(this).closest('.lightedit').data('lightedit');
			var popup=$(this).closest('.lightedit').find('.lightedit-toolbar-popup');
			popup.find('.lightedit-toolbar-popup-content').html($(this).attr('data-title'));
			var offset=$(this).offset();
			var offParent=lightedit.offsetParent($(this).closest('.lightedit'));
			var left=offset.left-popup.width()/2+$(this).width()/2;
			if(left<$(window).scrollLeft()){
			    popup.find('.lightedit-toolbar-popup-tail').css('background-position', (offset.left+5)+'px -8px');
			    popup.find('.lightedit-toolbar-popup-tailtop').css('background-position', (offset.left+5)+'px 0');
			    left=5;
			}
			else if(left+popup.width()>$(window).scrollLeft()+$(document).width()){
			    left=$(window).scrollLeft()+$(document).width()-popup.width()-5;
			    popup.find('.lightedit-toolbar-popup-tail').css('background-position', (offset.left-left+8)+'px -8px');
			    popup.find('.lightedit-toolbar-popup-tailtop').css('background-position', (offset.left-left+8)+'px 0');
			}
			var top=0;
			if(offset.top>$(window).scrollTop()+lightedit.options.top+popup.height()){
			    popup.find('.lightedit-toolbar-popup-tailtop').hide();
			    popup.find('.lightedit-toolbar-popup-tail').show();
			    top=offset.top-popup.height();
			}
			else{
			    popup.find('.lightedit-toolbar-popup-tail').hide();
			    popup.find('.lightedit-toolbar-popup-tailtop').show();
			    top=offset.top+$(this).height();
			}	
			popup.css({
			    'left': left-offParent.left+'px',
			    'top':  top-offParent.top+'px'
			}).show();
			
		    },function(){
			var popup=$(this).closest('.lightedit').find('.lightedit-toolbar-popup');
			popup.hide();
			popup.find('.lightedit-toolbar-popup-tail').css('background-position', '50% -8px');
			popup.find('.lightedit-toolbar-popup-tailtop').css('background-position', '50% 0');
		    });
		    tool.find('a').click(function(){
			$('.lightedit-dropdown').hide();
			var $$=$(this).closest('.lightedit').data('lightedit');
			var text=$(this).closest('.lightedit').find('.lightedit-text.lightedit-current').data('lightedit-text');
			$('.lightedit').removeClass('lightedit-current');
			$$.element.addClass('lightedit-current');
			if(text!=undefined){
			    if($(this).data('exec')!=undefined){
				text.exec($(this).data('exec'), $(this).data('param'));
			    }
			    else if($(this).data('plugin')!=undefined){
				var plugin=$(this).data('plugin');
				text.plugin(plugin);
			    }
			    else if($(this).data('function')!=undefined){
				var func=$(this).data('function');
				text.func(func, this);
			    }
			    else if($(this).parent().find('ul').length){
				$$.element.find('.lightedit-dropdown').html('').css({
				    'left': $(this).offset().left-$$.offsetParent($$.element).left,
				    'top': $(this).offset().top-$$.offsetParent($$.element).top
				});
				$$.element.find('.lightedit-dropdown').append($(this).parent().find('ul').clone(true).show()).show();
			    }
			}
			return false;
		    });
		    toolbar.children('ul').append(tool);
		}
		
		var items=$('<ul></ul>');
		for(key in this.styles['classes']){
		    val=this.styles['classes'][key];
		    var item=$('<li class="'+val+'"><a href="">'+val+'</a></li>');
		    item.click($.proxy(function(val){
			$('.lightedit-dropdown').hide();
			this.current.exec('class', val);
			return false;
		    }, this, val));
		    items.append(item);
		}
		item=$('<li><a href="">'+this.language['class-remove']+'</a></li>');
		item.click($.proxy(function(val){
		    $('.lightedit-dropdown').hide();
		    $.proxy(this.current.exec, this.current, 'removeClass')();
		    return false;
		}, this, val));
		items.append(item);
		toolbar.find('.lightedit-tool-class').append(items);
		this.element.prepend(toolbar);
		toolbar.after('<div class="lightedit-toolbar-left"></div>');
		toolbar.after('<div class="lightedit-toolbar-right"></div>');
		toolbar.after('<div class="lightedit-toolbar-popup" style="display:none;"><div class="lightedit-toolbar-popup-tailtop"></div><div class="lightedit-toolbar-popup-content"></div><div class="lightedit-toolbar-popup-tail"></div></div>');
		this.updateToolbar();
		
		this.element.find('.lightedit-toolbar-left,.lightedit-toolbar-right').hide();
		this.element.find('.lightedit-toolbar').mousemove(function(e){
		    var $$=$(this).closest('.lightedit').data('lightedit');
		    var x=e.pageX - $(this).offset().left;
		    var width=$(this).width();
		    if(x<50&&$(this).scrollLeft()!=0){
			$$.element.find('.lightedit-toolbar-left').show().css('opacity', (50-x)*0.1);
		    }
		    else{
			$$.element.find('.lightedit-toolbar-left').hide();
		    }
		    if(width-x<50&&$(this).scrollLeft()+width<$(this).children('ul').width()){
			$$.element.find('.lightedit-toolbar-right').show().css('opacity', (50-width+x)*0.1);
		    }
		    else{
			$$.element.find('.lightedit-toolbar-right').hide();
		    }
		});
		this.element.find('.lightedit-toolbar-left,.lightedit-toolbar-right').hover(function(){
		    $(this).addClass('lightedit-over');
		},function(){
		    $(this).removeClass('lightedit-over')
		});
		if(this.options.hidden) this.hide();
	    });
	    this.dialogUpdate();
	    this.element.scroll($.proxy(function(){
		this.updateToolbar();
		this.updateDialog();
		$('.lightedit-dropdown').hide();
	    }, this));
	    setInterval($.proxy(function(){
		if(this.top!=this.element.offset().top){
		    this.updateToolbar();
		    this.top=this.element.offset().top;
		}
	    },this), 50);
	    if(this.options.autosave){
		setInterval($.proxy(this.options.save, this, this.get()), this.options.autosave);
	    }
	},
	//Load files
	load: function(files, vars, func){
	    if(!files.length) $.proxy(func, this)();
	    else $.get(files.shift(), $.proxy(function(r){
		this[0][vars.shift()]=r;
		$.proxy(this[0].load, this[0], this[1], this[2], this[3])();
	    }, [this, files, vars, func]));
	    return;
	},
	//Update html for all editors
	updateHtml: function(){
	    this.element.find('.lightedit-text').each(function(){
		$(this).data('lightedit-text').updateHtml();
	    });
	},
	//Update text for all editors
	updateText: function(){	    
	    this.element.find('.lightedit-text').each(function(){
		$(this).data('lightedit-text').updateText();
	    });
	},
	//Update toolbar
	updateToolbar: function(){
	    if(this.options["top-var"]){
		eval('this.options.top='+this.options["top-var"]);
	    }
	    var outer=this.element.find('.lightedit-toolbar').outerWidth()-this.element.find('.lightedit-toolbar').width();
	    var full=this.element.hasClass('full');
	    var top=full?this.element.offset.top():$(window).scrollTop();
	    
	    var position=this.element.offset();
	    var offsetParent=this.offsetParent(this.element);
	    
	    var width=this.element.find('.lightedit-tabs').width();
	    if(!width) width=this.element.find('iframe:first').width();
	    var textTop=this.element.find('.lightedit-text.lightedit-current').offset().top;
	    if(full) textTop+=top;
	    var textHeight=this.element.find('.lightedit-text.lightedit-current').height();
	    
	    this.element.find('.lightedit-toolbar').width(width-outer);
	    
	    if(textTop-this.options.top>top){
		position=textTop;
	    }
	    else if(top<textHeight+textTop){
		position=top+this.options.top;
	    }
	    else{
		position=textHeight+textTop;
	    }
	    position-=offsetParent.top;
	    this.element.find('.lightedit-toolbar').css('top', position+'px');
	    
	    var toolbarWidth=this.element.find('.lightedit-toolbar').width();
	    var toolsWidth=0;
	    this.element.find('.lightedit-toolbar>ul>li').each(function(){
		if($(this).css('display')=='none') return;
		toolsWidth+=$(this).outerWidth();
	    });
	    this.element.find('.lightedit-toolbar>ul').width(toolsWidth);
	    this.element.find('.lightedit-toolbar-left').css({
		'top': position+'px'
	    });
	    this.element.find('.lightedit-toolbar-right').css({
		'top': position+'px',
		'margin-left': (toolbarWidth-16)+'px'
	    });
	    if(toolbarWidth<toolsWidth){
		this.element.find('.lightedit-toolbar').addClass('scrollable');
	    }
	    else{
		this.element.find('.lightedit-toolbar').removeClass('scrollable');
	    }
	},
	dialogUpdate: function(){
	    var h1=$(document).height();
	    var h2=$('body').height();
	    this.element.find('.lightedit-overlay').height(Math.max(h1, h2));
	    
	    var width=$(window).width();
	    var height=$(window).height();
	    var top=$(window).scrollTop();
	    
	    var offParent=this.offsetParent(this.element);
	    
	    var dialogWidth=this.element.find('.lightedit-dialog').outerWidth();
	    var dialogHeight=this.element.find('.lightedit-dialog').outerHeight();
	    
	    this.element.find('.lightedit-dialog').css({
		'left': (width/2-dialogWidth/2-offParent.left)+'px',
		'top': (top+height/2-dialogHeight/2-offParent.top)+'px'
	    });
	},
	dialog: function(settings){
	    if(settings==undefined) settings={};
	    settings=$.extend({
		'width':400,
		'height':340,
		'ajax': '',
		'data': {},
		'props': {}
	    },settings);
	    this.element.find('.lightedit-dialog').width(settings.width).height(settings.height);
	    var width=$(window).width();
	    var height=$(window).height();
	    var dialogWidth=this.element.find('.lightedit-dialog').outerWidth();
	    var dialogHeight=this.element.find('.lightedit-dialog').outerHeight();
	    var top=$(window).scrollTop();
	    
	    this.element.find('.lightedit-dialog').css({
		'left': (width/2-dialogWidth/2)+'px',
		'top': (top+height/2-dialogHeight/2)+'px'
	    });
	    
	    this.element.find('.lightedit-dialog').show();
	    this.dialogUpdate();
	    this.element.find('.lightedit-overlay').fadeIn('fast');
	    if(settings.ajax){
		this.element.find('.lightedit-dialog').addClass('loading');
		$.ajax({
		    'url':settings.ajax,
		    'data':settings.data,
		    'type':'POST',
		    'dataType':'html',
		    'success': $.proxy(function(result){
			this[0].data=this[1];
			var matches=result.match(/\{[a-zA-Z\-]*\}/g);
			if(matches!=null){
			    for(key in matches){
				val=matches[key];
				label=val.substr(1, val.length-2);
				result=result.replace(new RegExp(val, "g"), this[0].language[label]);
			    }
			}
			this[0].element.find('.lightedit-dialog').removeClass('loading');
			this[0].element.find('.lightedit-dialog').html(result);
		    },[this, settings.props])
		});
	    }
	},
	dialogClose: function(){
	    this.element.find('.lightedit-overlay').fadeOut('fast');
	    this.element.find('.lightedit-dialog').hide().removeClass('loading').html('');
	},
	exec: function(exec, param){
	    this.current.exec(exec, param);
	},
	insert: function(text){
	    this.current.exec('inserthtml', text);
	},
	get: function(){
	    this.updateText();
	    var result={};
	    if(this.element.find('.lightedit-tab').length){
		result['languages']={};
		this.element.find('.lightedit-tab').each(function(){
		    var langauge={};
		    $(this).find('input,textarea,select').each(function(){
			langauge[$(this).attr('name')]=$(this).val();
		    });
		    result['languages'][$(this).attr('data-lang')]=langauge;
		});
	    }
	    this.element.find('input,textarea,select').each(function(){
		if($(this).closest('.lightedit-tab').length==0){
		    result[$(this).attr('name')]=$(this).val();
		}
	    });
	    return result;
	},
	set: function(values){
	    for(key in values){
		value=values[key];
		if(key=='languages'){
		    continue;
		}
		this.element.find('[name="'+key+'"]').val(value);
	    }
	    if(values['languages']!=undefined){
		for(key in values['languages']){
		    value=values['languages'][key];
		    var tab=this.element.find('.lightedit-tab[data-lang="'+key+'"]');
		    for(k in value){
			v=value[k];
			tab.find('[name="'+k+'"]').val(v);
		    }
		}
	    }
	    this.updateHtml();
	},
	hide: function(){
	    this.element.find('.lightedit-toolbar').hide();
	    this.options.hidden=true;
	},
	show: function(){
	    this.element.find('.lightedit-toolbar').show();
	    this.options.hidden=false;
	},
	offsetParent: function(elem){
	    var top=0, left=0;
	    elem=elem.offsetParent();
	    while(elem.get(0).tagName!='BODY'){
		top = top + parseFloat(elem.offset().top)
		left = left + parseFloat(elem.offset().left)
		elem = elem.offsetParent();
	    }
	    return {top: Math.round(top), left: Math.round(left)}
	}
    };
    
    //Editor
    var LighteditText = function(parent, element, focus){
	this.parent=parent;
	this.element=element;
	this.history=[];
	this.index=-1;
	this.iframe=null;
	this.doc=null;
	this.textarea=null;
	
	this.init(focus);
    };
    LighteditText.prototype = {
	//Init an editor
	init: function(focus){
	    this.element.wrap('<div class="lightedit-text">');
	    this.textarea=this.element;
	    this.element=this.element.parent();
	    this.element.data('lightedit-text', this);
	    var options=this.parent.options;
	    this.iframe=$('<iframe src="javascript:false;" frameborder="0" style="width:100%;"></iframe>');
	    this.textarea.after(this.iframe);
	    this.textarea.hide();
	    this.doc=$(this.iframe.get(0).contentDocument || this.iframe.get(0).contentWindow.document);
	    this.win=$(this.iframe.get(0).contentWindow || this.iframe.get(0).contentDocument.defaultView);
	    
	    var html='<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><base href="'+options.base+'" /><link rel="stylesheet" href="'+options.path+'css/formating.css" type="text/css" media="screen" />STYLE_SHEET</head><body>INITIAL_CONTENT</body></html>';
	    var style="<style>body,html{overflow:hidden;}body:after{content: '.';display: block;clear: both;visibility: hidden;height: 0;}</style>";
	    var doc=this.doc.get(0);
	    
	    doc.open();
	    doc.write(html
			.replace(/INITIAL_CONTENT/, function() { return ' '; })
			.replace(/STYLE_SHEET/, function() { return style; })
			);
	    doc.close();
	    doc.designMode='on';
	    
	    this.win.bind("paste", $.proxy(function(e){
		// cancel paste
		var original=e.originalEvent;
		original.preventDefault();
		// get text representation of clipboard
		var text = original.clipboardData.getData("text/plain");
		text=text.replace(/\n\r/g, "\n");
		text=text.replace(/\r/g, "\n");
		if(text.match(/\r/)!=null) text="<p>"+text+"</p>";
		
		text=text.replace(/\n(\s*)\n/g, "</p><p>");
		text=text.replace(/\n/g, "<br/>");
		
		
		// insert text manually
		this.exec("insertHTML", text);
	    },this));
	    
	    this.doc.click($.proxy(function(){
		var parent=this.parent.element;
		parent.find('.lightedit-dropdown').hide();
		parent.find('.lightedit-text').removeClass('lightedit-current');
		this.element.addClass('lightedit-current');
		this.parent.current=this;
		$('.lightedit').removeClass('lightedit-current');
		this.parent.element.addClass('lightedit-current');
		this.parent.updateToolbar();
	    }, this));
	    this.doc.keyup($.proxy(function(e){
		if(e.keyCode==8){
		    var html=$(this).find('body').html();
		    if(html=='<br>'||html=='<br/>'||html=='<br />'||html==''||html==' '){
			this.execCommand("formatblock", false, "<p>");
		    }
		    this.historySave();
		}
		else if(e.keyCode==32){
		    this.historySave();
		}
		this.updateHeight();
	    }, this));
	    this.doc.keydown($.proxy(function(e){
		if(e.ctrlKey&&e.keyCode==90){
		    //this.historyPrev()
		    //return false;
		}
		else if(e.ctrlKey&&e.keyCode==89){
		    //this.historyNext();
		    //return false;
		}
		else if(e.ctrlKey&&e.keyCode==83){
		    if($.proxy(this.parent.options.save, this.parent, this.parent.get())()){
			this.parent.element.find('.lightedit-saved').fadeIn();
		    }
		    return false;
		}
		return true;
	    }, this));
	    
	    if(focus){
		this.element.addClass('lightedit-current');
		this.parent.current=this;
		this.iframe.focus();
	    }
	    
	    this.index=-1;
	    this.updateHtml();
	    this.selectionToEnd();
	    //this.history.push([this.textarea.val(),this.selectionGet()]);
	    this.historySave();
	    
	},
	update: function(){
	    this.doc.find('img').unbind('click').click($.proxy(function(e){
		var element=$(e.currentTarget);
		this.parent.dialog({
		    'ajax': this.parent.options.path+'plugins/image-edit.php',
		    'props': {
			'image': element
		    }
		});
		return false;
	    }, this));
	    this.doc.find('a').unbind('click').click($.proxy(function(e){
		var element=$(e.currentTarget);
		this.parent.dialog({
		    'ajax': this.parent.options.path+'plugins/link.php',
		    'props': {
			'link': element
		    }
		});
		return false;
	    },this));
	    this.doc.find('table').unbind('hover').hover(function(){
		$(this).addClass('lightedit-over');
	    },function(){
		$(this).removeClass('lightedit-over');
	    }).find('td').unbind('click').click(function(){
		$(this).closest('body').find('td.lightedit-active,table.lightedit-active').removeClass('lightedit-active');
		$(this).closest('table').addClass('lightedit-active');
		$(this).addClass('lightedit-active');
		return false;
	    });
	    this.doc.find('span').unbind('click').click(function(){
		$(this).closest('body').find('span.lightedit-active').removeClass('lightedit-active');
		$(this).addClass('lightedit-active');
		return false;
	    });
	    this.doc.find('.grid-col').unbind('hover').hover(function(){
		$(this).addClass('lightedit-over');
	    },function(){
		$(this).removeClass('lightedit-over');
	    }).unbind('click').click(function(){
		$(this).closest('body').find('.grid-col').removeClass('lightedit-active');
		$(this).addClass('lightedit-active');
		return false;
	    });
	    this.doc.click(function(){
		$(this).find('td.lightedit-active,table.lightedit-active,.grid-col.lightedit-active,span.lightedit-active').removeClass('lightedit-active');
	    });
	},
	//Update html
	updateHtml: function(){
	    var val=this.element.find('textarea').val();
	    if(val==''||val=='<br>'||val=='<br/>') val='<p><br/></p>';
	    $(this.element.find('iframe').get(0).contentWindow.document).find('body').html(val);
	    this.update();
	    this.updateHeight();
	},
	//Update text
	updateText: function(){
	    this.element.find('textarea').val($(this.element.find('iframe').get(0).contentWindow.document).find('body').html());
	},
	updateHeight: function(){
	    var bodyHeight=this.doc.find('body').outerHeight()+20;
	    this.iframe.height(bodyHeight>this.parent.options.height?bodyHeight:this.parent.options.height);
	    $('.lightedit').each(function(){
		$(this).data('lightedit').updateToolbar();
	    });
	},
	exec: function(exec, param){
	    this.iframe.get(0).contentWindow.focus();
	    if(exec=='class'){
		var magic="#00f001";
		this.doc.get(0).execCommand('ForeColor', false, magic);
		this.doc.find('font[color="'+magic+'"]').each(function(){
		    text=$(this).html();
		    $(this).replaceWith('<span class="'+param+'">'+text+'</span>');
		});
		this.update();
	    }
	    else if(exec=='removeClass'){
		this.doc.find('span.lightedit-active').replaceWith(this.doc.find('span.lightedit-active').html());
	    }
	    else{ this.doc.get(0).execCommand(exec, false, param); }
	    this.historySave();
	    this.updateHeight();
	    this.update();
	},
	insert: function(text){
	    this.exec('inserthtml', text);
	},
	func: function(name, element){
	    if(LighteditFunctions[name]!=undefined){
		$.proxy(LighteditFunctions[name], this.parent, $(element).parent())();
	    }
	},
	plugin: function(name){
	    this.parent.dialog({
		'ajax': this.parent.options.path+'plugins/'+name+'.php',
		'data': this.parent.options['plugin-'+name]
	    });
	},
	historySave: function(){
	    this.index++;
	    for(i=this.index; i<this.history.length; i++){
		delete this.history[i];
	    }
	    this.history[this.index]=[this.doc.find('body').html(),this.selectionGet()];
	},
	historyNext: function(){
	    if(this.index+1>=this.history.length) return;
	    this.index++;
	    this.doc.find('body').html(this.history[this.index][0]);
	    this.selectionSet(this.history[this.index][1]);
	    this.update();
	    this.updateHeight();
	},
	historyPrev: function(){
	    if(this.index<1) return;
	    this.index--;
	    this.doc.find('body').html(this.history[this.index][0]);
	    this.selectionSet(this.history[this.index][1]);
	    this.update();
	    this.updateHeight();
	},
	selectionGet: function(){
	    var win=this.win.get(0);
	    var doc=this.doc.get(0);
	    
	    if (win.getSelection) {
		sel = win.getSelection();
		if (sel.getRangeAt && sel.rangeCount) {
		    return sel.getRangeAt(0);
		}
	    } else if (doc.selection && doc.selection.createRange) {
		return doc.selection.createRange();
	    }
	    return null;
	},
	selectionSet: function(selection){
	    var win=this.win.get(0);
	    var doc=this.doc.get(0);
	    if(selection){
		if(win.getSelection){
		    sel=win.getSelection();
		    sel.removeAllRanges();
		    sel.addRange(selection);
		}
		else if(doc.selection && sel.select){
		    sel.select();
		}
	    }
	},
	selectionToEnd:function(){
	    var win=this.win.get(0);
	    var doc=this.doc.get(0);
	    var sel=win.getSelection();
	    var p=this.doc.find('p:last').get(0);
	    if(doc.selection){
		//var range = doc.selection.createRange();
	    }
	    else{
		var range = doc.createRange();
		range.setStart(p, 1);
		range.setEnd(p, 1);
		sel.removeAllRanges();
		sel.addRange(range);
	    }
	},
	get: function(){
	    this.updateText();
	    return this.textarea.val();
	},
	set: function(html){
	    this.textarea.val(html);
	    this.updateHtml();
	}
    };
    $(document).click(function(){
	$('.lightedit-dropdown').hide();
    });
    $(window).scroll(function(){
	$('.lightedit').each(function(){
	    var lightedit=$(this).data('lightedit');
	    lightedit.updateToolbar();
	    lightedit.dialogUpdate();
	});
	$('.lightedit-dropdown').hide();
    });
    $(window).resize(function(){
	$('.lightedit').each(function(){
	    var lightedit=$(this).data('lightedit');
	    lightedit.updateToolbar();
	    lightedit.dialogUpdate();
	});
	$('.lightedit-dropdown').hide();
	var outerVertical=parseInt($('.lightedit.full').css('padding-top'))+parseInt($('.lightedit.full').css('padding-bottom'));
	$('.lightedit.full').height($(window).height()-outerVertical);
	var outerHorizontal=parseInt($('.lightedit.full').css('padding-left'))+parseInt($('.lightedit.full').css('padding-right'));
	$('.lightedit.full').width($(window).width()-outerHorizontal);
    });
    setInterval(function(){
	$('.lightedit-toolbar-left.lightedit-over').each(function(){
	    var toolbar=$(this).closest('.lightedit').find('.lightedit-toolbar');
	    var left=toolbar.scrollLeft();
	    toolbar.scrollLeft(left-10);
	    if(left==toolbar.scrollLeft()){
		$(this).removeClass('lightedit-over').hide();
	    }
	});
	$('.lightedit-toolbar-right.lightedit-over').each(function(){
	    var toolbar=$(this).closest('.lightedit').find('.lightedit-toolbar');
	    var left=toolbar.scrollLeft();
	    toolbar.scrollLeft(left+10);
	    if(left==toolbar.scrollLeft()){
		$(this).removeClass('lightedit-over').hide();
	    }
	});
    },100);
})(jQuery);