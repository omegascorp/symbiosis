<script>
$(document).ready(function(){
    var lightedit=$.lightedit();
    var opts=lightedit.options;
    
    lightedit.dialog({
        'height': 200
    });
    for(i=2;i<=6;i++){
        var name='';
        if(i==2) name='II';
        else if(i==3) name='III';
        else if(i==4) name='IV';
        else if(i==5) name='V';
        else if(i==6) name='VI';
        var span=$('<span data-count="'+i+'">'+name+'</span>');
        span.hover(function(){
            $(this).addClass('over');
        },function(){
            $(this).removeClass('over');
        })
        .click(function(){
            $(this).parent().find('span').removeClass('active');
            $(this).addClass('active');
            
            var count=$(this).attr('data-count');
            var cols=$(this).closest('.lightedit-grid').find('.cols');
            cols.html('');
            if(count<2||count>6) return;
            for(i=0; i<count; i++){
                cols.append('<div class="col"></div>');
                if(i!=count-1){
                    var union=$('<div class="union"></div>');
                    union.click(function(){
                        if($(this).hasClass('active')){
                            $(this).removeClass('active');
                        }
                        else{
                            $(this).addClass('active');
                        }
                        var passive=$(this).closest('.cols').find('.union:not(.union.active)');
                        if(passive.length==1){
                            passive.css('visibility', 'hidden');
                        }
                        else{
                            passive.css('visibility', 'visible');
                        }
                    });
                    cols.append(union);
                }
            }
            var passive=$(this).closest('.lightedit-grid').find('.cols').find('.union:not(.union.active)');
            if(passive.length==1){
                passive.css('visibility', 'hidden');
            }
            else{
                passive.css('visibility', 'visible');
            }
        });
        $('.lightedit-grid .count').append(span);
    }
    $('.lightedit-grid .count span').eq(1).click();
    $('.lightedit-grid .buttons a.insert').click(function(){
        var count=parseInt($(this).closest('.lightedit-grid').find('.count .active').attr('data-count'));
        var cols=[];
        var i=0;
        $('.lightedit-grid .cols .col').each(function(){
            if(cols[i]==undefined) cols[i]=1;
            else cols[i]++;
            if(!$(this).next().hasClass('active')){
                i++;
            }
        });
        var grid=$('<div class="grid"></div>');
        for(i=0; i<cols.length; i++){
            var name='';
            if(cols[i]==2){name+='two';}
            else if(cols[i]==3){name+='three';}
            else if(cols[i]==4){name+='four';}
            else if(cols[i]==5){name+='five';}
            if(count==2){name+='half';}
            else if(count==3){name+='third';}
            else if(count==4){name+='quarter';}
            else if(count==5){name+='fifth';}
            else if(count==6){name+='sixth';}
            grid.append($('<div class="grid-col grid-'+name+'"><p><br/></p></div>'));
        }
        lightedit.insert('<div class="grid">'+grid.html()+'</div><p></p>');
        lightedit.dialogClose();
        return false;
    });
    $('.lightedit-grid .buttons a.cancel').click(function(){
        lightedit.dialogClose();
        return false;
    });
});
</script>
<div class="lightedit-grid">
    <div class="count-label">{grid-columns}</div>
    <div class="count">
        
    </div>
    <div class="cols"></div>
    <div class="help">{grid-help}</div>
    <div class="buttons">
        <a href="" class="lightedit-button insert">{button-insert}</a>
        <a href="" class="lightedit-button cancel">{button-cancel}</a>
    </div>
</div>