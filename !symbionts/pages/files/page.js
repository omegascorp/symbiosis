$(document).ready(function(){
    $('.symbiosis-page .admin-widget-tab[data-tab="plugin"] .admin-button-save').click(function(){
        if($('.symbiosis-page .admin-widget-tab[data-tab="plugin"] .admin-selected').hasClass('default')){
            return;
        }
        $('.symbiosis-page .admin-widget-tab[data-tab="plugin"] .default').removeClass('default');
        $('.symbiosis-page .admin-widget-tab[data-tab="plugin"] .admin-selected').addClass('default');
        $('.symbiosis-page').attr('data-symbiont', $('.symbiosis-page .admin-widget-tab[data-tab="plugin"] .admin-selected').attr('data-plugin'));
        $.ajax({
            'data':{
                'symbiont': 'Pages-Page.dbSet',
                'id': $('.symbiosis-page').attr('data-id'),
                'plugin': $('.symbiosis-page .admin-widget-tab[data-tab="plugin"] .admin-selected').attr('data-plugin')
            },
            'success': function(){
                
            }
        });
        SPageContent($('.symbiosis-page').attr('data-symbiont'));
    });
    $('.symbiosis-page .admin-widget-tab[data-tab="plugin"] .admin-button-reset').click(function(){
        $('.symbiosis-page .admin-widget-tab[data-tab="plugin"] .admin-selected').removeClass('admin-selected');
        $('.symbiosis-page .admin-widget-tab[data-tab="plugin"] .default').addClass('admin-selected');
        SPageContent($('.symbiosis-page').attr('data-symbiont'));
    });
    
    
    /*
    $("#simple .position").sortable({
        connectWith: '.position',
        items: '.widget',
        start:function(event, ui){
            if(ui.item.hasClass('block')){
                ui.item.sortable("destroy");
            }
        },
        stop:function(){
            SPagesSave();
        }
    });
    $("#simple .symbionts .widget").draggable({
        connectToSortable: ".position",
        helper: "clone",
        revert: "invalid",
        stop: function(){
            var widget=$(this).closest('#simple').find('.position').find('.new');
            if(widget.length>0){
                $.ajax({
                    'data':{
                        'symbiont': 'Admin.ajax',
                        'add': widget.find('.symbiont').html()
                    },
                    'success':function(r){
                        widget.find('.symbiont').html(r.symbiont);
                        widget.removeClass('new').find('.controls').show();
                        SPagesWidgetInfo(widget);
                        SPagesWidget(widget);
                        SPagesSave();
                    }
                });
            }
        }
    });
    $("#simple .position").each(function(){
        var alias=$(this).attr('position');
        var current=$(this);
        current.adminPulsate();
        $.ajax({
            'data':{
                'symbiont': 'Pages-Page.dbRead',
                'alias': alias
            },
            'success':function(widgets){
                for(key in widgets){
                    widget=widgets[key];
                    element=$('#simple .examples .widget').clone();
                    element.find('.title').html(widget.title);
                    element.find('.symbiont').html(widget.symbiont);
                    element.find('.accessLevel').html(widget.accessLevel);
                    element.find('.icon').attr('src', widget.icon);
                    current.append(element);
                    SPagesWidget(element);
                }
                current.adminPulsate(true);
            }
        });
    });
    */
});
function SPageContent(symbiont){
        $.ajax({
            'data':{
                'symbiont': 'Admin.ajax',
                'admin': symbiont
            },
            'dataType': 'html',
            'success': function(r){
                var html=$(r);
                Admin.init(html);
                $('.symbiosis-page .admin-widget-tab[data-tab="content"]').html(html);
            }
        });
    }
    function SPageSave(symbiont){
        $.ajax({
            'data':{
                'symbiont': 'Pages-Page.dbSet',
                'id': $('.symbiosis-page').attr('data-id'),
                'plugin': symbiont
            },
            'success': function(){
                
            }
        });
    }
/*
function SPagesSave(){
    var positions={};
    $("#simple .position").each(function(){
        var position=$(this).attr('position');
        var widgets=[];
        $(this).find('.widget').each(function(){
            widget={
                'symbiont':$(this).find('.symbiont').html(),
                'accessLevel':$(this).find('.accessLevel').html()
            }
            widgets.push(widget);
        });
        if(!widgets.length) widgets='';
        positions[position]=widgets;
    });
    $.ajax({
        'data':{
            'symbiont': 'Pages-Page.dbSave',
            'positions': positions
        },
        'success':function(){
            
        }
    });
}
function SPagesWidget(widget){
    widget.find('.controls .close').click(function(event){
        $.ajax({
            'data':{
                'symbiont': 'Pages.delete'
            },
            'dataType': 'html',
            'success':function(result){
                var content=$(result);
                content.find('.yes').button().click(function(){
                    $.ajax({
                        'data':{
                            'symbiont': 'Admin.ajax',
                            'delete': widget.find('.symbiont').html()
                        },
                        'success':function(r){
                            if(r.status){
                                if(widget.find('.editing').css('display')!='none'){
                                    SPagesEnable();
                                }
                                widget.remove();
                                SPagesSave();
                            }
                            else{
                                widget.addClass('ui-state-error');
                            }
                        }
                    });
                    $(this).closest('.admin-dialog').remove();
                });
                content.find('.no').button().click(function(){
                    $(this).closest('.admin-dialog').remove();
                });
                result=$.adminDialog(content, {"x": event.pageX, "y":event.pageY});
            }
        });
    });
    widget.find('.controls .edit').click(function(){
        if(widget.find('.editing').css('display')=='none'){
            SPagesWidgetOpen(widget);
        }
        else{
            SPagesWidgetClose(widget);
        }
    });
}
var SPagesLocks=0;
function SPagesWidgetOpen(widget){
    SPagesDisable();
    widget.find('.edit').removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-n');
    widget.css('cursor', 'auto');
    $.ajax({
        'data':{
            'symbiont': 'Admin.ajax',
            'edit': widget.find('.symbiont').html(),
            'link': $.symbiosis.link
        },
        'dataType': 'html',
        'success':function(r){
            widget.find('.editing').html(r).adminUI().slideDown();
        }
    });
}
function SPagesWidgetClose(widget){
    SPagesEnable();
    widget.css('cursor', 'move');
    widget.find('.edit').removeClass('ui-icon-triangle-1-n').addClass('ui-icon-triangle-1-s');
    widget.find('.editing').slideUp(500, function(){
        $(this).html('');
    });
}
function SPagesEnable(){
    SPagesLocks--;
    SPagesButtons();
}
function SPagesDisable(){
    SPagesLocks++;
    SPagesButtons();
}
function SPagesButtons(){
    if(SPagesLocks==0){
        $("#simple .position").sortable("enable");
        $("#simple .symbionts .widget").draggable("enable");
    }
    else{
        $("#simple .position").sortable("disable");
        $("#simple .symbionts .widget").draggable("disable");
    }
}
function SPagesWidgetInfo(widget){
    $.ajax({
        'data':{
            'symbiont': 'Admin.ajax',
            'info': widget.find('.symbiont').html()
        },
        'success':function(r){
            widget.find('.title').html(r['title']);
        }
    });
}
*/