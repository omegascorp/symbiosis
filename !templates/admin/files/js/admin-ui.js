(function($){
    $(document).ready(function(){
	Admin.init($(document), function(){
	    Admin.hash();
	});
	setTimeout(function(){
	    Admin.step(0);
	},0);
	$('#admin-overly').click(function(){
	    Admin.overlyHide();
	    $('.admin-layer').each(function(){
		if($(this).data('adminlayer').get('closable')) $(this).data('adminlayer').remove();
	    });
	});
    });
    $(document).click(function(e){
	if(!$(e.target).closest('.admin-layer-tooltip').length){
	    $('.admin-layer-tooltip').each(function(){
		if($(this).data('adminlayer').get('removable')) $(this).data('adminlayer').remove();
	    });
	}
    });
    $(window).resize(function(){
	Admin.step();
	$('.admin-layer').each(function(){
	    if($(this).data('adminlayer').get('removable')) $(this).data('adminlayer').remove();
	    else $(this).data('adminlayer').change();
	});
    });
    $(window).scroll(function(){
	Admin.step();
    });
    $(window).bind('hashchange',function(){
	Admin.hash();
    });
    $.fn.adminList=function(opts){
	$(this).data('admin', new AdminEditableList($(this), opts));
    };
    $.fn.adminTable=function(opts){
	$(this).data('admin', new AdminEditableTable($(this), opts));
    };
})(jQuery);

/* Simple JavaScript Inheritance
* By John Resig http://ejohn.org/
* MIT Licensed.
*/
(function(){
    var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;
    this.Class = function(){};
    Class.extend = function(prop) {
	var _super = this.prototype;
	initializing = true;
	var prototype = new this();
	initializing = false;
	for (var name in prop) {
	    prototype[name] = typeof prop[name] == "function" && 
	    typeof _super[name] == "function" && fnTest.test(prop[name]) ?
	    (function(name, fn){
		return function() {
		    var tmp = this._super;
		    this._super = _super[name];
		    var ret = fn.apply(this, arguments);
		    this._super = tmp;
		    return ret;
		};
	    })(name, prop[name]) :
	    prop[name];
	}
	function Class() {
	    if ( !initializing && this.init )
		this.init.apply(this, arguments);
	}
	Class.prototype = prototype;
	Class.prototype.constructor = Class;
	Class.extend = arguments.callee;
	return Class;
    };
})();

