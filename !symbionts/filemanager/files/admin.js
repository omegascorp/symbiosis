var SFilemanager={};
var SFilemanagetSelection=false;
$(document).ready(function(){
    SFilemanagerRead();
    $('.symbionts-filemanager .selection').click(function(){
        SFilemanagetSelection=true;
        $('.symbionts-filemanager .selection').hide();
        $('.symbionts-filemanager').find('.selection-remove,.selection-all').show();
    });
    $('.symbionts-filemanager .refresh').click(function(){
        SFilemanagerRead();
    });
    $('.symbionts-filemanager .selection-remove').click(function(){
        SFilemanagetSelection=false;
        $('.symbionts-filemanager .selection').show();
        $('.symbionts-filemanager').find('.selection-remove,.selection-all,.delete').hide();
        $('.symbionts-filemanager .files .selected').removeClass('selected');
    });
    $('.symbionts-filemanager .selection-all').click(function(){
        $('.symbionts-filemanager .files .file').addClass('selected');
        $(this).hide();
        $('.symbionts-filemanager .delete').show();
    });
    $('.symbionts-filemanager .delete').click(function(){
        SFilemanagerDelete();
    });
    $('.symbionts-filemanager .top').click(function(){
        var path=$('.symbionts-filemanager .path input').val();
        path=path.substring(0, path.length-1);
        var index=path.lastIndexOf('\\');
        if(index==-1){
            index=path.lastIndexOf('/');
        }
        if(path){
            $('.symbionts-filemanager .path input').val(path.substring(0, index+1));
            SFilemanagerRead();
        }
    });
    $('.symbionts-filemanager .settings').click(function(){
        SFilemanagerSettings();
    });
    $('.symbionts-filemanager .upload').click(function(){
        SFilemanagerUpload();
    });
    $('.symbionts-filemanager .path input').keydown(function(e){
        if(e.keyCode=='13') {
            if($(this).val()==''){
                $(this).val('/');
            }
            SFilemanagerRead();
        }
    });
    $('.symbionts-filemanager .create').click(function(){
        SFilemanagerCreate();
    });
});
function SFilemanagerRead(func){
    $.ajax({
        'data': {
            'symbiont': 'Filemanager.read',
            'path': $('.symbionts-filemanager .path input').val()
        },
        'success': function(result){
            if(result.files!=undefined){
                SFilemanager=result;
                SFilemanagerPrint(func);
            }
            else if(result.error){
                var path=$('.symbionts-filemanager .path input').val();
                path=path.substring(0, path.length-1);
                var index=path.lastIndexOf('\\');
                if(index==-1){
                    index=path.lastIndexOf('/');
                }
                SFilemanager=result;
                //$('.symbionts-filemanager .path input').val(path.substring(0, index+1));
                SFilemanagerPrint();
                if(result.errorCode==0){
                    SFilemanagerSettings();
                }
            }
            Admin.step();
        }
    });
}
function SFilemanagerSettings(name){
    $template=$('.symbionts-filemanager .templates .symbionts-filemanager-settings').clone(true);
    if(SFilemanager.name) $template.find('.name').val(SFilemanager.name);
    else $template.find('.name').parent().hide();
    if(SFilemanager.config!=undefined){
        $template.find('.view').get(0).selectedIndex=$template.find('.view [value='+SFilemanager.config.view.accessLevel+']').index();
        $template.find('.change').get(0).selectedIndex=$template.find('.change [value='+SFilemanager.config.change.accessLevel+']').index();
        $template.find('.maxsize').val(SFilemanager.config.maxsize);
        var types=SFilemanager.config.types;
        var joined='';
        if(types!=undefined){
            for(key in types) {
                type=types[key];
                if(type=='#images'){
                    $template.find('.images').addClass('admin-selected');
                }
                else if(type=='#video'){
                    $template.find('.video').addClass('admin-selected');
                }
                else if(type=='#archives'){
                    $template.find('.archives').addClass('admin-selected');
                }
                else if(type=='#sounds'){
                    $template.find('.sounds').addClass('admin-selected');
                }
                else if(type=='#documents'){
                    $template.find('.documents').addClass('admin-selected');
                }
                else{
                    joined+=joined?',':'';
                    joined+=type;
                }
            }
            $template.find('.types').val(joined);
        }
    }
    var popup=new AdminLayerPopup({
        'content': $template
    });
    Admin.init(popup.options.element);
    var element=popup.options.element;
    element.find('.admin-button-save').click($.proxy(function(){
        var element=this.options.element;
        var types='';
        if(element.find('.images').hasClass('admin-selected')){ types+='#images,'; }
        if(element.find('.video').hasClass('admin-selected')){ types+='#video,'; }
        if(element.find('.archives').hasClass('admin-selected')){ types+='#archives,'; }
        if(element.find('.sounds').hasClass('admin-selected')){ types+='#sounds,'; }
        if(element.find('.documents').hasClass('admin-selected')){ types+='#documents,'; }
        types+=element.find('.types').val();
        $.ajax({
            'data':{
                'symbiont': 'Filemanager.fileConfig',
                'path': $('.symbionts-filemanager .path input').val(),
                'name': element.find('.name').val(),
                'view': element.find('.view').val(),
                'change': element.find('.change').val(),
                'maxsize': element.find('.maxsize').val(),
                'types': types
            },
            'success':$.proxy(function(r){
                this.remove();
                SFilemanagerRead();
            }, this)
        });
    }, popup));
    element.find('.admin-button-cancel').click($.proxy(function(){
        this.remove();
    }, popup));
}
function SFilemanagerCreate(){
    $template=$('.symbionts-filemanager .templates .symbionts-filemanager-create').clone(true);
    var popup=new AdminLayerPopup({
        'content': $template
    });
    Admin.init(popup.options.element);
    var element=popup.options.element;
    element.find('.admin-button-save').click($.proxy(function(){
        var element=this.options.element;
        $.ajax({
            'data':{
                'symbiont': 'Filemanager.createFolder',
                'path': $('.symbionts-filemanager .path input').val(),
                'name': element.find('.name').val()
            },
            'success':$.proxy(function(r){
                this.remove();
                SFilemanagerRead();
            }, this)
        });
    }, popup));
    element.find('.admin-button-cancel').click($.proxy(function(){
        this.remove();
    }, popup));
}
function SFilemanagerUpload(){
    $template=$('.symbionts-filemanager .templates .symbionts-filemanager-upload').clone(true);
    var popup=new AdminLayerPopup({
        'content': $template
    });
    Admin.init(popup.options.element);
    var element=popup.options.element;
    element.get(0).ondragover = function(e) {
        $(e.target).addClass('grag');
        return false;
    };
    element.get(0).ondragleave = function(e) {
        $(e.target).removeClass('grag');
        return false;
    };
    element.get(0).ondrop = $.proxy(function(e) {
        e.preventDefault();
        $(e.target).removeClass('grag');
        $(e.target).addClass('drop');
        var file = e.dataTransfer.files[0];
        /*
        if (file.size > maxFileSize) {
            //dropZone.text('Файл слишком большой!');
            $(e.target).addClass('error');
            return false;
        }
        */
        var formData = new FormData();
        var xhr = new XMLHttpRequest();
        //xhr.upload.addEventListener('progress', $.proxy(uploadProgress, {element:e.target, xhr:xhr}), false);
        xhr.onreadystatechange = $.proxy(stateChange, {element:$(e.target), xhr:xhr, popup:this});
        xhr.open('POST', $.symbiosis.ajax);
        formData.append('symbiont', 'Filemanager.upload');
        formData.append('file', file);
        formData.append('input', 'file');
        formData.append('overwrite', false);
        formData.append('path', $('.symbionts-filemanager .path input').val());
        //xhr.setRequestHeader('X-FILE-NAME', file.name);
        xhr.send(formData);        
        return true;
    }, popup);
    function stateChange(e){
        if(e.target.readyState == 4){
            var json=JSON.parse(this.xhr.responseText);
            if(json.errorId!=0){
                this.element.addClass('error');
                this.element.html(json.error);
            }
            else{
                this.popup.remove();
                SFilemanagerRead();
            }
        }
    }
}
function SFilemanagerDelete(){
    $template=$('.symbionts-filemanager .templates .symbionts-filemanager-delete').clone(true);
    var popup=new AdminLayerPopup({
        'content': $template
    });
    Admin.init(popup.options.element);
    var element=popup.options.element;
    element.find('.admin-button-yes').click($.proxy(function(){
        var element=this.options.element;
        var files=[];
        $('.symbionts-filemanager .files .selected').each(function(){
            files.push($(this).attr('data-name'));
        });
        $.ajax({
            'data':{
                'symbiont': 'Filemanager.delete',
                'path': $('.symbionts-filemanager .path input').val(),
                'files': files
            },
            'success':$.proxy(function(r){
                this.remove();
                SFilemanagerRead();
            }, this)
        });
    }, popup));
    element.find('.admin-button-no').click($.proxy(function(){
        this.remove();
    }, popup));
}
function SFilemanagerPrint(func){
    /*
    var order=$('.SFilemanager .buttons .order:checked').val();
    var button=$('.SFilemanager .buttons .order:checked');
    if(order=='name'){
        if(button.prev().find('.desc').css('display')!='none'){
            SFilemanager.files.sort(SFilemanagetSortByNameDesc);
        }
        else{
            SFilemanager.files.sort(SFilemanagetSortByNameAsc);
        }
    }
    else{
        if(button.prev().find('.desc').css('display')!='none'){
            SFilemanager.files.sort(SFilemanagetSortByDateDesc);
        }
        else{
            SFilemanager.files.sort(SFilemanagetSortByDateAsc);
        }
    }
    */
    
    
    
    var files=$('.symbionts-filemanager .files');
    files.html('');
    if (SFilemanager.files==undefined) {
        return;
    }
    SFilemanager.files.sort(SFilemanagetSortByNameAsc);
    for(key in SFilemanager.files){
        file=SFilemanager.files[key];
        var $file=$('<div class="file icon-'+file.type+'" data-name="'+file.name+'"></div>');
        if(file.config==undefined){
            $file.addClass('error');
        }
        var name='';
        if(file.type=='folder'){
            if(file.name.length>30){
                var size=10;
                name=file.name.substr(0,15)+' '+file.name.substr(15, 15)+' '+file.name.substr(30, 15);
            }
            else if(file.name.length>15){
                var size=12;
                name=file.name.substr(0,10)+' '+file.name.substr(10);
            }
            else{
                var size=14;
                name=file.name;
            }
            $file.append('<div class="name" style="font-size: '+size+'px;">'+name+'</div>');
        }
        else if(file.type=='image'){
            $file.append('<img src="'+file.icon+'" />');
        }
        
        $file.click(function(e){
            if(e.ctrlKey||SFilemanagetSelection){
                //$(this).closest('.files').find('.file').not(this).removeClass('selected');
                if($(this).hasClass('selected')){
                    $(this).removeClass('selected');
                }
                else{
                    $(this).addClass('selected');
                }
                var all=$('.symbionts-filemanager .files .file').length;
                var selected=$('.symbionts-filemanager .files .selected').length;
                if(all==selected){
                    $('.symbionts-filemanager .selection-all').hide();
                }
                else{
                    $('.symbionts-filemanager .selection-all').show();
                }
                if(selected){
                    $('.symbionts-filemanager .delete').show();
                }
                else{
                    $('.symbionts-filemanager .delete').hide();
                }
            }
            else{
                if($(this).hasClass('icon-folder')) {
                    $('.symbionts-filemanager .path input').val($('.symbionts-filemanager .path input').val()+$(this).attr('data-name')+'/');
                    SFilemanagerRead();
                }
            }
        });
        files.append($file);
    }
    
    if(typeof(func)=='function'){
        func();
    }
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