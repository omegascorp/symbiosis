$(document).ready(function(){
    $('.symbionts-forms-edit ul li').click(function(){
        $(this).parent().find('li').removeClass('admin-selected');
        $(this).addClass('admin-selected');
    });
    $('.symbionts-forms-edit .admin-button-save').unbind('click').click(function(){
        var edit=$(this).closest('.symbionts-forms-edit');
        var widget=$(this).closest('.widget');
        var uniq=edit.attr('data-uniq');
        var template='form-'+edit.find('.templates li.admin-selected').attr('data-name');
        var symbiont='Forms.main.uniq="'+uniq+'"|'+template;
        edit.closest('.widget').find('.symbiont').html(symbiont);
        $.ajax({
            'data':{
                'symbiont': 'Forms.editSave',
                'uniq': uniq,
                'emails': edit.find('.emails input').val()
            },
            'success': function(){
                SPagesWidgetInfo(widget);
            }
        });
        SPagesSave();
        SPagesWidgetClose(widget);
    });
    $('.symbionts-forms-edit .admin-button-cancel').unbind('click').click(function(){
        SPagesWidgetClose($(this).closest('.widget'));
    });
});