$.loginFormUI = {
	dfd : {
		qv : false
	},
	quickValidate : function(){	
		lfu.dfd.qv = new $.Deferred();
		$('#form-adm-signin input').each(function(){
			var val = $(this).val();
			if(val == '' || val.length < 1 || typeof(val) == 'undefined'){
				$(this).focus();
			}
		}).promise.done(function(){
			lfu.dfd.qv.resolve(true);
		});		
		return lfu.dfd.qv.promise();
	},
	bindEventListeners : function(){
		lfu = $.loginFormUI;
		lfu.unbindEventListeners();
		$('#adm-login-form-trigger').bind('click',function(ev){
			ev.preventDefault();
			lfu.quickValidate().done(function(){
				
			});					
			return false;
		});
	},
	unbindEventListeners : function(){
		$('#adm-login-form-trigger').unbind('click');
		$('#adm-login-form-trigger').bind('click',function(ev){ ev.preventDefault(); return false; });
	}
}
$(document).ready(function(){
	$.loginFormUI.bindEventListeners();
});