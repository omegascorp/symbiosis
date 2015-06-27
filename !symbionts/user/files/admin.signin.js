$(document).ready(function(){
    $('.symbionts-user-signin .username').focus();
    $('.symbionts-user-signin form').submit(function(){
	var username=$(this).find(".username").val();
        var pass=$(this).find(".password").val();
	var key=$(this).find(".key").val();
	var password=calcMD5(calcMD5(pass)+key);
	$('.admin-layer-signin').remove();
        $.ajax({
            'data':{
                'file': 'user/ajax/signin',
		'kernel': true,
                'username': username,
                'password': password
            },
	    'success':function(r){
		if(r.accessLevel!=undefined&&r.accessLevel>=8){
		    location.reload();
		}
		else{
		    $('#signin-content, #signin-shadow').effect({
			'effect':'bounce',
			'duration':1000,
			'complete':function(){
			    if($(this).attr('id')!='signin-content') return;
			    var top=parseInt($('#signin-shadow').css('top'))+$('#signin-shadow').outerHeight()/2;
			    var left=parseInt($('#signin-shadow').css('left'))+$('#signin-shadow').outerWidth();
			    var popup=new AdminLayerTooltip({
				'class': 'signin',
				'content': $('.symbionts-user-signin').attr('data-invalid'),
				'x': top,
				'y': left,
				'type': 'left'
			    });
			}
		    });
		}
	    }
        });
        return false;
    });
});