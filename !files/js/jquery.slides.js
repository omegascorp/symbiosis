/*
 * L'slides v0.1.2
 * jQuery plagin
 * OmegaScorp
 */
(function ($) {
    $.fn.slides=function(opt, param){
        if($(this).attr('lslides')!=undefined){
            $(this).slidesAPI(opt, param);
        }
        else{
            $(this).slidesInit(opt);
        }
    };
    $.fn.slidesInit=function(opt){
        if(opt!=undefined){
            if(opt.type!=undefined){
                switch(opt.type){
                    case 'horizontal': opt.type=0; break;
                    case 'vertical': opt.type=1; break;
                    case 'alpha': opt.type=2; break
                }
            }
        }
        var slides=$(this);
        slides.find('ul').addClass('slides');
        var options=$.extend({
            'inpage': 1,
            'position': 0,
            'speed': 1000,
            'width': $(this).find('ul>li').first().width(),
            'height': $(this).find('ul>li').height(),
            'type': 0,
            'count': $(this).find('ul>li').length,
            'timeout':0,
            'paused':false,
            'last': 0,
            'move': false,
            'change': function(){}
        },opt);
        
        slides.data('slides', options);
        
        slides.css({'overflow': 'hidden'})
        .width(options.width*(options.type==0?options.inpage:1))
        .height(options.height*(options.type==1?options.inpage:1));
        
        slides.find('ul')
        .css({
            'display': 'block',
            'list-style': 'none',
            'margin': '0px',
            'padding': '0px'
        })
        .width(options.width*(options.type==0?options.count:1))
        .height(options.height*(options.type==1?options.count:1));
        
        slides.find('ul>li').css({'width': options.width, 'height': options.height});
        
        switch(options.type){
            case 0:
                slides.find('ul>li').css({
                    'display': 'block',
                    'float': 'left'
                });
                slides.find('ul').css({
                    'margin-left': -options.position*options.width+'px'
                });
                break;
            case 1:
                slides.find('ul>li').css({
                    'display': 'block',
                    'float': 'left'
                });
                slides.find('ul').css({
                    'margin-top': -options.position*options.height+'px'
                });
                break;
            case 2:
                slides.find('ul>li').css({
                    'display': 'block',
                    'position': 'absolute',
                    'overflow': 'hidden',
                    'display': 'none'
                }).eq(options.position).css('display', 'block');
                break;
        }
        
        if(options.tabs){
            if(!slides.find('.tabs').length){
                var tabs=$('<ul class="tabs"></ul>');
                slides.find('li').each(function(){
                    var tab=$('<li><a href="#'+$(this).index()+'"></a></li>');
                    tabs.append(tab);
                });
                slides.append(tabs);
            }
        }
        
        var step=function(){
            slides.delay(options.timeout).queue(function(){
                d=new Date();
                if((options.last+options.timeout*2<d.getTime())&&!options.pause){
                    slides.slidesTo('next');
                }
                step();
            });
        };
        if(options.timeout){
            step();
        }
        
        
        if(options.type==0||options.type==1){
            slides.find('.slides li').eq(options.position).each(options.change);
        }
        else{
            slides.find('.slides li').eq(options.position).each(options.change);
        }
        
        if(tabs!=undefined){
            tabs.find('a').eq(options.position).addClass('active');
            tabs.find('a').click(function(){
                var to=parseInt($(this).attr('href').substring(1));
                slides.slidesTo(to, true);
                return false;
            });
        }
        
        slides.mousedown(function(e){
            options.move=true;
            options.mouse={x:e.clientX, y:e.clientY};
        });
        slides.mouseup(function(){
            options.move=false;
        });
        slides.mousemove(function(e){
            mouse={x:e.clientX, y:e.clientY};
            if(options.move){
                /*
                switch(options.type){
                case 0:
                    var left=parseInt($(this).find('ul').css('margin-left'));
                    $(this).find('ul').css({
                        'margin-left': -(options.mouse.x-mouse.x)+left+'px'
                    });
                    break;
                case 1:
                    $(this).find('ul').css({
                        'margin-top': -options.position*options.height+'px'
                    });
                    break;
                }
                */
            }
            options.mouse=mouse;
        });
    };
    $.fn.slidesTo=function(numb, save){
        var slides=$(this);
        var ul=$(this).find('ul:first');
        var tabs=slides.find('.tabs');
        var options=$(this).data('slides');
        
        var positionOld=options.position;
        if(numb!=undefined){
            if(numb=='next'){
                options.position++;
            }
            else if(numb=='prev'){
                options.position--;
            }
            else{
                options.position=numb;
            }
        }
        if(save!=undefined&&save){
            var date=new Date();
            options.last=date.getTime();
        }
        var direction=options.position>positionOld?1:0;
        options.position=$.slidesCircle(options.position, options.count-options.inpage+1);
        switch(options.type){
            case 0:
                ul.animate({
                    'margin-left': -options.position*options.width+'px'
                }, options.speed, function(){
                    slides.dequeue();
                    ul.find('li').eq(options.position).each(options.change);
                });
                break;
            case 1:
                ul.animate({
                    'margin-top': -options.position*options.height+'px'
                }, options.speed, function(){
                    slides.dequeue();
                    ul.find('li').eq(options.position).each(options.change);
                });
                break;
            case 2:
                ul.find('li').eq(positionOld).fadeOut(options.speed);
                ul.find('li').eq(options.position).fadeIn(options.speed, function(){
                    slides.dequeue();
                    $(this).each(options.change);
                });
                break;
        }
        
        if(tabs.length){
            tabs.find('li a').removeClass('active');
            tabs.find('a').eq(options.position).addClass('active');
        }
        
        slides.data('slides', options);
    };
    $.slidesCircle=function(number, max){
        while(number>=max){
            number-=max;
        }
        while(number<0){
            number+=max;
        }
        return number;
    };
})(jQuery);