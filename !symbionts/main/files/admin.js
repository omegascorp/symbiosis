$(document).ready(function(){
    $(".admin-widget-main").each(function(){
        $(this).data('scrollar', new Scrollar({element : $(this), vscroll:false}));
    });
});