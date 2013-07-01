$(document).ready(function(){
    $('.SAdminEdit .main').click(function(){
        var id=$('.SAdminEdit .id').html();
        $.ajax({
            'data':{
                'symbiont': 'Pages.setAsMain',
                'id': id,
                'main': 'Admin'
            },
            'success': function(result){
                $('.SAdminEdit .main').addClass('ui-state-highlight');
                $('.SAdminEdit .main .ui-button-text').html($('.SAdminEdit .label').html());
            }
        });
    });
});