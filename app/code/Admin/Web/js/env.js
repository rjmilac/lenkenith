env = { 	
  	ext : true,
 	flags : { },
 	mode : 'development',
 	tab : {
 		status : 'active',
 		visibiltyAPI : {
 			idleTime : 0,
 			idleInterval : false,
 			idleTimeToRefreshPage : 1200,
 			idleTimeIncrement : function(){ 
 				env.tab.visibiltyAPI.idleTime++; 
 				if(env.tab.visibiltyAPI.idleTime >= env.tab.visibiltyAPI.idleTimeToRefreshPage){ 
 					clearInterval(env.tab.visibiltyAPI.idleInterval);
 					window.location.reload(); 
 				} 
 			},
 			idleTimeCountStart : function(){ env.tab.visibiltyAPI.idleInterval = setInterval(env.tab.visibiltyAPI.idleTimeIncrement ,1000) },
 			idleTimeCountReset : function($restart){ 
 				clearInterval(env.tab.visibiltyAPI.idleInterval);  				
 				env.tab.visibiltyAPI.idleTime = 0;  
 				if($restart){
 					env.tab.visibiltyAPI.idleTimeCountStart();
 				}	
 			},
 			getHiddenProp : function() {
 				var prefixes = ['webkit','moz','ms','o'];    
			    if ('hidden' in document) return 'hidden';
			    for (var i = 0; i < prefixes.length; i++){
			        if ((prefixes[i] + 'Hidden') in document) 
			            return prefixes[i] + 'Hidden';
			    }
			    return null;
 			},
 			isSupported : function() { return (typeof(document.visibilityState) === 'undefined') ? false : true }, 			
 			isHidden : function(){ 
 				var prop = env.tab.visibiltyAPI.getHiddenProp();
				if (!prop) return false;
				return document[prop];
 			}, 			
 			bindIdleWatch : function(){
 				env.tab.visibiltyAPI.idleTimeCountStart();
 				$(window).bind('mousemove',function(e) {
 					env.tab.visibiltyAPI.idleTimeCountReset(true);
				});
				$(window).bind('keypress',function(e){
					env.tab.visibiltyAPI.idleTimeCountReset(true);
				});	
 			},
 			unbindIdleWatch : function(){
 				$(window).unbind('keypress');
 				$(window).unbind('mousemove');
 			},
 			bindVisChange : function(){
 				var visProp = env.tab.visibiltyAPI.getHiddenProp();
 				if(visProp){
 					var evtname = visProp.replace(/[H|h]idden/,'') + 'visibilitychange';
  					document.addEventListener(evtname, env.tab.visibiltyAPI.visChange);
 				}
 				else{
 					return 'Visibility API is unsupported';
 				}
 			},
 			visChange : function(){
 				if(!env.tab.visibiltyAPI.isHidden()){
 					// -- tab is switched to active 					
 				}
 				else{
 					// -- user switched to other tab... 			
 				}
 			}
 		}
 	}	
}