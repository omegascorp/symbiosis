$(document).ready(function(){
    $('.symbionts-user-signin form').submit(function(){
	var username=$(this).find(".username input").val();
        var pass=$(this).find(".password input").val();
	var key=$(this).find(".key").val();
	var password=calcMD5(calcMD5(pass)+key);
        $.ajax({
            'data':{
                'file': 'user/ajax/signin',
		'kernel': true,
                'username': username,
                'password': password
            },
	    'success':function(r){
		if(r.accessLevel!=undefined&&r.accessLevel>=9){
		    location.reload();
		}
		else{
		    
		}
	    }
        });
        return false;
    });
});