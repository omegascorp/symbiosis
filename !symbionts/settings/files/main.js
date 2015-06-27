$(document).ready(function(){
    $('.symbionts-settings .admin-button-save').click(function(){
        var data={};
        data['symbiont']='Settings.dbSave';
        data['settings']={};
        $('.symbionts-settings [data-alias]').each(function(){
            val='';
            if($(this).hasClass('admin-switch')){
                val=$(this).hasClass('admin-checked');
            }
            else if($(this).hasClass('admin-selectable')){
                val=$(this).find('.admin-selected').attr('data-value');
            }
            else if($(this).attr('type')=='text'){
                val=$(this).val();
            }
            data['settings'][$(this).attr('data-alias')]=val;
        });
        
        $.ajax({
            'data':data,
            'success':function(){
                
            }
        })
    });
    $('.symbionts-settings .admin-button-refresh').click(function(){
        location.reload();
    });
});