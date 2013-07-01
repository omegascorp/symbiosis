$(document).ready(function(){
    $('.symbionts-comments form').submit(function(){
        var comments=$(this).closest('.symbionts-comments');
        var comment=$(this).closest('.comment');
        var form=$(this).closest('.commentForm');
        if(form.find('textarea').val()==''){
            form.find('textarea').focus();
            return false;
        }
        var data={};
        data['symbiont']='Comments.dbChange';
        data['for']=comments.find('.for').val();
        data['text']=comments.find('textarea').val();
        data['parentId']=comments.find('.parentId').val();
        $.ajax({
            'data': data,
            'success': function(result){
                if(result.success!=undefined){
                    var clone=comments.find('.commentTemplate').clone(true);
                    clone.removeClass('commentTemplate');
                    clone.show();
                    clone.find('.text').html(result.text);
                    clone.find('.id').val(result.id);
                    clone.find('.date').html(result.date);
                    if(comment.length){
                        comment.find('.answers:first').append(clone);
                        form.remove();
                    }
                    else{
                        comments.find('.comments').append(clone);
                        form.find('textarea').val('');
                    }
                }
            }
        });
        return false;
    });
    $('.symbionts-comments .answer').click(function(){
        var comments=$(this).closest('.symbionts-comments');
        var comment=$(this).closest('.comment');
        
        comments.find('.comment').each(function(){
            $(this).find('.commentForm').remove();
            $(this).find('.answer').show();
        });
        
        $(this).hide();
        
        var clone=comments.find('.commentForm').clone(true);
        clone.find('.parentId').val(comment.find('.id').val());
        comment.append(clone);
    });
});