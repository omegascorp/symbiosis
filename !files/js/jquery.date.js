(function($){
    $.dateNow=undefined;
    $.dateFormat='';
    $.dateMonths=['january','february','march','april','may','june','july','august','september','october','november','december'];
    $.dateNowSet=function(date){
        $.dateNow=new Date(date);
    };
    $.date=function(date){
        var now=$.dateNow;
        var d=new Date(date);
        if(now!=undefined){
            shtamp=now.getTime()-d.getTime();
            shtamp=Math.ceil(shtamp/1000);
            if(shtamp<=60){
                return $.numericGet('date-now', 0);
            }
            shtamp=Math.ceil(shtamp/60);
            if(shtamp<=60){
                return $.numericGet('date-minute', shtamp);
            }
            shtamp=Math.ceil(shtamp/60);
            if(shtamp<=60){
                return $.numericGet('date-hour', shtamp);
            }
            shtamp=Math.ceil(shtamp/24);
            if(shtamp<=6){
                return $.numericGet('date-day', shtamp);
            }
        }
        ret=d.getDate()+' '+$.dateMonths[d.getMonth()];
        if(d.getFullYear()!=now.getFullYear()){
            ret+=' '+d.getFullYear();
        }
        return ret;
    };
    $.fn.date=function(){
        $(this).each(function(){
            if($(this).attr('data-date')){
                $(this).html($.date($(this).attr('data-date')));
            }
        });
    };
    $(document).ready(function(){
        $('.date').date();
    });
    setInterval(function(){
        if($.dateNow!=undefined){
            $.dateNow.setTime($.dateNow.getTime()+60000);
            $('.date').date();
        }
    },60000);
})(jQuery);