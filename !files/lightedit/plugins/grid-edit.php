<script>
$(document).ready(function(){
    var lightedit=$.lightedit();
    var opts=lightedit.options;
    
    var old=lightedit.styles["blocks"];
    var blocks=$.extend([], old);
    var current=lightedit.current.doc.find('.grid-col.active');
    
    lightedit.dialog({
        'height': 120
    });
    
    $('.lightedit-grid-edit .buttons a.save').click(function(){
        var clss=$('.lightedit-grid-edit .style.active').attr('data-class');
        current.attr('class', current.attr('class').replace(/ grid-style-[A-Za-z0-9]*/, ''));
        if(clss) current.addClass('grid-style-'+clss);
        lightedit.dialogClose();
        lightedit.current.historySave();
        return false;
    });
    
    $('.lightedit-grid-edit .buttons a.cancel').click(function(){
        lightedit.dialogClose();
        return false;
    });
    
    blocks.unshift("");
    for(key in blocks){
        val=blocks[key];
        var style=$("<div class=\"style\" data-class=\""+val+"\">"+(val?val:"none")+"</div>")
        if(current.hasClass('grid-style-'+val)) style.addClass('active');
        style.hover(function(){
            $(this).addClass('over');
        },function(){
            $(this).removeClass('over');
        }).click(function(){
            $(this).parent().find('.style').removeClass('active');
            $(this).addClass('active');
        });
        $('.lightedit-grid-edit .styles').append(style);
    }
    if($('.lightedit-grid-edit .styles .style.active').length==0) $('.lightedit-grid-edit .styles .style:first').addClass('active');
});
</script>

<div class="lightedit-grid-edit">
    <div class="styles">
        
    </div>
    <div class="buttons">
        <a href="" class="lightedit-button save">{button-save}</a>
        <a href="" class="lightedit-button cancel">{button-cancel}</a>
    </div>
</div>