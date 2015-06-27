var SFilemanagerFiles=[];
var SFilemanagerStartTime=0;
var SFilemanagerStopTime=0;
var SFilemanagerGear=null;
var SFilemanagerName=null;
var SFilemanagerIsDeletable=true;
$(document).ready(function(){
    $('.SFilemanager .path input').change(function(){
        SFilemanagetRead();
    });
    
    $('.SFilemanager .buttons .upload')
    .button({icons:{primary:"ui-icon-folder-open"}})
    .click(function(){
        if($('.SFilemanager .subs .upload').css('display')=='none'){
            $('.SFilemanager .subs .upload').slideDown();
            $('.SFilemanager .subs .sub:not(.upload)').slideUp();
        }
        else{
            $('.SFilemanager .subs .upload').slideUp();
        }
    });
    
    $('.SFilemanager .buttons .folder')
    .button({icons:{primary:"ui-icon-folder-collapsed"}})
    .click(function(){
        if($('.SFilemanager .subs .folder').css('display')=='none'){
            $('.SFilemanager .subs .folder').slideDown();
            $('.SFilemanager .subs .sub:not(.folder)').slideUp();
        }
        else{
            $('.SFilemanager .subs .folder').slideUp();
        }
    });
    
    $('.SFilemanager .buttons .delete').button({icons:{primary:"ui-icon-closethick"}})
    .click(function(){
        if($('.SFilemanager .files .file.ui-selected').length==0) return;
        if($('.SFilemanager .subs .delete').css('display')=='none'){
            $('.SFilemanager .subs .delete').slideDown();
            $('.SFilemanager .subs .sub:not(.delete)').slideUp();
        }
        else{
            $('.SFilemanager .subs .delete').slideUp();
        }
    });
    $('.SFilemanager .buttons .refresh')
    .button({icons:{primary:"ui-icon-refresh"}})
    .click(function(){
        SFilemanagetRead();
    });
    
    $('.SFilemanager .buttons .top').button({icons:{primary:"ui-icon-arrowreturnthick-1-n"}})
    .click(function(){
        var path=$('.SFilemanager .path input').val();
        var index=path.lastIndexOf('\\');
        if(index==-1){
            index=path.lastIndexOf('/');
        }
        $('.SFilemanager .path input').val(path.substr(index));
        SFilemanagetRead();
    });
    
    $('.SFilemanager .buttons .hidden').button({icons:{primary:"ui-icon-star"}})
    .click(function(){
        SFilemanagetRead();
    });
    
    
    $('.SFilemanager .buttons .size').button().click(function(){
        SFilemanagetRead();
    });
    
    $('.SFilemanager .buttons .name').button().click(function(){
        if($(this).prev().find('.asc').css('display')=='none'){
            $(this).prev().find('.asc').show();
            $(this).prev().find('.desc').hide();
            $(this).parent().find('.date').prev().find('.asc, .desc').hide();
        }
        else{
            $(this).prev().find('.asc').hide();
            $(this).prev().find('.desc').show();
            $(this).parent().find('.date').prev().find('.asc, .desc').hide();
        }
        SFilemanagerPrint();
    });
    $('.SFilemanager .buttons .date').button().click(function(){
        if($(this).prev().find('.asc').css('display')=='none'){
            $(this).prev().find('.asc').show();
            $(this).prev().find('.desc').hide();
            $(this).parent().find('.name').prev().find('.asc, .desc').hide();
        }
        else{
            $(this).prev().find('.asc').hide();
            $(this).prev().find('.desc').show();
            $(this).parent().find('.name').prev().find('.asc, .desc').hide();
        }
        SFilemanagerPrint();
    });
    $('.SFilemanager .buttons .ready').button();
    $('.SFilemanager .buttons .cancel').button();
    
    $('.SFilemanager .filter').keyup(function(){
        var filter=$(this).val();
        filter=filter.replace(/([\(\)\[\]\+])/g, '\\$1');
        filter=filter.replace(/\./g, '\\.');
        filter=filter.replace(/\*/g, '.*');
        filter=new RegExp(filter);
        $('.SFilemanager .files .file').each(function(){
            var name=$(this).find('.name').html();
            if(filter.test(name)){
                $(this).show();
            }
            else{
                $(this).hide();
            }
        });
    });
    
    $('.SFilemanager .buttons .buttonset').buttonset();
    
    $('.SFilemanager .subs .upload .browse')
    .button()
    .removeClass('ui-corner-all')
    .click(function(){
        $(this).closest('.upload').find('.file').click();
    });
    $('.SFilemanager .subs .upload .filename').click(function(){
        if($(this).val()=='') $(this).closest('.upload').find('.file').click();
    })
    .change(function(){
        SFilemanagerFileExists();
    });
    
    $('.SFilemanager .subs .upload .file').change(function(){
        var filename=$(this).val();
        var index=filename.lastIndexOf('\\');
        if(index==-1){
            index=filename.lastIndexOf('/');
        }
        $(this).closest('.upload').find('.filename').val(filename.substr(index+1));
        SFilemanagerFileExists();
    });
    
    $('.SFilemanager .subs .upload .ready')
    .button().removeClass('ui-corner-all').addClass('ui-corner-right')
    .click(function(){
        $.lupload({
            url: $.symbiosis.ajax,            
            element: '.SFilemanager .upload .file',
            data:{
                'symbiont':'Filemanager.upload',
                'input': 'file',
                'path':$('.SFilemanager .path input').val(),
                'name':$('.SFilemanager .subs .upload .filename').val()
            },
            success: function (r, status){
                SFilemanagetRead();
                $('.SFilemanager .subs .upload').slideUp();
                $('.SFilemanager .subs .upload .filename').removeClass('ui-state-highlight').removeClass('ui-state-error').val('');
            }
        });
    });
    
    $('.SFilemanager .subs .folder .create')
    .button()
    .removeClass('ui-corner-all').addClass('ui-corner-right')
    .click(function(){
        $.ajax({
            'data': {
                'symbiont': 'Filemanager.createFolder',
                'path': $('.SFilemanager .path input').val(),
                'name': $('.SFilemanager .subs .folder input').val()
            },
            'success': function(result){
                if(result.success!=undefined){
                    SFilemanagetRead();
                    $('.SFilemanager .buttons .folder').click();
                }
                else if(result.error!=undefined){
                    
                }
            }
        });
    });
    
    
    $('.SFilemanager .subs .delete .shure')
    .button()
    .removeClass('ui-corner-all')
    .addClass('ui-corner-bl')
    .click(function(){
        SFilemanagerDelete();
    });
    $('.SFilemanager .subs .delete .cancel')
    .button()
    .removeClass('ui-corner-all')
    .addClass('ui-corner-right')
    .click(function(){
        $('.SFilemanager .buttons .delete').click();
    });
    
    
    
    $('.SFilemanager .files').selectable({
        filter: '.file',
        selected: function(event, ui){
            $(this).find('.ui-selected').addClass('ui-state-hover');
        },
        unselected: function(){
            $(this).find('.file:not(.ui-selected)').removeClass('ui-state-hover');
        },
        start: function(){
            var time=new Date().getTime();
            SFilemanagerStartTime=time;
        },
        stop: function(event, ui){
            var time=new Date().getTime();
            if(SFilemanagerGear&&SFilemanagerStartTime&&time-SFilemanagerStartTime<150){
                SFilemanagerGear.hide();
                var file=SFilemanagerGear.closest('.file');
                SFilemanagerLock();
                var filesRight=$('.SFilemanager .files').width()+$('.SFilemanager .files').offset().left;
                var optionsRight=314+file.offset().left;
                var tmp=$('<div class="config ui-state-default ui-corner-all"></div>');
                var t=$('.SFilemanager');
                var abs=false;
                while(t.filter('body').length==0){
                    if(t.css('position')=='absolute'){
                        abs=true;
                        break;
                    }
                    t=t.parent();
                }
                var globalLeft=abs?$('.SFilemanager').offset().left-5:0;
                var globalTop=abs?$('.SFilemanager').offset().top-5:0;
                tmp.css({
                    'float': 'left',
                    'width': file.css('width'),
                    'height': file.css('height'),
                    'position': 'absolute',
                    'left': file.offset().left-globalLeft,
                    'top': file.offset().top-globalTop,
                    'z-index': 2
                });
                if(optionsRight>filesRight){
                    tmp.css({
                        'left': (filesRight-314)+'px'
                    });
                }
                tmp.animate({
                    'width': '310px',
                    'height': '182px'
                }, 250, function(){
                    tmp.append($('.SFilemanager .template .options').clone());
                    tmp.find('input').attr('disabled', false);
                    tmp.find('.buttons input').button();
                    options=SFilemanagerFiles[file.find('.key').html()];
                    tmp.find('.name input').val(options.name);
                    if(options.config!=undefined){
                        tmp.find('.maxsize input').val(options.config.maxsize);
                        types='';
                        for(key in options.config.types){
                            val=options.config.types[key];
                            if(key!=0){
                                types+=', ';
                            }
                            types+=val;
                        }
                        tmp.find('.types input').val(types);
                        tmp.find('.view option').each(function(){
                            if($(this).val()==options.config.view.accessLevel){
                                $(this).attr('selected', true);
                            }
                        });
                        tmp.find('.change option').each(function(){
                            if($(this).val()==options.config.change.accessLevel){
                                $(this).attr('selected', true);
                            }
                        });
                    }
                    tmp.find('.buttons .save').click(function(){
                        $.ajax({
                            'data':{
                                'symbiont': 'Filemanager.fileConfig',
                                'path': $('.SFilemanager .path input').val(),
                                'name': file.find('.name').html(),
                                'nameNew': file.find('.name').html()!=tmp.find('.name input').val()?tmp.find('.name input').val():undefined,
                                'view': tmp.find('.view select').val(),
                                'change': tmp.find('.change select').val(),
                                'maxsize': tmp.find('.maxsize input').val(),
                                'types': tmp.find('.types input').val()
                            },
                            'success':function(r){
                                tmp.remove();
                                SFilemanagerUnlock();
                                SFilemanagetRead();
                            }
                        });
                    });
                    tmp.find('.buttons .close').click(function(){
                        tmp.remove();
                        SFilemanagerUnlock();
                    });
                });
                $('.SFilemanager').append(tmp);
            }
            
            if(SFilemanagerName!=null&&SFilemanagerStopTime&&time-SFilemanagerStopTime>300&&time-SFilemanagerStopTime<600){
                input=$('<input class="name ui-corner-all" type="text" value="'+SFilemanagerName.html()+'" />');
                SFilemanagerIsDeletable=false;
                input.blur(function(){
                    if($(this).next().html()!=$(this).val()){
                        $.ajax({
                            'data':{
                                'symbiont': 'Filemanager.fileRename',
                                'path': $('.SFilemanager .path input').val(),
                                'name': $(this).next().html(),
                                'nameNew': $(this).val()
                            },
                            'success':function(r){
                                input.next().html(input.val()).show();
                                input.remove();
                                SFilemanagerIsDeletable=true;
                            }
                        });
                    }
                    else{
                        input.next().show();
                        input.remove();
                        SFilemanagerIsDeletable=true;
                    }
                })
                .lkey('enter', function(){
                    $(this).blur();
                });
                SFilemanagerName.before(input);
                SFilemanagerName.hide();
                input.focus();
            }
            
            if(SFilemanagerStopTime&&time-SFilemanagerStopTime<300){
                $(this).find('.ui-selected').each(function(){
                    var type=$(this).find('.type').html();
                    if(type=='folder'){
                        $('.SFilemanager .path input').val($('.SFilemanager .path input').val()+$(this).find('.name').html()+'/');
                        SFilemanagetRead();
                    }
                    else if(type=='jpg'||type=='jpeg'||type=='png'||type=='gif'||type=='bmp'){
                        var src='uploads/'+$('.SFilemanager .path input').val()+$(this).find('.name').html();
                        var obj=new Image();
                        obj.onload=function(){
                            var img=$('<img src="'+src+'">');
                            $.adminDialog(img, {
                                'x': 'center',
                                'y': 'middle',
                                'z': 110
                            });
                        }
                        obj.src=src;
                    }
                    else{
                        $.ajax({
                            'data':{
                                'symbiont': 'Filemanager.fileRead',
                                'path': $('.SFilemanager .path input').val()+$(this).find('.name').html()
                            },
                            'dataType': 'html',
                            'success': function(r){
                                if(!r) return;
                                var content=$(r);
                                content.adminUI();
                                $.adminDialog(content, {
                                    'x': 'center',
                                    'y': 'middle',
                                    'z': 110
                                });
                            }
                        });
                    }
                    return false;
                });
            }
            SFilemanagerStopTime=time;
        }
    });
    
    SFilemanagetRead();
    SFilemanagerResize();
    $(document).bind('symbiontsHide', function(){
        SFilemanagerResize();
    })
    .bind('symbiontsShow', function(){
        SFilemanagerResize();
    });
});
$.lkey('del', function(e){
    if(SFilemanagerIsDeletable) SFilemanagerDelete();
});
$(window).resize(function(){
    SFilemanagerResize();
});
function SFilemanagerDelete(){
    var files=[];
    $('.SFilemanager .files .file').each(function(){
        if($(this).hasClass('ui-selected')) files.push($(this).find('.name').html());
    });
    $.ajax({
        'data': {
            'symbiont': 'Filemanager.delete',
            'path': $('.SFilemanager .path input').val(),
            'files': files
        },
        'success': function(result){
            if(result.success!=undefined){
                $('.SFilemanager .subs .delete').slideUp();
                SFilemanagetRead();
            }
            else if(result.error!=undefined){
                $('.SFilemanager .subs .delete').slideUp();
                SFilemanagetRead();
            }
        }
    });
}
function SFilemanagetRead(){
    $.ajax({
        'data': {
            'symbiont': 'Filemanager.read',
            'path': $('.SFilemanager .path input').val(),
            'size': $('.SFilemanager .size:checked').val(),zz
            'hidden': $('.SFilemanager .hidden:checked').length?true:false
        },
        'success': function(result){
            if(result.files!=undefined){
                SFilemanagerFiles=result.files;
                SFilemanagerPrint();
            }
            else if(result.error){
                var path=$('.SFilemanager .path input').val();
                path=path.substring(0, path.length-1);
                var index=path.lastIndexOf('\\');
                if(index==-1){
                    index=path.lastIndexOf('/');
                }
                $('.SFilemanager .path input').val(path.substring(0, index+1));
                SFilemanagetRead();
            }
        }
    });
}
function SFilemanagerPrint(){
    var order=$('.SFilemanager .buttons .order:checked').val();
    var button=$('.SFilemanager .buttons .order:checked');
    if(order=='name'){
        if(button.prev().find('.desc').css('display')!='none'){
            SFilemanagerFiles.sort(SFilemanagetSortByNameDesc);
        }
        else{
            SFilemanagerFiles.sort(SFilemanagetSortByNameAsc);
        }
    }
    else{
        if(button.prev().find('.desc').css('display')!='none'){
            SFilemanagerFiles.sort(SFilemanagetSortByDateDesc);
        }
        else{
            SFilemanagerFiles.sort(SFilemanagetSortByDateAsc);
        }
    }
    
    var files=$('.SFilemanager .files');
    files.html('');
    for(key in SFilemanagerFiles){
        file=SFilemanagerFiles[key];
        clone=$('.SFilemanager .template .file').clone(false);
        clone.find('img').attr('src', file.icon);
        clone.find('.name').html(file.name);
        clone.find('.type').html(file.type);
        clone.find('.key').html(key);
        clone.addClass('file'+$('.SFilemanager .size:checked').val());
        clone.hover(function(){
            if($(this).find('.type').html()=='folder'){
                $(this).find('.gear').show();
            }
        },function(){
            if($(this).find('.type').html()=='folder'){
                $(this).find('.gear').hide();
            }
        });
        clone.find('.gear').hover(function(){
            SFilemanagerGear=$(this);
        },function(){
            SFilemanagerGear=null;
        });
        clone.find('.name').hover(function(){
            SFilemanagerName=$(this);
        },function(){
            SFilemanagerName=null;
        });
        if(file.type=='folder'){
            if(file.config==undefined){
                clone.addClass('ui-state-error');
            }
        }
        files.append(clone);
    }
}
function SFilemanagerFileExists(){
    $.ajax({
        'data': {
            'symbiont': 'Filemanager.fileExists',
            'path': $('.SFilemanager .path input').val()+$('.SFilemanager .subs .upload .filename').val()
        },
        'success': function(result){
            if(result.exists){
                $('.SFilemanager .subs .upload .filename').removeClass('ui-state-highlight').addClass('ui-state-error');
            }
            else{
                $('.SFilemanager .subs .upload .filename').removeClass('ui-state-error').addClass('ui-state-highlight');
            }
        }
    });
}
function SFilemanagerResize(){
    var pleft;
    var ptop;
    $('.SFilemanager .sub').each(function(){
        pleft=$('.SFilemanager .buttons .'+$(this).attr('for')).position().left;
        ptop=$('.SFilemanager .buttons .'+$(this).attr('for')).position().top+$('.SFilemanager .buttons .'+$(this).attr('for')).height();
        $(this)
        .css({
            'left': pleft+'px',
            'top': ptop+'px'
        });
    });
    
    
    var file=$('.SFilemanager .config').next();
    if(file.length){
        $('.SFilemanager .config').css({
            'left': file.offset().left,
            'top': file.offset().top
        });
        var filesRight=$('.SFilemanager .files').width()+$('.SFilemanager .files').offset().left;
        var optionsRight=314+file.offset().left;
        if(optionsRight>filesRight){
            $('.SFilemanager .config').css({
                'left': (filesRight-314)+'px'
            });
        }
    }
    
    $('.SFilemanager .lock').css({
        'width': $('.SFilemanager').width()+'px',
        'height': $('.SFilemanager').height()+'px',
        'left': 'auto',
        'top': 'auto'
    });
    
    
}
function SFilemanagerLock(){
    $('.SFilemanager .lock').css({
        'width': $('.SFilemanager').width()+'px',
        'height': $('.SFilemanager').height()+'px',
        'left': 'auto',
        'top': 'auto'
    }).fadeIn();
    $('.SFilemanager input').attr('disabled', true);
    $('.SFilemanager .files').selectable('disable');
}
function SFilemanagerUnlock(){
    $('.SFilemanager .lock').fadeOut();
    $('.SFilemanager input').attr('disabled', false);
    $('.SFilemanager .files').selectable('enable');
}
function SFilemanagetSortByNameAsc(i, j){
    if(i.type=='folder'&&j.type!='folder'){
        return -1;
    }
    if(j.type=='folder'&&i.type!='folder'){
        return 1;
    }
    if(i.name>j.name){
        return 1;
    }
    if(i.name<j.name){
        return -1;
    }
    return 2;
}
function SFilemanagetSortByNameDesc(i, j){
    if(i.type=='folder'&&j.type!='folder'){
        return -1;
    }
    if(j.type=='folder'&&i.type!='folder'){
        return 1;
    }
    if(i.name<j.name){
        return 1;
    }
    if(i.name>j.name){
        return -1;
    }
    return 2;
}
function SFilemanagetSortByDateAsc(i, j){
    if(i.type=='folder'&&j.type!='folder'){
        return -1;
    }
    if(j.type=='folder'&&i.type!='folder'){
        return 1;
    }
    if(i.date>j.date){
        return 1;
    }
    if(i.date<j.date){
        return -1;
    }
    return 2;
}
function SFilemanagetSortByDateDesc(i, j){
    if(i.type=='folder'&&j.type!='folder'){
        return -1;
    }
    if(j.type=='folder'&&i.type!='folder'){
        return 1;
    }
    if(i.date<j.date){
        return 1;
    }
    if(i.date>j.date){
        return -1;
    }
    return 2;
}