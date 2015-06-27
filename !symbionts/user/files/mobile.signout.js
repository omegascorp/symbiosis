$(document).ready(function(){
    $('.symbionts-user-signout .signout').click(function(){
	$.ajax({
	    'data':{
                'file': 'user/ajax/signout',
		'kernel': true
            },
	    'success':function(){
		location.reload();
	    }
	});
	return false;
    });
});