$(document).ready(function(){
    $('.symbionts-users-admin-main .admin-widget-table').adminTable({
	'reload': 'Users.items',
	'change': 'Users.change',
	'changeLoad': function(tab){
	    
	},
	'changeSave': function(tab){
	    var data={};
	    data['symbiont']='Users.dbChange'
	    data['id']=$(this).find('.symbionts-users-admin-change').attr('data-id');
	    data['username']=$(this).find('.username').val();
	    password=$(this).find('.password').val();
	    repeatPassword=$(this).find('.repeatPassword').val();
	    if(password!=repeatPassword) return;
	    if(password){
		data['password']=calcMD5(password);
	    }
	    data['firstName']=$(this).find('.firstName').val();
	    data['lastName']=$(this).find('.lastName').val();
	    data['email']=$(this).find('.email').val();
	    data['country']=$(this).find('.country').val();
	    data['city']=$(this).find('.city').val();
	    data['sex']=$(this).find('.sex :first').hasClass('admin-selected')?1:0;
	    data['timezone']=$(this).find('.timezone').val();
	    data['accessLevel']=$(this).find('.accessLevel').val();
	    $.ajax({
		'data': data,
		'success':function(result){
		    if(result.success!=undefined){
			
		    }
		    else if(result.error!=undefined){
			var offset=$(this).find('.admin-button-save').offset();
			var popup=new AdminLayerTooltip({
			    'x': offset.top,
			    'y': offset.left,
			    'content': result.error,
			    'type': 'bottom',
			    'tail': 'left'
			});
		    }
		}
	    });
	    return true;
	},
	'remove': 'Users.delete',
	'removeOk': 'Users.dbDelete'
    });
});