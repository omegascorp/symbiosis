$(document).ready(function(){
    $('.symbionts-admin .symbionts').sortable({
        placeholder: 'ui-state-default admin-widget-square admin-widget-square-128 ui-corner-all',
        update: function(){
            
            var sort=[];
            $('.symbionts-admin .admin-widget-square').each(function(){
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
    symbiontsAdminSymbiontsRemove();
});
function symbiontsAdminSymbiontsRemove(){
    $('#leftside').remove();
    $('#middle').addClass('full');
    $('#content>div').animate({'width': 'auto'});
}