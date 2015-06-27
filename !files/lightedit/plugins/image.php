<?
$images=isset($_POST['images'])?htmlspecialchars($_POST['images']):'';
$upload=isset($_POST['upload'])?htmlspecialchars($_POST['upload']):'';
?>
<script>
    $(document).ready(function(){
        var lightedit=$.lightedit();
        lightedit.dialog({
            'height': 358
        });
        var images=$('.lightedit-image').attr('data-images');
        var loadImages=function(){
            $('.lightedit-image .images ul').html('');
            $.ajax({
                'url':images,
                'type':'GET',
                'dataType':'json',
                'success':function(r){
                    for(key in r){
                        val=r[key];
                        var li=$('<li><a href="'+val.image+'"><img src="'+val.cover+'" /></a></li>');
                        li.find('a').click(function(){
                            var style='style="';
                            var selected=$('.lightedit-image .tools li.selected');
                            var title=$('.lightedit-image .tools .title input').val();
                            var width=$('.lightedit-image .tools .width input').val();
                            var height=$('.lightedit-image .tools .height input').val();
                            if(selected.hasClass('left')){
                                style+='display:block;float:left;';
                            }
                            else if(selected.hasClass('right')){
                                style+='display:block;float:right;';
                            }
                            else if(selected.hasClass('center')){
                                style+='display:block;margin:0 auto;';
                            }
                            if(width){
                                if(parseFloat(width)==width) width+='px';
                                style+='width:'+width+';';
                            }
                            if(height){
                                if(parseFloat(height)==width) height+='px';
                                style+='height:'+height+';';
                            }
                            style+='"';
                            var image=new Image();
                            image.onload=$.proxy(function(href, title, style){
                                this.insert('<img src="'+href+'" alt="'+title+'" title="'+title+'" '+style+' />');
                                this.element.find('.lightedit-dialog').removeClass('loading');
                                this.dialogClose();
                            }, lightedit, $(this).attr('href'), title, style);
                            image.src=$(this).attr('href');
                            lightedit.element.find('.lightedit-dialog').addClass('loading').html('');
                            return false;
                        });
                        $('.lightedit-image .images ul').append(li);
                    }
                }
            });
        };
        loadImages();
        $('.lightedit-image .tools a').click(function(){
            $(this).parent().parent().find('li').removeClass('selected');
            $(this).parent().addClass('selected');
            return false;
        });
        $('.lightedit-image .tools .width, .lightedit-image .tools .height').click(function(){
            $(this).find('input').focus();
        });
        $('.lightedit-image .browse form input').change(function(){
            $(this).closest('form').submit();
        });
        $('#lightedit-image-iframe').load(function(){
            if(this.contentWindow){
                json = this.contentWindow.document.body?this.contentWindow.document.body.innerHTML:null;
            }
            else if(this.contentDocument){
                json = this.contentDocument.document.body?this.contentDocument.document.body.innerHTML:null;
            }
            eval("json="+json);
            if(json.status=="success"){
                loadImages();
            }
            else{
                
            }
        });
    });
</script>
<div class="lightedit-image" data-images="<?=$images?>">
    <iframe id="lightedit-image-iframe" name="lightedit-image-iframe" />
    <div class="browse">
        <form action="<?=$upload?>" method="POST" target="lightedit-image-iframe" enctype="multipart/form-data">
            <input type="file" name="file" />
        </form>
    </div>
    <div class="images">
        <ul>
        </ul>
    </div>
    <div class="tools">
        <div class="tools-left">
            <div class="title"><input type="text" placeholder="{image-title}" /></div>
            <ul>
                <li class="none"><a href=""></a></li><li class="left"><a href=""></a></li><li class="center selected"><a href=""></a></li><li class="right"><a href=""></a></li>
            </ul>
        </div>
        <div class="tools-right">
            <div class="width"><label>{image-width}</label><input type="text" placeholder="{image-auto}" /></div>
            <div class="height"><label>{image-height}</label><input type="text" placeholder="{image-auto}" /></div>
        </div>
    </div>
</div>