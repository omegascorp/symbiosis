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
        var symbiont='#Notes';
        var attrs='';
        var list=false;
        var main=false;
        if($('.symbionts-notes-admin .main').hasClass('admin-selected')){
            symbiont+='.main';
            main=true;
        }
        if($('.symbionts-notes-admin .category').hasClass('admin-selected')){
            symbiont+='.pageCategory';
            list=true;
        }
        if($('.symbionts-notes-admin .categories').hasClass('admin-selected')){
            symbiont+='.pageCategories';
            list=true;
        }
        
        if(list){
            attrs+='id='+$('.symbionts-notes-admin .list .admin-selected').attr('data-id');
        }
        
        if($('.symbionts-notes-admin .templates .admin-selected').length) symbiont+='.'+$('.symbionts-notes-admin .templates .admin-selected').text();
        
        if(attrs) symbiont+='['+attrs+']';
        
        SPageSave(symbiont);
        if(main){
            $.ajax({
                'data': {
                    'symbiont': 'Notes-Admin.dbDefault.id='+$('.symbiosis-page').attr('data-id')
                }
            });
        }
        $('.symbionts-notes-admin .default').removeClass('default');
        $('.symbionts-notes-admin .admin-selected').addClass('default');
    });
    $('.admin-button-reset').click(function(){
        $('.symbionts-notes-admin .admin-selected').removeClass('admin-selected');
        $('.symbionts-notes-admin .default').addClass('admin-selected');
    });
});