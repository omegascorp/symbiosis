$(document).ready(function(){
    $('.symbionts-forms form').submit(function(){
        if(!$(this).validateForm()){
            return false;
        }
        var element=$(this).closest('.symbionts-forms');
        element.find('.message').removeClass('error').slideUp();
        $(this).find('input[type=submit]').attr('disabled', true);
        var data={};
        data['symbiont']='Forms.send';
        data['uniq']=element.attr('data-uniq');
        data['data']=[];
        $(this).find('input[type=text],textarea').each(function(){
            var current={};
            //current['type']=$(this).attr('data-type');
            var label=$(this).parent().find('label').html();
            var placeholder = $(this).attr('placeolder');
            current['label']= label || placeholder;
            current['value']=$(this).val();
            data['data'].push(current);
        });
        data['captcha']=element.find('.symbionts-captcha input').val();
        data['captchaUniq']=element.find('.symbionts-captcha').attr('data-uniq');
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