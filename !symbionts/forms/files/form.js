$(document).ready(function(){
    $('.symbionts-forms form').submit(function(){
        var element=$(this).closest('.symbionts-forms');
        element.find('.message').removeClass('error').slideUp();
        $(this).find('input[type=submit]').attr('disabled', true);
        var data={};
        data['symbiont']='Forms.send';
        data['uniq']=element.attr('data-uniq');
        data['data']=[];
        $(this).find('.data').each(function(){
            var current={};
            current['type']=$(this).attr('data-type');
            var label=$(this).find('label').html();
            current['label']=label!=null?label:'';
            current['value']='';
            var input=$(this).find('input').val();
            var textarea=$(this).find('textarea').val();
            if(input!=undefined) current['value']=input;
            if(textarea!=undefined) current['value']=textarea;
            data['data'].push(current);
        });
        $.ajax({
            'data': data,
            'success': function(r){
                element.find('input[type=submit]').attr('disabled', false);
                if(r.error!=undefined){
                    element.find('.message').addClass('error').html(r.error).slideDown();
                    
                }
                else if(r.success!=undefined){
                    element.find('form').slideUp();
                    element.find('.message').addClass('success').html(r.success).slideDown();
                }
            }
        })
        return false;
    });
});