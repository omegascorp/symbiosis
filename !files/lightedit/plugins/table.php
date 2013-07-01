<script>
$(document).ready(function(){
    var lightedit=$.lightedit();
    var opts=lightedit.options;
    var tables=lightedit.styles["tables"];
    
    lightedit.dialog({
        'height': 146
    });
    $('.lightedit-table .buttons a.insert').click(function(){
        var rows=$('.lightedit-table .rows input').val();
        var cols=$('.lightedit-table .cols input').val();
        var clss=$('.lightedit-table .style.active').attr('data-class');
        var table=$('<table></table>');
        while(rows--){
            row=$('<tr></tr>');
            tmp=cols;
            while(tmp--){
                row.append('<td><br/></td>');
            }
            table.append(row);
        }
        
        lightedit.insert('<table '+(clss?'class="'+clss+'"':'')+'>'+table.html()+'</table>');
        lightedit.dialogClose();
        return false;
    });
    
    $('.lightedit-table .buttons a.cancel').click(function(){
        lightedit.dialogClose();
        return false;
    });
    
    for(key in tables){
        val=tables[key];
        var table=$("<div class=\"style\" data-class=\""+val+"\"><table class=\""+val+"\"><tr><td>{table-one}</td><td>{table-two}</td></tr><tr><td>{table-tree}</td><td>{table-four}</td></tr></table></div>")
        table.hover(function(){
            $(this).addClass('over');
        },function(){
            $(this).removeClass('over');
        }).click(function(){
            $(this).parent().find('.style').removeClass('active');
            $(this).addClass('active');
        });
        $('.lightedit-table .styles').append(table);
    }
});
</script>
<div class="lightedit-table">
    <div class="tools">
        <div class="rows"><label>{table-rows}</label><input type="text" value="2" /></div>
        <div class="cols"><label>{table-cols}</label><input type="text" value="3" /></div>
        <div class="styles">
            
        </div>
    </div>
    <div class="buttons">
        <a href="" class="lightedit-button insert">{button-insert}</a>
        <a href="" class="lightedit-button cancel">{button-cancel}</a>
    </div>
</div>