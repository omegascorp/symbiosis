var windowSize=[];
$(document).ready(function(){
    if($("#signin-overlay").length){
	$("#signin-shadow").css({
	    'left': $(document).width()/2-$("#signin-shadow").width()/2-10,
	    'top': $(document).height()/2-$("#signin-shadow").height()/2-60
	});
	$("#signin-content").css({
	    'left': $(document).width()/2-$("#signin-shadow").width()/2,
	    'top': $(document).height()/2-$("#signin-shadow").height()/2-50
	});
	$("#signin-overlay").height($(window).height());
	var max=Math.round(Math.random()*17)+3;
	var rgb=rand(150, 255);
	for(i=0;i<max;i++){
	    var circle=$('<div class="circle"></div>')
	    var size=Math.round(Math.random()*$(document).height()/10);
	    var left=Math.round(Math.random()*$(document).width())-size;
	    var top=Math.round(Math.random()*$(document).height())-size;
	    var opacity=size/100;
	    circle.css({
		'left': left+'px',
		'top': top+'px',
		'width': size+'px',
		'height': size+'px',
		'opacity': opacity,
		'border-radius': size,
		'background': rgb,
		'box-shadow': '0 0 20px '+rgb
	    });
	    
	    $("#signin-overlay").append(circle);
	}
    }
    
    $("#languages-content ul").css({
	'margin-left': $("#languages-content").width()/2-$("#languages-content ul").width()/2
    });
    
    $('.admin-widget-icon.clean').click(function(){
        $.ajax({
            'data':{
                'file':'admin/ajax/clean'
            }
        })
    });
    
    //Loading
    $(document).ajaxStart(function(event,request, settings){
	adminLoading=true;
	$('#loading,#signin-shadow .spinner').show();
	//adminLoadingHide();
    });
    $(document).ajaxStop(function(event,request, settings){
	adminLoading=false;
	$('#loading,#signin-shadow .spinner').hide();
    });
    $('#top').click(function(){
	$(window).scrollTop(0);
    });
    if($('aside#leftside').height()>$('#middleside').height()){
	$('#middleside').css('min-height', $('aside#leftside').height()+'px');
    }
    windowSize=[$(window).width(), $(window).height()];
});
$(window).scroll(function(){
    var opacity = ($(this).scrollTop()-$(window).height())/1000;
    if(opacity<0){
	opacity=0;
    }
    else if(opacity>1){
	opacity=1;
    }
    $('#top').css({
	'display': opacity?'block':'none',
	'opacity': opacity
    });
});
$(window).resize(function(){
    if($("#signin-overlay").length){
	$("#signin-shadow").css({
	    'left': $(document).width()/2-$("#signin-shadow").width()/2-10,
	    'top': $(document).height()/2-$("#signin-shadow").height()/2-60
	});
	$("#signin-content").css({
	    'left': $(document).width()/2-$("#signin-shadow").width()/2,
	    'top': $(document).height()/2-$("#signin-shadow").height()/2-50
	});
	$("#signin-overlay").height($(window).height());
	
	$('#signin-overlay .circle').each(function(){
	    var top=parseInt($(this).css('top'));
	    var left=parseInt($(this).css('left'));
	    $(this).css({
		'left': (left/windowSize[0]*$(window).width())+'px',
		'top': (top/windowSize[1]*$(window).height())+'px'
	    });
	});
	
	windowSize=[$(window).width(), $(window).height()];
    }
});

function dec2hex(n){
    n = parseInt(n); var c = 'ABCDEF';
    var b = n / 16; var r = n % 16; b = b-(r/16); 
    b = ((b>=0) && (b<=9)) ? b : c.charAt(b-10);    
    return ((r>=0) && (r<=9)) ? b+''+r : b+''+c.charAt(r-10);
}
function rand(min, max){
    var rand=Math.round(Math.random()*2);
    r=Math.round(Math.random()*(max-min))+min;
    g=Math.round(Math.random()*(max-min))+min;
    b=Math.round(Math.random()*(max-min))+min;
    min=min*3;
    max=max*3;
    if(rand==3){
	max-=r+g;
	min-=r+g;
	if(min<0) min=0;
	if(max<0) max=0;
	if(min>255) min=255;
	if(max>255) max=255;
	b=Math.round(Math.random()*(max-min))+min;
    }
    else if(rand==2){
	max-=r+b;
	min-=r+b;
	if(min<0) min=0;
	if(max<0) max=0;
	if(min>255) min=255;
	if(max>255) max=255;
	g=Math.round(Math.random()*(max-min))+min;
    }
    else{
	max-=g+b;
	min-=g+b;
	if(min<0) min=0;
	if(max<0) max=0;
	if(min>255) min=255;
	if(max>255) max=255;
	r=Math.round(Math.random()*(max-min))+min;
    }
    return '#'+dec2hex(r)+dec2hex(g)+dec2hex(b);
}