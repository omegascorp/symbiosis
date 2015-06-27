$(document).ready(function(){
    $('.symbionts-tags-edit .main').click(function(){
        var id=$('#content .id').first().html();
        var symbiont='Tags.main';
        if(!$(this).hasClass('ui-state-active')){
            symbiont+='.main=true';
        }
        $(this).closest('.widget').find('.symbiont').html(symbiont);
        SPagesSave();
        SPagesWidgetInfo($(this).closest('.widget'));
    });
});