$(document).ready(function(){
    /*
    $('.symbionts-notes-widget-admin').mCustomScrollbar({
        horizontalScroll:true,
        scrollInertia: 100,
        autoHideScrollbar: true,
        advanced:{  
            updateOnContentResize: true,
        },
        theme: "dark",
        callbacks:{
            onTotalScroll:function(){
                $.ajax({
                    'data':{
                        'symbiont': '#Notes-Admin.notes.widget-admin-notes[text=ture,order=1,limit=10,start='+$('.symbionts-notes-widget-admin li:last').attr('data-date')+']'
                    },
                    'dataType': 'html',
                    'success': function(r){
                        ul=$(r);
                        $('.symbionts-notes-widget-admin ul').append(ul.children());
                        //SNotesWidgetWidth();
                        var clone=$('.symbionts-notes-widget-admin ul').clone();
                        $('.symbionts-notes-widget-admin .mCSB_container').html('');
                        //$('.symbionts-notes-widget-admin .mCSB_container').append(clone);
                        if (ul.children().length){
                            $('.symbionts-notes-widget-admin').mCustomScrollbar("update");
                        }
                    }
                });
            }
        }
    });
    */
    var scrollar=$('.symbionts-notes-widget-admin').data('scrollar');
    scrollar.options.onscroll=function(){
        if(this.hEnd){
            $.ajax({
                'data':{
                    'symbiont': '#Notes-Admin.notes.widget-admin-notes[text=ture,order=1,limit=10,start='+$('.symbionts-notes-widget-admin li:last').attr('data-date')+']'
                },
                'dataType': 'html',
                'success': function(r){
                    ul=$(r);
                    $('.symbionts-notes-widget-admin ul').append(ul.children());
                    //SNotesWidgetWidth();
                    //var clone=$('.symbionts-notes-widget-admin ul').clone();
                    //$('.symbionts-notes-widget-admin .mCSB_container').html('');
                    //$('.symbionts-notes-widget-admin .mCSB_container').append(clone);
                }
            });
        }
    };
});
function SNotesWidgetWidth() {
    $('.symbionts-notes-widget-admin').each(function(){
        var width=0;
        $(this).find('li').each(function(){
            width+=$(this).outerWidth();
        });
        $(this).find('ul').width(width);
        //$(this).find('ul').parent().width(width);
    });
    
}