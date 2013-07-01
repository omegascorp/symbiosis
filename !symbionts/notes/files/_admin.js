$(document).ready(function(){
    if($('.symbionts-notes-admin .main.admin-selected').length){
        $('.symbionts-notes-admin .list').hide();
    }
    $('.symbionts-notes-admin .type .admin-radio').click(function(){
        if($(this).hasClass('main')){
            $('.symbionts-notes-admin .list').hide();
        }
        else{
            $('.symbionts-notes-admin .list').show();
        }
    });
    $('.admin-button-save').click(function(){
        var symbiont='Notes';
        var list=false;
        if($('.symbionts-notes-admin .main').hasClass('admin-selected')){
            symbiont+='';
        }
        if($('.symbionts-notes-admin .category').hasClass('admin-selected')){
            symbiont+='.category';
            list=true;
        }
        if($('.symbionts-notes-admin .categories').hasClass('admin-selected')){
            symbiont+='.categories';
            list=true;
        }
        if(list){
            symbiont+='.id='+$('.symbionts-notes-admin .list .admin-selected').attr('data-id');
        }
        if(symbiont=='Notes'){
            $.ajax({
                'data': {
                    'symbiont': 'Notes-Admin.dbDefault.id='+$('.symbiosis-page').attr('data-id')
                }
            });
        }
        else{
            AdminPageSave(symbiont);
        }
        $('.symbionts-notes-admin .default').removeClass('default');
        $('.symbionts-notes-admin .admin-selected').addClass('default');
    });
    $('.admin-button-reset').click(function(){
        $('.symbionts-notes-admin .admin-selected').removeClass('admin-selected');
        $('.symbionts-notes-admin .default').addClass('admin-selected');
    });
});