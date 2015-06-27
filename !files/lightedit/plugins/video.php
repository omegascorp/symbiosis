<script>
$(document).ready(function(){
    var lightedit=$.lightedit();
    lightedit.dialog({
        'height': 234
    });
    $('.lightedit-video .buttons a.save').click(function(){
        var code=$('.lightedit-video .tools .code textarea').val();
        lightedit.insert(code);
        lightedit.dialogClose();
        return false;
    });
    $('.lightedit-video .buttons a.cancel').click(function(){
        lightedit.dialogClose();
        return false;
    });
});
</script>
<div class="lightedit-video">
    <div class="tools">
        <div class="code">
            <textarea placeholder="{video-code}"></textarea>
        </div>
    </div>
    <div class="buttons">
        <a href="" class="lightedit-button save">{button-insert}</a>
        <a href="" class="lightedit-button cancel">{button-cancel}</a>
    </div>
</div>