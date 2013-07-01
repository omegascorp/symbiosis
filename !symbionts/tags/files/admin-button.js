$(document).ready(function(){
    $('.symbionts-tags-admin-button').click(function(){
        var rand=Math.ceil(Math.random()*1000000);
        $(this).attr('data-rand', rand);
        var tags=[];
        $(this).find('.tag').each(function(){
            tags.push($(this).attr('data-id'));
        });
        $.ajax({
            'data':{
                'symbiont': 'Tags-Admin.select',
                'tags': tags
            },
            'dataType': 'html',
            'success': function(result){
                var max=1;
                var res=$(result);
                res.adminUI();
                res.find('.tabs').tabs();
                res.find('.selected .tag').click(function(){
                    $(this).remove();
                    return false;
                });
                res.find('.find').keyup(function(){
                    symbiontsTagsAdminGet();
                });
                res.find('.admin-button-save').click(function(){
                    if(res.find('.selected .tag').length){
                        $('.symbionts-tags-admin-button[data-rand='+rand+'] span').html(res.find('.label-tags').html()+res.find('.selected').html());
                    }
                    else{
                        $('.symbionts-tags-admin-button[data-rand='+rand+'] span').html(res.find('.label-addTags').html());
                    }
                    $('.admin-dialog-tags').remove();
                });
                symbiontsTagsAdminGet();
                var dialog=$.adminDialog(res, {
                    'x': 'center',
                    'y': '200',
                    'width': 600,
                    'height': 400
                });
                dialog.addClass('admin-dialog-tags');
            }
        });
    });
});
function symbiontsTagsAdminGet(){
    $.ajax({
        'data':{
            'symbiont': 'Tags-Admin.get',
            'title': $('.symbionts-tags-admin-select .find').val()
        },
        'success': function(result){
            $('.symbionts-tags-admin-select .tags').html('');
            for(key in result){
                var val=result[key];
                var tag=$('<a href="admin/tags/'+val.alias+'/" class="tag" data-id="'+val.id+'">'+val.title+'</span>');
                tag.click(function(){
                    var had=false;
                    var clone=$(this).clone(false);
                    $('.symbionts-tags-admin-select .selected a').each(function(){
                        if($(this).attr('data-id')==clone.attr('data-id')){
                            had=true;
                        }
                    });
                    if(!had){
                        clone.click(function(){
                            $(this).remove();
                            return false;
                        });
                        $('.symbionts-tags-admin-select .selected').append(clone);
                    }
                    return false;
                });
                $('.symbionts-tags-admin-select .tags').append(tag);
            }
        }
    });
}