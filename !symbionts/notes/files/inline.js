$(document).ready(function(){
    if(typeof(redactors)=='undefined') redactors=[];
    $('.symbionts-notes-note .text').dblclick(function(){
        var index=redactors.length;
        $(this).attr('data-redactor', index);
        $(this).parent().find('.wysiwyg').show();
        $(this).parent().find('.buttons').show();
        $(this).parent().find('.wysiwyg').attr('data-redactor', index);
        redactors[index]=$(this).parent().find('.wysiwyg').redactor({
            'base': $.symbiosis.url,
            'toolbar': 'symbiosis',
            'imageUpload': 'ajax.php?symbiont=Filemanager.upload&path=redactor/&input=file&return=html&overwrite=false',
            'air': true,
            'lang': $.symbiosis.language
        });
        $(this).hide();
    });
    
});