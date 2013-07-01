//Symbiosis UI 0.0.8
(function ($) {
    $.symbiosis=function(json){
        if(json!=undefined){
            if(json.url!=undefined) $.symbiosis.url=json.url;
            if(json.path!=undefined) $.symbiosis.path=json.path;
            if(json.mlm!=undefined) $.symbiosis.mlm=json.mlm;
            if(json.language!=undefined) $.symbiosis.language=json.language;
            if(json.design!=undefined) $.symbiosis.design=json.design;
            if(json.code!=undefined) $.symbiosis.code=json.code;
            if(json.link!=undefined) $.symbiosis.link=json.link;
            if(json.admin!=undefined) $.symbiosis.admin=json.admin;
            if(json.language!=undefined){
                $.symbiosis.ajax=$.symbiosis.url+'ajax.php?language='+$.symbiosis.language;
            }
            if(json.init!=undefined) $.symbiosis.init=json.init;
            $.symbiosis.ajaxLoads=0;
            $.symbiosis.ajaxLoadsComplete=0;
            $.symbiosis.funcs={};
            $.symbiosis.hash={};
        }
        $.ajaxSetup({
            url: $.symbiosis.ajax,
            type: "POST",
            dataType: "json",
            global: true
        });
        if($.symbiosis.admin){
            $.ajax({
                'data':{
                    'symbiont': 'Admin.top',
                    'link': $.symbiosis.link
                },
                'dataType': 'html',
                'success': function(r){
                    var div=$('<div>'+r+'</div>');
                    $('body').prepend(div);
                }
            });
        }
    };
    $.fn.sym=function(){
        if($.symbiosis.init!=undefined){
            $.fn.symbiosis_init=$.symbiosis.init;
            $(this).symbiosis_init();
        }
    };
    $.symbiosisHash=function(hash){
        //var hash=location.hash.substr(1);
        var exp=hash.split("/");
        var result={};
        for(key in exp){
            var val=exp[key];
            var index=val.substr(0,val.indexOf('='));
            var value=val.substr(val.indexOf('=')+1);
            result[index]=value;
        }
        return result;
    };
    $(document).ready(function(){
        if($.symbiosis.init!=undefined){
            $.fn.symbiosis_init=$.symbiosis.init;
            $(this).symbiosis_init();
        }
        $.symbiosis.hash=$.symbiosisHash(location.hash.substr(1));
    });
    $(window).bind('hashchange', function(){
        $.symbiosis.hash=$.symbiosisHash(location.hash.substr(1));
    });
})(jQuery);