var Admin={
    'top': 0,
    'init': function(element, func){
	Admin.tabsCount+=element.find('.admin-widget-tab').length;
	if(element==undefined) element=$(document);
	element.find('.admin-widget-icon').hover(function(){
	    if(!$(this).hasClass('active')){
		$(this).find('.max').show();
	    }
	    $(this).addClass('hover');
	},function(){
	    if(!$(this).hasClass('active')) $(this).find('.max').hide();
	    $(this).removeClass('hover');
	});
	element.find('.admin-widget-block').hover(function(){
	    $(this).addClass('hover');
	},function(){
	    $(this).removeClass('hover');
	});
	element.find('.admin-button').hover(function(){
	    $(this).addClass('hover');
	},function(){
	    $(this).removeClass('hover');
	});
	element.find('.admin-widget-tabs-nav li').each(function(){
	    Admin.tabInit($(this).attr('data-tab'));
	});
	//Admin tabs init
	element.find('.admin-tabs').each(function(){
	    $(this).find('.admin-tab:not(:first)').hide();
	    $(this).find('.admin-tabs-nav li:first').addClass('active');
	    $(this).find('.admin-tabs-nav li').click(function(){
		$(this).parent().find('li').removeClass('active');
		$(this).addClass('active');
		var index=$(this).index();
		$(this).closest('.admin-tabs').find('.admin-tab').hide().eq(index).show();
	    });
	});
	//Admin checkboxes init
	element.find('.admin-checkbox').click(function(){
	    if($(this).hasClass('admin-checked')){
		$(this).removeClass('admin-checked');
	    }
	    else{
		$(this).addClass('admin-checked');
	    }
	});
	//Admin selectable init
	element.find('.admin-selectable-item,.admin-radio,.admin-choose').click(function(){
	    if($(this).closest('.admin-selectable').length){
		$(this).closest('.admin-selectable').find('.admin-selectable-item,.admin-radio,.admin-choose').removeClass('admin-selected');
		$(this).addClass('admin-selected');
	    }
	    else if($(this).closest('.admin-selectable-multiselect').length){
		if($(this).hasClass('admin-selected')){
		    $(this).removeClass('admin-selected');
		}
		else{
		    $(this).addClass('admin-selected');
		}
	    }
	    
	});
	element.find('.admin-switch').click(function(){
	    if($(this).hasClass('admin-checked')){
		$(this).removeClass('admin-checked');
	    }
	    else{
		$(this).addClass('admin-checked');
	    }
	});
	//Translit
	element.find('.admin-translit-source').keyup(function(e){
	    var abbr=$(this).attr('data-abbr');
	    var receiver=$(this).closest('.admin-widget-tab').find('.admin-translit-receiver[data-abbr="'+abbr+'"]');
	    var translit=Admin.translit[abbr];
	    var text=$(this).val().toLowerCase();
	    var res='';
	    if(translit==undefined) res=text;
	    else{
		for(key in text){
		    val=text[key];
		    if(translit[val]!=undefined){
			res+=translit[val];
		    }
		}
	    }
	    receiver.val(res);
	});
	if(func!=undefined) func();
    },
    'translit': {},
    'step': function(duration){
	if(duration==undefined) duration=500;
	if($('.admin-widget-top').length){
	    $('.admin-widget-top').each(function(){
		if($(this).css('display')=='block'){
		    Admin.top=$(this).height();
		}
		$(this).css({
		    'top': Math.max(0, $(window).scrollTop()-$('#middle').offset().top)+'px'
		});
		if($(this).next().hasClass('admin-widget-top-holder')){
		    $(this).next().animate({
			'height': $(this).height()+'px'
		    }, {'queue':false, 'duration': duration});
		}
		else{
		    var top=$('<div class="admin-widget-top-holder"></div>');
		    top.height($(this).height());
		    $(this).after(top);
		}
	    });
	}
	if($('.admin-widget-bottom').length){
	    $('.admin-widget-bottom').each(function(){
		$(this).css({
		    'top': Math.min($('#middle').height()-$(this).height(), $(window).scrollTop()+$(window).height()-$(this).height()-$('#middle').offset().top)+'px'
		});
		if($(this).next().hasClass('admin-widget-bottom-holder')){
		    $(this).next().css({
			'height': $(this).height()+'px'
		    }, {'queue':false, 'duration': duration});
		}
		else{
		    var bottom=$('<div class="admin-widget-bottom-holder"></div>');
		    bottom.height($(this).height());
		    $(this).after(bottom);
		}
	    });
	}
    },
    'hash': function(){
	var hash=Admin.hashExplode(location.hash.substr(1));
	if(hash['add']!=undefined){
	    var admin=$('.admin-widget-list,.admin-widget-table').first().data('admin');
	    if(admin!=undefined){
		admin.change(0);
	    }
	    else{
		$('.admin-widget-list,.admin-widget-table').first().attr('data-change', '0');
	    }
	}
	else if(hash['edit']!=undefined){
	    var id=hash['edit'];
	    var admin=$('.admin-widget-list,.admin-widget-table').first().data('admin');
	    if(admin!=undefined){
		admin.change(id);
	    }
	    else{
		$('.admin-widget-list,.admin-widget-table').first().attr('data-change', id);
	    }
	}
	else if(hash['remove']!=undefined){
	    var id=hash['remove'];
	    var admin=$('.admin-widget-list,.admin-widget-table').first().data('admin');
	    if(admin!=undefined){
		admin.remove(id);
	    }
	    else{
		$('.admin-widget-list,.admin-widget-table').first().attr('data-remove', id);
	    }
	    Admin.tabShow($('.admin-widget-tabs-nav li[data-tab]:first').attr('data-tab'));
	}
	else if(hash['tab']!=undefined){
	    var tab=hash['tab'];
	    if(this.tabExists(tab)){
		this.tabShow(tab);
	    }
	}
	else if(hash['action']!=undefined){
	    var admin=$('.admin-widget-list:first').data('admin');
	    if(admin!=undefined){
		admin.action(hash['action']);
	    }
	    else{
		$('.admin-widget-list:first').attr('data-action', hash['action']);
	    }
	}
	else{
	    if($('.admin-widget-tabs-nav li[data-tab]:first').attr('data-tab')!=undefined) Admin.tabShow($('.admin-widget-tabs-nav li[data-tab]:first').attr('data-tab'));
	}
    },
    'hashExplode': function(hash){
	var exp=hash.split("/");
        var result={};
        for(key in exp){
            var val=exp[key];
	    var eq=val.indexOf('=');
	    var index, value;
	    if(eq>0){
		index=val.substr(0,eq);
		value=val.substr(eq+1);
	    }
	    else{
		index=val;
		value='';
	    }
            result[index]=value;
        }
        return result;
    },
    'overlyShow': function(){
	$('#admin-overly').fadeIn('fast');
    },
    'overlyHide': function(){
	$('#admin-overly').fadeOut('fast');
    },
    'tabsCount': 0,
    'tabsCountMax': 40,
    'tabAdd': function(name, title, content){
	if(Admin.tabsCount>=Admin.tabsCountMax){
	    var x=$('.admin-widget-tabs-nav').parent().offset().top+$('.admin-widget-tabs-nav').parent().height();
	    var y=$('.admin-widget-tabs-nav').parent().offset().left+$('.admin-widget-tabs-nav').parent().width()/2;
	    var tooltip=new AdminLayerTooltip({
		'x': x,
		'y': y,
		'content': $('.admin-labels-tabs-count').html(),
		'type': 'top',
		'width': 200
	    });
	    if($('.admin-widget-tabs-nav li[data-tab].current').attr('data-tab')!=undefined) Admin.tabShow($('.admin-widget-tabs-nav li[data-tab].current').attr('data-tab'));
	    return false;
	}
	var li=$('<div><li class="admin-widget-icon" data-tab="'+name+'"><a href="'+window.location.pathname+'#tab='+name+'" class="admin-link"><span>'+title+'</span></a></li></div>');
	Admin.init(li);
	if($('.admin-widget-tabs-nav .admin-widget-icon-last').length){
	    $('.admin-widget-tabs-nav .admin-widget-icon-last').before(li.children());
	}
	else{
	    $('.admin-widget-tabs-nav').append(li.children());
	}
	if(content==undefined) content='';
	var tab=$('<div class="admin-widget-tab" data-tab="'+name+'" style="display:none;"></div>');
	if(content) tab.append(content);
	$('.admin-widget-tabs').append(tab);
	Admin.tabInit(name);
	Admin.step();
	Admin.tabsCount++;
	return true;
    },
    'tabInit': function(name){
	$('.admin-widget-tabs-nav li[data-tab='+name+']').click(function(e){
	    Admin.tabShow($(e.currentTarget).attr('data-tab'));
	});
    },
    'tabSet': function(name, title, content){
	$('.admin-widget-tabs-nav li[data-tab='+name+'] a span').html(title);
	$('.admin-widget-tab[data-tab='+name+']').html('');
	$('.admin-widget-tab[data-tab='+name+']').append(content);
    },
    'tabRemove': function(name){
	$('.admin-widget-tabs-nav li[data-tab='+name+']').prev().click();
	$('.admin-widget-tabs-nav li[data-tab='+name+']').remove();
	$('.admin-widget-tab[data-tab='+name+']').remove();
	Admin.tabsCount--;
    },
    'tabShow': function(name){
	location.hash='tab='+name;
	$('.admin-widget-tabs-nav li').removeClass('current');
	$('.admin-widget-tabs-nav li[data-tab='+name+']').addClass('current');
	$('.admin-widget-tab,.admin-widget-tab').hide();
	$('.admin-widget-tab[data-tab='+name+']').show();
	Admin.step();
    },
    'tabExists': function(name){
	return $('.admin-widget-tabs-nav li[data-tab='+name+']').length?true:false;
    }
};

