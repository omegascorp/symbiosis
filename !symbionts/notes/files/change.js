$(document).ready(function(){
    window.onbeforeunload = function(evt) {
        return " ";
    }
    
    $('.symbionts-notes-admin-change').find('.date').datetimepicker({
        'dateFormat': 'yy-mm-dd',
        'timeFormat': 'hh:mm:ss',
        'showSecond': true
    });
    $( "#datepicker" ).datepicker("option", $.datepicker.regional[$.symbiosis.code]);
    
    $('.symbionts-notes-admin-change').find('.lightedit').lightedit();
    $('.symbionts-notes-admin-change').find('.image').click(function(){
        var dialog=$.adminDialog($('.install-template').children().clone(), {x:'center', y:'middle', 'width': 600, 'height': 400});
        dialog.addClass('symbionts-notes-filemanager');
        $.ajax({
            'data': {
                'symbiont': 'Filemanager.mini.path="'+$('.symbionts-notes-admin-main').attr('data-path')+'"'
            },
            'dataType': 'html',
            'success': function(result){
                var div=$(result).adminUI();
                div.find('.files .file').click(function(){
                    var path=$(this).closest('.files').attr('data-path');
                    var name=$(this).attr('data-name');
                    var cover='uploads'+path+'.128/'+name;
                    var file=path+name;
                    $('.symbionts-notes-admin-change').find('.image img').attr('src', cover);
                    $('.symbionts-notes-admin-change').find('.image').attr('data-image', file);
                    $('.symbionts-notes-admin-change').find('.image').attr('data-changed', 'true');
                    $('.symbionts-notes-filemanager').remove();
                    return false;
                });
                $('.symbionts-notes-filemanager .ui-widget-content').append(div);
            }
        });
    });
});