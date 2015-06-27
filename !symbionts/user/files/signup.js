$(document).ready(function(){
    $('.symbionts-user-signup form').submit(function(){
        var signup=$(this).parent();
        var username=signup.find('.username input').val();
        var password=signup.find('.password input').val();
        var repassword=signup.find('.repassword input').val();
        var email=signup.find('.email input').val();
        if(username==''){
            signup.find('.username input').addClass('error');
            return false;
        }
        else{
            signup.find('.username input').removeClass('error');
        }
        if(password==''){
            signup.find('.password input').addClass('error');
            return false;
        }
        else{
            signup.find('.password input').removeClass('error');
        }
        if(password!=repassword){
            signup.find('.repassword input').addClass('error');
            return false;
        }
        else{
            signup.find('.repassword input').removeClass('error');
        }
        if(email==''){
            signup.find('.email input').addClass('error');
            return false;
        }
        else{
            signup.find('.email input').removeClass('error');
        }
        
        $.ajax({
            'data': {
                'symbiont': 'User.dbSignUp',
                'username': username,
                'password': calcMD5(password),
                'email': email
            },
            'success': function(r){
                if(r.success!=undefined){
                    signup.find('.message').html(r.success).removeClass('error').addClass('success').slideDown();
                    signup.find('form').slideUp();
                }
                else if(r.error!=undefined){
                    signup.find('.message').html(r.error).removeClass('success').addClass('error').slideDown();
                }
            }
        });
        return false;
    });
});