var AdminLayer=Class.extend({
    'init': function(opts){
	this.options=$.extend({
	    'class': '',
	    'x': 'middle',
	    'y': 'center',
	    'width': 'auto',
	    'height': 'auto',
	    'content': '',
	    'element': null,
	    'speed': 0,
	    'display': 'absolute',
	    'hidden': false,
	    'z-index': 100,
	    'point': ['top','left'],
	    'removable': false,
	    'closable': false
	},opts);
	var cls=this.options['class']?'admin-layer-'+this.options['class']:'';
	var layer=$('<div class="admin-layer '+cls+'"><div class="admin-layer-content"></div></div>');
	layer.data('adminlayer', this);
	if(this.options.closable) layer.addClass('closable');
	this.options['element']=layer;
	$('#admin-layers').append(layer);
	this.options['element'].hide();
	this.x=0;
	this.y=0;
	this.display='absolute';
	this.width=null;
	this.height=null;
	this.change();
	if(!this.options['hidden']) this.show();
    },
    'change': function(opts){
	if(opts!=undefined) this.options=$.extend(this.options,opts);
	var content=this.options['element'].find('.admin-layer-content');
	if(typeof(this.options['content'])=='object'){
	    content.html('');
	    content.append(this.options['content']);
	}
	else{
	    content.html(this.options['content']);  
	}
	if(this.options['width']!='auto'){
	    this.options['element'].width(this.options['width']);
	}
	this.width=this.options['element'].outerWidth();
	if(this.options['height']!='auto'){
	    this.options['element'].height(this.options['height']);
	}
	this.height=this.options['element'].outerHeight();
	if(this.options['x']=='top'){
	    this.x=0;
	    this.display='fixed';
	}
	else if(this.options['x']=='bottom'){
	    this.x=$(window).height()-this.height;
	    this.display='fixed';
	}
	else if(this.options['x']=='middle'){
	    this.x=$(window).height()/2-this.height/2;
	    this.display='fixed';
	}
	else{
	    this.x=this.options['x'];
	}
	
	if(this.options['point'][0]=='middle'){
	    this.x-=this.height/2;
	}
	if(this.options['point'][0]=='bottom'){
	    this.x-=this.height;
	}
	
	if(this.options['y']=='left'){
	    this.y=0;
	}
	else if(this.options['y']=='right'){
	    this.y=$(window).width()-this.width;
	}
	else if(this.options['y']=='center'){
	    this.y=$(window).width()/2-this.width/2;
	}
	else{
	    this.y=this.options['y'];
	}
	
	if(this.options['point'][1]=='center'){
	    this.y-=this.width/2;
	}
	if(this.options['point'][1]=='right'){
	    this.y-=this.width;
	}
	
	this.options['element'].css({
	    'position': this.display,
	    'z-index': this.options['z-index']
	});
	this.options['element'].animate({
	    'top': this.x+'px',
	    'left': this.y+'px'
	}, this.options['speed']);
    },
    'show': function(func){
	this.change();
	this.options['hidden']=true;
	this.options['element'].fadeIn('normal', $.proxy(func, this));
    },
    'hide': function(func){
	this.change();
	this.options['false']=true;
	this.options['element'].fadeOut('normal', $.proxy(func, this));
    },
    'remove': function(){
	this.hide($.proxy(function(){
	    this.options['element'].remove();
	}, this));
    },
    'get': function(key){
	return this.options[key];
    }
});
var AdminLayerTooltip=AdminLayer.extend({
    'init': function(opts){
	opts['hidden']=true;
	opts['removable']=false;
	this._super(opts, true);
	if(this.options['type']==undefined) this.options['type']='left';
	if(this.options['tail']==undefined) this.options['tail']=this.options['type']=='left'||this.options['type']=='right'?'middle':'center';
	
	this.options['element'].addClass('admin-layer-tooltip').addClass('admin-layer-tooltip-'+this.options['type']);
	this.change();
	
	var tail=$('<div class="admin-layer-tail"></div>');
	
	if(this.options['type']=='left'){ this.options['point']=[this.options['tail'],'left']; tail.css({'margin-left':'-6px'}); }
	else if(this.options['type']=='right'){ this.options['point']=[this.options['tail'],'right']; tail.css({'margin-left':(this.width-8)+'px'}); }
	else if(this.options['type']=='top'){ this.options['point']=['top',this.options['tail']]; tail.css({'margin-top':'-6px'}); }
	else if(this.options['type']=='bottom'){ this.options['point']=['bottom',this.options['tail']];  tail.css({'margin-top':(this.height-8)+'px'}); }
	
	if(this.options['tail']=='top') tail.css({'margin-top':'10px'});
	if(this.options['tail']=='middle') tail.css({'margin-top':(this.height/2-8)+'px'});
	if(this.options['tail']=='bottom') tail.css({'margin-top':(this.height-17)+'px'});
	
	if(this.options['tail']=='left') tail.css({'margin-left':'10px'});
	if(this.options['tail']=='center') tail.css({'margin-left':(this.width/2-8)+'px'});
	if(this.options['tail']=='right') tail.css({'margin-left':(this.width-27)+'px'});
	
	this.options['element'].prepend(tail);
	this.show(function(){
	    this.options['removable']=true;
	});
    }
});
var AdminLayerPopup=AdminLayer.extend({
    'init': function(opts){
	Admin.overlyShow();
	opts['z-index']=10000;
	opts['removable']=false;
	opts['closable']=true;
	opts['hidden']=true;
	this._super(opts, true);
	this.options['element'].addClass('admin-layer-popup');
	this.change();
	this.show();
    },
    'remove': function(){
	Admin.overlyHide();
	this._super();
    }
});

