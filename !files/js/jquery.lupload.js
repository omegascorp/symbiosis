//L'Upload 0.0.2
$.extend({
    lupload: function(json){
        json=$.extend({}, $.ajaxSettings, json);
        if(json.global && ! $.active++){
	    jQuery.event.trigger("ajaxStart");
	}
        var id=new Date().getTime();
        var form=$.lupload_form(id, json.url, json.element, json.data);
        var iframe=$.lupload_iframe(id);
        form.submit();
        var uploaded=function(){
            var r = {};
            if(json.global) jQuery.event.trigger("ajaxSend", [r, json]);
            if(iframe.contentWindow){
                r.text = iframe.contentWindow.document.body?iframe.contentWindow.document.body.innerHTML:null;
                r.xml = iframe.contentWindow.document.XMLDocument?iframe.contentWindow.document.XMLDocument:iframe.contentWindow.document;
            }
            else if(iframe.contentDocument){
                r.text = iframe.contentDocument.document.body?iframe.contentDocument.document.body.innerHTML:null;
                r.xml = iframe.contentDocument.document.XMLDocument?iframe.contentDocument.document.XMLDocument:iframe.contentDocument.document;
            }
            if(json.success){
                json.success($.lupload_type(r, json.dataType));
                if(json.global) jQuery.event.trigger("ajaxSuccess", [r, json]);
            }
            if(json.global) jQuery.event.trigger( "ajaxComplete", [r, json] );
            if (json.global&&!--$.active){
                jQuery.event.trigger("ajaxStop");
            }
        }
        if(window.attachEvent){
            document.getElementById('lupload_iframe_'+id).attachEvent('onload', uploaded);
        }
        else{
            document.getElementById('lupload_iframe_'+id).addEventListener('load', uploaded, false);
        }
    },
    lupload_form: function(id, url, element, data){
        var form = $('<form action="'+url+'" method="POST" id="lupload_form_'+id+'" target="lupload_iframe_'+id+'" enctype="multipart/form-data"></form>');
        $(element).each(function(){
            e=$(this).clone(true);
            $(this).attr('id', '');
            $(this).before(e);
            form.append($(this));
        });
        for(key in data){
            form.append('<input type="text" name="'+key+'" value="'+data[key]+'" />');
        }
        form.css('display', 'none');
        $('body').append(form);
        return form;
    },
    lupload_iframe: function(id){
        var iframe=$('<iframe id="lupload_iframe_'+id+'" name="lupload_iframe_'+id+'" />');
        iframe.css('display', 'none');
        $('body').append(iframe);
        return iframe.get(0);
    },
    lupload_type:function(r, type){
        var data=(type=="xml"||!type?r.xml:r.text);
        if(type=="script"){
            jQuery.globalEval(data);
        }
        else if(type=="json"){
            eval("data="+data);
        }
        else if(type=="html"){
            $("<div>").html(data).evalScripts();
        }
        return data;
    }
});