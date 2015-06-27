$(document).ready(function(){
    $('.symbionts-symbionts .admin-clear .install').click(function(){
        var dialog=$.adminDialog($('.install-template').children().clone(), {x:'center', y:'middle', 'width': 400, 'height': 200});
        dialog.addClass('symbionts-symbionts-install-dialog');
        dialog.find('.cancel').button().click(function(){
            $('.symbionts-symbionts-install-dialog').remove();
        });
        dialog.find('.install').button().click(function(){
            $.lupload({
                url: $.symbiosis.ajax,            
                element: '.symbionts-symbionts-install-dialog .file',
                data:{
                    'symbiont':'Symbionts.upload',
                    'input': 'file',
                    'path': '.temp/',
                    'name': $('.symbionts-symbionts-install-dialog .filename').val()
                },
                success: function (r, status){
                    
                }
            });
        });
        dialog.find('.filename').click(function(){
            if($(this).val()=='') $(this).parent().find('.file').click();
        });
        dialog.find('.file').change(function(){
            var filename=$(this).val();
            var index=filename.lastIndexOf('\\');
            if(index==-1){
                index=filename.lastIndexOf('/');
            }
            $(this).parent().find('.filename').val(filename.substr(index+1));
        });
        dialog.find('.browse').button().click(function(){
            $(this).parent().parent().find('.file').click();
        });
    });
    $('.symbionts-symbionts .item.selectable').click(function(){
        if($(this).hasClass('admin-selected')){
            $(this).removeClass('admin-selected');
            $.ajax({
                'data':{
                    'symbiont': 'Symbionts.dbChange',
                    'name': $(this).attr('data-symbiont'),
                    'remove': true
                },
                'success': function(){
                    
                }
            });
        }
        else{
            $(this).addClass('admin-selected');
            $.ajax({
                'data':{
                    'symbiont': 'Symbionts.dbChange',
                    'name': $(this).attr('data-symbiont'),
                    'remove': false
                },
                'success': function(){
                    
                }
            });
        }
    });
});