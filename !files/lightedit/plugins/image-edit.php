<script>
    $(document).ready(function(){
        var lightedit=$.lightedit();
        lightedit.dialog({
            'height': 96
        });
        var image=lightedit.data.image;
        $('.lightedit-image-edit .title input').val(image.attr('title'));
        
        $('.lightedit-image-edit .tools a').click(function(){
            $(this).parent().parent().find('li').removeClass('selected');
            $(this).parent().addClass('selected');
            return false;
        });
        $('.lightedit-image-edit .buttons a.save').click(function(){
            var selected=$('.lightedit-image-edit .tools li.selected');
            var title=$('.lightedit-image-edit .tools .title input').val();
            var width=$('.lightedit-image-edit .tools .width input').val();
            var height=$('.lightedit-image-edit .tools .height input').val();
            image.attr('title', title);
            image.attr('alt', title);
            if(selected.hasClass('left')){
                image.css({
                    'display':'block',
                    'float': 'left',
                    'margin': 'inherit'
                });
            }
            else if(selected.hasClass('right')){
                image.css({
                    'display':'block',
                    'float': 'right',
                    'margin': 'inherit'
                });
            }
            else if(selected.hasClass('center')){
                image.css({
                    'display':'block',
                    'float': 'none',
                    'margin': '0 auto'
                });
            }
            else{
                image.css({
                    'display':'inline',
                    'float': 'none',
                    'margin': 'inherit'
                });
            }
            if(width){
                if(parseFloat(width)==width) width+='px';
                image.width(width);
            }
            if(height){
                if(parseFloat(height)==width) height+='px';
                image.height(height);
            }
            lightedit.current.updateHeight();
            lightedit.dialogClose();
            lightedit.current.historySave();
            return false;
        });
        $('.lightedit-image-edit .buttons a.cancel').click(function(){
            lightedit.dialogClose();
            return false;
        });
        $('.lightedit-image-edit .tools .width, .lightedit-image-edit .tools .height').click(function(){
            $(this).find('input').focus();
        });
        $('.lightedit-image-edit .tools .title input').val(image.attr('title'));
        if(image.css('display')=='inline'){
            $('.lightedit-image-edit .tools li.none a').click();
        }
        else if(image.css('display')=='block'){
            if(image.css('float')=='left'){
                $('.lightedit-image-edit .tools li.left a').click();
            }
            else if(image.css('float')=='right'){
                $('.lightedit-image-edit .tools li.right a').click();
            }
            else{
                $('.lightedit-image-edit .tools li.center a').click();
            }
        }
        $('.lightedit-image-edit .tools .width input').val(image.get(0).style.width);
        $('.lightedit-image-edit .tools .height input').val(image.get(0).style.height);
    });
</script>
<div class="lightedit-image-edit">
    <div class="tools">
        <div class="tools-left">
            <div class="title"><input type="text" placeholder="{image-title}" /></div>
            <ul>
                <li class="none"><a href=""></a></li><li class="left"><a href=""></a></li><li class="center"><a href=""></a></li><li class="right"><a href=""></a></li>
            </ul>
        </div>
        <div class="tools-right">
            <div class="width"><label>{image-width}</label><input type="text" placeholder="{image-auto}" /></div>
            <div class="height"><label>{image-height}</label><input type="text" placeholder="{image-auto}" /></div>
        </div>
    </div>
    <div class="buttons">
        <a href="" class="lightedit-button save">{button-save}</a>
        <a href="" class="lightedit-button cancel">{button-cancel}</a>
    </div>
</div>