(function($){
    $.numerics=[];
    $.numericSet=function(label, variants){
        $.numerics[label]=variants;
    };
    $.numericSetRu=function(label, v1, v2, v3){
        $.numericSet(label,{
            '*%10==1&&*!=11':v1,
            '*%10>=2&&*%10<=4&&(*<11||*>19)':v2,
            'true':v3
        });
    }
    $.numericSetEn=function(label, v1, v2){
        $.numericSet(label,{
            '*==1':v1,
            'true':v2
        });
    }
    $.numericSetStatic=function(label, v1){
        $.numericSet(label,{
            'true':v1
        });
    }
    $.numericGet=function(label, numeric){
        for(key in $.numerics[label]){
            val=$.numerics[label][key];
            term=key.replace(/\*/g, numeric);
            if(eval(term)){
                return val.replace(/\*/g, numeric);
            }
        }
        return '';
    };
    $.fn.numeric=function(){
        $(this).each(function(){
            $(this).html($.numericGet($(this).attr('data-label'), $(this).attr('data-numeric'))); 
        });
    };
    $(document).ready(function(){
        $('.numeric').numeric();
    });
})(jQuery);