$(document).ready(function(){
    $('.symbionts-filemanager-mini input[type=file]').change(function(){
        var filename=$(this).val();
        var index=filename.lastIndexOf('\\');
        if(index==-1){
            index=filename.lastIndexOf('/');
        }
        filename=filename.substr(index+1);
        index=filename.lastIndexOf('.');
        type=filename.substr(index+1);
        var createFolder=$(this).closest('.symbionts-filemanager-mini').attr('data-create-folder');
        $.lupload({
            url: $.symbiosis.ajax,            
            element: '.symbionts-filemanager-mini input[type=file]',
            data:{
                'symbiont':'Filemanager.upload',
                'input': 'file',
                'path':$('.symbionts-filemanager-mini .files').attr('data-path'),
                'name':filename,
                'overwrite': false,
                'createFolder': createFolder
            },
            success: function (r, status){
                var first=$('.symbionts-filemanager-mini .files .file:first');
                first.attr('data-name', r.name);
                first.attr('data-type', type);
                first.find('img').attr('src', r.icon128);
                first.click();
            }
        });
    });
});