var AdminEditable=Class.extend({
    'init': function(element, opts){
	this.element=element;
	this.options=$.extend({
	    'reload': '',
	    'change': '',
	    'changeLoad': function(){},
	    'changeSave': function(){},
	    'newIndex': 0
	},opts);
	if(this.element.attr('data-change')!=undefined){
	    this.change(this.element.attr('data-change'));
	}
	if(this.element.attr('data-remove')!=undefined){
	    this.remove(this.element.attr('data-remove'));
	}
	if(this.element.attr('data-action')!=undefined){
	    this.action(this.element.attr('data-action'));
	}
    },
    'markup': function(){
	
    },
    'reload': function(){
	$.ajax({
	    'data':{
		'symbiont': this.options.reload,
		'link': $.symbiosis.link
	    },
	    'dataType': 'html',
	    'success': $.proxy(function(r){
		this.element.html(r);
		this.markup();
		Admin.init(this.element);
		Admin.step();
	    }, this)
	});
    },
    'change': function(id){
	if(typeof(id)=='string'){
	    var split=id.split(',');
	    if(split.length>1){
		for(i=0;i<split.length;i++){
		    this.change(split[i]);
		}
		return true;
	    }
	}
	var tab;
	if(id==0){
	    tab='add-'+this.options.newIndex++;
	}
	else{
	    tab='edit-'+id;
	}
	if(!Admin.tabExists(tab)){
	    if(Admin.tabAdd(tab, 'Loading')){
		$.ajax({
		    'data': {
			'symbiont': this.options.change,
			'link': $.symbiosis.link,
			'id': id
		    },
		    'dataType': 'html',
		    'success': $.proxy(function(r){
			var content=$(r);
			Admin.init(content);
			
			content.find('.admin-button-cancel').click($.proxy(function(){
			    Admin.tabRemove(this[0]);
			}, [this[2]]));
			
			content.find('.admin-button-save').click($.proxy(function(){
			    var save=$.proxy(this[0].options.changeSave, $('.admin-widget-tab[data-tab="'+this[1]+'"]'), this[0], this[1])();
			    if(save){
				Admin.tabRemove(this[1]);
				$.proxy(this[0].reload(), this);
			    }
			}, [this[0],this[2]]));
			
			Admin.tabSet(this[2], content.find('h1').html(), content);
			Admin.tabShow(this[2]);
			$.proxy(this[0].options.changeLoad, $('.admin-widget-tab[data-tab="'+this[2]+'"]'), this[0], this[2])();
		    }, [this, id, tab])
		});
	    }
	    return false;
	}
	return true;
    },
    'remove': function(id){
	$.ajax({
	    'data': {
		'symbiont': this.options.remove,
		'link': $.symbiosis.link,
		'id': id
	    },
	    'dataType': 'html',
	    'success': $.proxy(function(r){
		var content=$(r);
		Admin.init(content);
		var split=this[1].split(',');
		var element, top, offset;
		if(split.length>1||this[0].element.find('li .admin-item[data-id='+this[1]+'] .remove').length==0){
		    element=$(document).find('.admin-widget-icon-remove');
		    type='bottom';
		    tail='left';
		    x=element.offset().top;
		    y=element.offset().left;
		}
		else{
		    element=this[0].element.find('li .admin-item[data-id='+this[1]+'] .remove')
		    type='top';
		    tail='right';
		    x=element.offset().top+element.outerHeight();
		    y=element.offset().left+element.outerWidth();
		}
		
		var tooltip=new AdminLayerTooltip({
		    'x': x,
		    'y': y,
		    'class': 'remove',
		    'content': content,
		    'type': type,
		    'tail': tail,
		    'width': 200
		});
		
		$('.admin-layer-remove .admin-button-no').click(function(){
		    $('.admin-layer-remove').data('adminlayer').remove();
		});
		
		$('.admin-layer-remove .admin-button-yes').click($.proxy(function(){
		    var id=this[1];
		    var ids=this[1].split(',');
		    if(ids.length==1){
			ids=ids[0];
		    }
		    $.ajax({
			'data': {
			    'symbiont': this[0].options.removeOk,
			    'link': $.symbiosis.link,
			    'id': ids
			}
		    });
		    if(typeof(ids)=='object'){
			for(key in ids){
			    id=ids[key];
			    this[0].element.find('li .admin-item[data-id="'+id+'"],tr[data-id="'+id+'"]').closest('li').remove();
			    this[0].element.find('tr[data-id="'+id+'"]').remove();
			}
		    }
		    else{
			this[0].element.find('li .admin-item[data-id='+ids+']').closest('li').remove();
			this[0].element.find('tr[data-id="'+id+'"]').remove();
		    }
		    $('.admin-layer-remove').data('adminlayer').remove();
		}, [this[0], this[1]]));
	    }, [this, id])
	});
    },
    'action': function(action){
	if(typeof(this.options['action-'+action])=='function'){
	    this.options['action-'+action]();
	}
    }
});

