<script>
$(document).ready(function(){
    var lightedit=$.lightedit();
    var opts=lightedit.options;
    var tables=lightedit.styles["tables"];
    var current=lightedit.current.doc.find('td.active').closest('table');
    
    lightedit.dialog({
        'height': 96
    });
    
    $('.lightedit-table-edit .buttons a.save').click(function(){
        var clss=$('.lightedit-table-edit .style.active').attr('data-class');
        current.attr('class', clss);
        lightedit.dialogClose();
        lightedit.current.historySave();
        return false;
    });
    
    $('.lightedit-table-edit .buttons a.cancel').click(function(){
        lightedit.dialogClose();
        return false;
    });

    for(key in tables){
        val=tables[key];
        var table=$("<div class=\"style\" data-class=\""+val+"\"><table class=\""+val+"\"><tr><td>{table-one}</td><td>{table-two}</td></tr><tr><td>{table-tree}</td><td>{table-four}</td></tr></table></div>")
        if(current.hasClass(val)) table.addClass('active');
        table.hover(function(){
            $(this).addClass('over');
        },function(){
            $(this).removeClass('over');
        }).click(function(){
            $(this).parent().find('.style').removeClass('active');
            $(this).addClass('active');
        });
        $('.lightedit-table-edit .styles').append(table);
    }
});
</script>
<div class="lightedit-table-edit">
    <div class="tools">
        <div class="styles">
            
        </div>
    </div>
    <div class="buttons">
        <a href="" class="lightedit-button save">{button-save}</a>
        <a href="" class="lightedit-button cancel">{button-cancel}</a>
    </div>
</div>
