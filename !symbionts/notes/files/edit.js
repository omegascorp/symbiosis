$(document).ready(function(){
    $('.symbionts-notes-edit .categories li').click(function(){
        var edit=$(this).closest('.symbionts-notes-edit');
        $(this).parent().find('li').removeClass('admin-selected');
        $(this).addClass('admin-selected');
        var categoryId=$(this).attr('category');
        if(categoryId!=0){
            $.ajax({
                'data':{
                    'symbiont': 'Notes.editNotes',
                    'categoryId': categoryId
                },
                'success': function(result){
                    edit.find('.notes').show();
                    edit.find('.notes li:not(:first)').remove();
                    noteId=parseInt(edit.find('.notes').attr('note'));
                    if(noteId==0) edit.find('.notes li:first').addClass('admin-selected');
                    for(key in result){
                        val=result[key];
                        li=$('<li class="ui-corner-all"></li>');
                        li.adminHover();
                        li.html(val.title);
                        li.attr('note', val.id);
                        if(noteId==val.id) li.addClass('admin-selected');
                        li.click(function(){
                            $(this).parent().find('li').removeClass('admin-selected');
                            $(this).addClass('admin-selected');
                        });
                        edit.find('.notes li:last').after(li);
                    }
                    edit.find('.notes').attr('note', '0');
                }
            });
        }
        else{
            edit.find('.notes').hide();
        }
    });
    $('.symbionts-notes-edit .notes li').click(function(){
        $(this).parent().find('li').removeClass('admin-selected');
        $(this).addClass('admin-selected');
    });
    noteId=$('.symbionts-notes-edit .notes').attr('note');
    categoryId=$('.symbionts-notes-edit .categories li.admin-selected').attr('category');
    if(categoryId==0){
        $('.symbionts-notes-edit .notes').hide();
    }
    else{
        $('.symbionts-notes-edit .categories li.admin-selected').click();
    }
    $('.symbionts-notes-edit .admin-button-save').unbind('click').click(function(){
        var edit=$(this).closest('.symbionts-notes-edit');
        var symbiont='Notes.main';
        
        categoryId=parseInt(edit.find('ul.categories li.admin-selected').attr('category'));
        noteId=parseInt(edit.find('ul.notes li.admin-selected').attr('note'));
        if(categoryId){
            symbiont+='.categoryId='+categoryId;
            if(noteId){
                symbiont+='.noteId='+noteId;
            }
        }
        edit.closest('.widget').find('.symbiont').html(symbiont);
        SPagesSave();
        SPagesWidgetClose($(this).closest('.widget'));
        SPagesWidgetInfo($(this).closest('.widget'));
    });
    $('.symbionts-notes-edit .admin-button-cancel').unbind('click').click(function(){
        SPagesWidgetClose($(this).closest('.widget'));
    });
});