var AdminEditableList=AdminEditable.extend({
    'init': function(element, opts){
	this._super(element, opts);
	this.markup();
    },
    'markup': function(){
	this.element.find('li .admin-item').addClass('admin-animate');
	this.element.find('li .admin-item').click(function(e){
	    if(!e.ctrlKey){
		if($(this).closest('ul[data-level=0]').length){
		    $(this).closest('ul[data-level=0]').find('li .admin-item').removeClass('admin-selected');
		}
		else{
		    $(this).closest('ul').find('li .admin-item').removeClass('admin-selected');
		}
		
	    }
	    if($(this).hasClass('admin-selected')){
		$(this).removeClass('admin-selected');
	    }
	    else{
		$(this).addClass('admin-selected');
	    }
	    if($(this).closest('ul').find('li .admin-item.admin-selected').length){
		var arr='';
		$(this).closest('ul').find('li .admin-item.admin-selected').each(function(){
		    if(arr) arr+=',';
		    arr+=$(this).attr('data-id');
		});
		
		var a=$(this).closest('.admin-widget-tab').find('.admin-widget-icon-edit a');
		var href=$(this).closest('.admin-widget-tab').find('.admin-widget-icon-edit:first a').attr('href');
		a.attr('href', href.substr(0,href.indexOf('#'))+'#edit='+arr);
		$(this).closest('.admin-widget-tab').find('.admin-widget-icon-edit').show();
		
		var a=$(this).closest('.admin-widget-tab').find('.admin-widget-icon-remove a');
		var href=$(this).closest('.admin-widget-tab').find('.admin-widget-icon-remove:first a').attr('href');
		a.attr('href', href.substr(0,href.indexOf('#'))+'#remove='+arr);
		$(this).closest('.admin-widget-tab').find('.admin-widget-icon-remove').show();
	    }
	    else{
		$(this).closest('.admin-widget-tab').find('.admin-widget-icon-edit').hide();
		$(this).closest('.admin-widget-tab').find('.admin-widget-icon-remove').hide();
	    }
	});
	if(this.element.attr('data-sortable')!='false'){
	    this.sortable();
	}
    },
    'sortable': function(){
	this.element.find('ul').sortable({
	    placeholder: 'admin-widget-list-placeholder',
	    start: function(){
		$(this).find('li').removeClass('admin-animate');
		$(this).find('ul').hide();
	    },
	    stop: function(){
		$(this).find('li').addClass('admin-animate');
		$(this).find('ul').show();
	    },
	    update: $.proxy(function(){
		var sort=[];
		this.element.find('li .admin-item').each(function(){
		    var id=$(this).attr('data-id');
		    if($(this).find('.id').langth) id=$(this).find('.id').html();
		    sort.push(id);
		});
		$.ajax({
		    'data':{
			'symbiont': this.options.sort,
			'sort': sort
		    }
		});
	    }, this)
	});
	this.element.attr('data-sortable', 'true');
    },
    'unsortable': function(){
	this.element.find('ul').sortable('destroy');
	this.element.attr('data-sortable', 'false');
    }
});

