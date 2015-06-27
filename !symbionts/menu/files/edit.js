$(document).ready(function(){
    $('.symbionts-menu-edit li').click(function(){
        $(this).parent().find('.admin-selected').removeClass('admin-selected');
        $(this).addClass('admin-selected');
    });
    $('.symbionts-menu-edit .admin-button-save').unbind('click').click(function(){
        var edit=$(this).closest('.symbionts-menu-edit');
        
        var id=parseInt(edit.find('ul li.admin-selected').attr('data-id'));
        var symbiont='Menu.main';
        if(id) symbiont+='.id='+id;
        edit.closest('.widget').find('.symbiont').html(symbiont);
        SPagesSave();
        SPagesWidgetClose($(this).closest('.widget'));
        SPagesWidgetInfo($(this).closest('.widget'));
    });
    $('.symbionts-menu-edit .admin-button-cancel').unbind('click').click(function(){
        SPagesWidgetClose($(this).closest('.widget'));
    });
});