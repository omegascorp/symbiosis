<script>
$(document).ready(function(){
    var lightedit=$.lightedit();
    var link=lightedit.data.link;
    if(link!=undefined){
        $('.lightedit-link .tools .url input').val(link.attr('href'));
    }
    lightedit.dialog({
        'height': 96
    });
    $('.lightedit-link .buttons a.save').click(function(){
        var href=$('.lightedit-link .tools .url input').val();
        if(link!=undefined){
            link.attr('href', href);
        }
        else{
            lightedit.exec('unlink');
            lightedit.exec('createlink', href);
        }
        lightedit.dialogClose();
        return false;
    });
    $('.lightedit-link .buttons a.remove').click(function(){
        if(link!=undefined){
            link.replaceWith(link.html());
        }
        else{
            lightedit.exec('unlink');
        }
        lightedit.dialogClose();
        return false;
    });
    $('.lightedit-link .buttons a.cancel').click(function(){
        lightedit.dialogClose();
        return false;
    });
});
</script>
<div class="lightedit-link">
    <div class="tools">
        <div class="url"><input type="text" placeholder="{link-link}" /></div>
    </div>
    <div class="buttons">
        <a href="" class="lightedit-button save">{button-save}</a>
        <a href="" class="lightedit-button remove">{button-remove}</a>
        <a href="" class="lightedit-button cancel">{button-cancel}</a>
    </div>
</div>