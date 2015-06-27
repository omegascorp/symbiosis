$(document).ready(function(){
    $('.symbionts-forms-edit .admin-button-save').unbind('click').click(function(){
        var edit=$(this).closest('.symbionts-forms-edit');
        var widget=$(this).closest('.admin-widget-tab');
        var uniq=edit.attr('data-uniq');
        var template=edit.find('.templates li.admin-selected').attr('data-name');
        var symbiont='#Forms.main.'+template+'[uniq="'+uniq+'"]';
        $.ajax({
            'data':{
                'symbiont': 'Forms.adminSave',
                'uniq': uniq,
                'emails': edit.find('input[name=email]').val()
            },
            'success': function(){
                SPageSave(symbiont);
            }
        });
    });
});