var init=function(){
    $('#top').click(function(){
	$(window).scrollTop(0);
    });
}
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