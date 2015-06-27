$(document).ready(function(){
    $('.symbionts-admin-symbionts .symbionts').sortable({
        placeholder: 'admin-widget-block-placeholder',
        update: function(e, ui){
            ui.item.mouseover();
            var sort=[];
            $('.symbionts-admin-symbionts .admin-widget-block').each(function(){
                sort.push($(this).find('.name').html());
            });
            $.ajax({
                data:{
                    'symbiont': 'admin.dbSort',
                    'sort': sort
                }
            });
        }
    });
    var SymbiontAdminOver=false;
    $('.symbionts-admin-symbionts .admin-widget-block').hover(function(){
        $('#admin-symbiont-title').html($(this).attr('data-title'));
        if($('#admin-symbiont-popup').css('display')=='none'){
            $('#admin-symbiont-popup').css({
                'top': $(this).offset().top+'px'
            });
        }
        else{
            $('#admin-symbiont-popup').animate({
                'top': $(this).offset().top+'px'
            }, {queue:false});
        }
        SymbiontAdminOver=true;
    },function(){
        SymbiontAdminOver=false;
    });
    setInterval(function(){
        if(SymbiontAdminOver){
            $('#admin-symbiont-popup').show();
        }
        else if(!SymbiontAdminOver){
            $('#admin-symbiont-popup').hide();
        }
    },100);
});