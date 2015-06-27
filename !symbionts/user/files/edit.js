$(document).ready(function(){
    $('.symbionts-user-edit .type .admin-button').click(function(){
        $(this).parent().find('.admin-button').removeClass('ui-state-highlight');
        $(this).addClass('ui-state-highlight');
    });
    $('.symbionts-user-edit .admin-button-save').unbind('click').click(function(){
        var edit=$(this).closest('.symbionts-user-edit');
        var symbiont='User';
        var button=edit.find('.type .admin-button.ui-state-highlight');
        if(button.hasClass('sign-up')){
            symbiont+='.signUp';
        }
        else if(button.hasClass('profile')){
            symbiont+='.profile';
        }
        edit.closest('.widget').find('.symbiont').html(symbiont);
        SPagesSave();
        SPagesWidgetClose($(this).closest('.widget'));
        SPagesWidgetInfo($(this).closest('.widget'));
    });
    $('.symbionts-user-edit .admin-button-cancel').unbind('click').click(function(){
        SPagesWidgetClose($(this).closest('.widget'));
    });
});