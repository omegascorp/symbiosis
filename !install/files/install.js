var globals={
    'abbr': 'en',
    'step': 1
};
$(document).ready(function(){
    $("#shadow").css({
	'left': $(document).width()/2-$("#shadow").width()/2,
	'top': $(document).height()/2-$("#shadow").height()/2-50
    });
    $("#content").css({
	'left': $(document).width()/2-$("#shadow").width()/2,
	'top': $(document).height()/2-$("#shadow").height()/2-50
    });
    //Loading
    $(document).ajaxStart(function(event,request, settings){
	adminLoading=true;
	adminLoadingHide();
    });
    $(document).ajaxStop(function(event,request, settings){
	adminLoading=false;
    });

    loadLanguages();
});
function adminLoadingHide(){
    $('#footer img').animate({
	'opacity': '0.2'
    }, 500);
    setTimeout(adminLoadingShow, 500);
}
function adminLoadingShow(){
    $('#footer img').animate({
	'opacity': '1'
    }, 500);
    if(adminLoading) setTimeout(adminLoadingHide, 500);
}
function loadLanguages(){
    $.ajax({
	'data': {
	    'abbr': globals.abbr
	},
	'url': '!install/language.php',
	'dataType': 'html',
	'type': 'post',
	'success': function(result){
	    $('#content').html(result);
	    $("#footer .back").button({icons: {primary:'ui-icon-arrow-1-w'}});
	    $("#footer .next").button({icons: {secondary:'ui-icon-arrow-1-e'}});
	    $("#footer .back").button('disable');
	    $("#footer .next").click(function(){
		if(globals.step==1){
		    globals.step++;
		    loadRights();
		}
		else if(globals.step==2){
		    globals.step++;
		    loadDb();
		}
		else if(globals.step==3){
		    saveDb();
		}
		else if(globals.step==4){
		    saveData();
		}
	    });
	    $("#footer .back").click(function(){
		if(globals.step==2){
		    globals.step--;
		    loadLanguages();
		}
		else if(globals.step==3){
		    globals.step--;
		    loadRights();
		}
		else if(globals.step==4){
		    globals.step--;
		    loadDb();
		}
		else if(globals.step==5){
		    globals.step--;
		    loadData();
		}
	    });
	    $("#middle li a")
	    .hover(function(){
		$(this).addClass('over');
	    },function(){
		$(this).removeClass('over');
	    })
	    .click(function(){
		globals.abbr=$(this).attr('data-abbr');
		loadLanguages();
	    });
	}
    });
}
function loadRights(){
    $.ajax({
	'data': {
	    'abbr': globals.abbr
	},
	'url': '!install/rights.php',
	'dataType': 'html',
	'type': 'post',
	'success': function(result){
	    $('#middle').html(result);
	    $("#footer .back").button('enable');
	    $("#middle .refresh")
	    .button({icons: {primary:'ui-icon-refresh'}})
	    .click(function(){
		loadRights();
	    });
	}
    });
}
function loadDb(){
    $.ajax({
	'data': {
	    'abbr': globals.abbr
	},
	'url': '!install/db.php',
	'dataType': 'html',
	'type': 'post',
	'success': function(result){
	    $('#middle').html(result);
	}
    });
}
function saveDb(){
    $.ajax({
	'data': {
	    'abbr': globals.abbr,
	    'host': $('#middle .host input').val(),
	    'username': $('#middle .username input').val(),
	    'password': $('#middle .password input').val(),
	    'database': $('#middle .database input').val()
	},
	'url': '!install/dbSave.php',
	'dataType': 'json',
	'type': 'post',
	'success': function(result){
	    if(result.success!=undefined){
		loadData();
		globals.step++;
	    }
	    else if(result.error!=undefined){
		$('#middle .message').addClass('ui-state-error').removeClass('ui-state-highlight').html(result.error);
	    }
	}
    });
}
function loadData(){
    $.ajax({
	'data': {
	    'abbr': globals.abbr
	},
	'url': '!install/data.php',
	'dataType': 'html',
	'type': 'post',
	'success': function(result){
	    $('#middle').html(result);
	}
    });
}
function saveData(){
    $.ajax({
	'data': {
	    'abbr': globals.abbr,
	    'username': $('#middle .username input').val(),
	    'password': $('#middle .password input').val(),
	    'email': $('#middle .email input').val()
	},
	'url': '!install/dataSave.php',
	'dataType': 'json',
	'type': 'post',
	'success': function(result){
	    if(result.success!=undefined){
		location.reload();
	    }
	    else if(result.error!=undefined){
		$('#middle .message').addClass('ui-state-error').removeClass('ui-state-highlight').html(result.error);
	    }
	}
    });
}