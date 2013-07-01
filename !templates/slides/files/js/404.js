var init=function(){
    $(window).resize(resize);
    resize();
};
var resize=function(){
    $('#wrapper').css({
        'margin-top': ($(window).height()/2-$('#wrapper').height()/2)+'px'
    });
}