var AdminEditableTable=AdminEditable.extend({
    'init': function(element, opts){
	this._super(element, opts);
	this.markup();
    },
    'markup': function(){
	this.element.find('tbody tr').click(function(e){
	    if(!e.ctrlKey){
		if($(this).closest('table').length){
		    $(this).closest('table').find('tbody tr').removeClass('admin-selected');
		}
		else{
		    $(this).closest('table').find('tbody tr').removeClass('admin-selected');
		}
		
	    }
	    if($(this).hasClass('admin-selected')){
		$(this).removeClass('admin-selected');
	    }
	    else{
		$(this).addClass('admin-selected');
	    }
	    if($(this).closest('table').find('tbody tr.admin-selected').length){
		var arr='';
		$(this).closest('table').find('tbody tr.admin-selected').each(function(){
		    if(arr) arr+=',';
		    arr+=$(this).attr('data-id');
		});
		
		var a=$(this).closest('.admin-widget-tab').find('.admin-widget-icon-edit a');
		var href=$(this).closest('.admin-widget-tab').find('.admin-widget-icon-edit:first a').attr('href');
		a.attr('href', href.substr(0,href.indexOf('#'))+'#edit='+arr);
		$(this).closest('.admin-widget-tab').find('.admin-widget-icon-edit').show();
		
		var a=$(this).closest('.admin-widget-tab').find('.admin-widget-icon-remove a');
		var href=$(this).closest('.admin-widget-tab').find('.admin-widget-icon-remove:first a').attr('href');
		a.attr('href', href.substr(0,href.indexOf('#'))+'#remove='+arr);
		$(this).closest('.admin-widget-tab').find('.admin-widget-icon-remove').show();
	    }
	    else{
		$(this).closest('.admin-widget-tab').find('.admin-widget-icon-edit').hide();
		$(this).closest('.admin-widget-tab').find('.admin-widget-icon-remove').hide();
	    }
	});
	this.element.find('thead td').append('<span class="sort">&nbsp;</span>');
	this.element.find('thead td').click(function(e){
	    var td=$(e.target);
	    td.closest('thead').find('td').not(e.target).find('.sort').removeClass('desc asc');
	    AdminEditableTableIndex=td.index();
	    if(td.find('.sort').hasClass('desc')){
		td.find('.sort').removeClass('desc').addClass('asc');
		AdminEditableTableScending=1;
	    }
	    else if(td.find('.sort').hasClass('asc')){
		td.find('.sort').removeClass('asc');
		AdminEditableTableScending=0;
	    }
	    else{
		td.find('.sort').addClass('desc');
		AdminEditableTableScending=-1;
	    }
	    var tr=td.closest('table').find('tbody tr').sort(AdminEditableTableSort);
	    td.closest('table').find('tbody').html('');
	    td.closest('table').find('tbody').append(tr);
	});
    }
});
function AdminEditableTableSort(i, j){
    var first, second;
    if(AdminEditableTableScending==0){
	first=$(i).attr('data-id');
	second=$(j).attr('data-id');
    }
    else{
	first=$(i).find('td').eq(AdminEditableTableIndex);
	second=$(j).find('td').eq(AdminEditableTableIndex);
	first=first.attr('data-sort')?first.attr('data-sort'):first.text();
	second=second.attr('data-sort')?second.attr('data-sort'):second.text();
    }
    
    if(first>second){
	return AdminEditableTableScending>=0?1:-1;
    }
    if(first<second){
	return AdminEditableTableScending>=0?-1:1;
    }
    return 2;
}
var AdminEditableTableScending=false;
var AdminEditableTableIndex=0;