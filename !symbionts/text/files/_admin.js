$(document).ready(function(){
    $('.symbionts-text-admin .lightedit').lightedit({
        'top-var':'Admin.top',
        'language': $.symbiosis.language,
        'autosave': 30000,
        'path': '!files/lightedit/',
        'save': function(){
            $('.symbionts-text-admin .saveAsDraft').click();
            return true;
        }
    });
    $('.symbionts-text-admin .approve, .symbionts-text-admin .saveAsDraft').click(function(){
        var data=$('.symbionts-text-admin .lightedit').lighteditGet();
        data['symbiont']='Text-Admin.dbSave';
        data['id']=$('.symbionts-text-admin').attr('data-id');
        if($(this).hasClass('saveAsDraft')) data['draft']=true;
        $('.symbionts-text-admin .lightedit').find('.lightedit-saved').fadeIn();
        $.ajax({
            'data': data,
            'success': $.proxy(function(r){
                $('.symbionts-text-admin .lightedit').find('.lightedit-saved').fadeOut();
            }, this)
        });
    });
    $('.symbionts-text-admin .loadOriginal, .symbionts-text-admin .loadDraft').click(function(){
        var data={};
        data['symbiont']='Text-Admin.dbRead';
        data['id']=$('.symbionts-text-admin').attr('data-id');
        if($(this).hasClass('loadDraft')) data['draft']=true;
        $.ajax({
            'data': data,
            'success': $.proxy(function(r){
                $('.symbionts-text-admin .lightedit').lighteditSet(r);
            }, this)
        });
    });
});