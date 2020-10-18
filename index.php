<?php
	
	// $print_exception = true;
	require_once(__DIR__.'/system/Core/Bootstrap.php');
	$bootstrap = new \Lenkenith\Core\Bootstrap();
	// try{
	$app = $bootstrap->run();
	$app->execute();	
	// }
	// catch(\Exception $e){
	// 	handleException($e);
	// }	
	// function handleException($e){
	// 	global $bootstrap;
	// 	if($bootstrap->logger instanceof \Lenkenith\Core\Logger){ 
	// 		$bootstrap->logger->log('('.get_class($e).') '.$e->getMessage().' ('.$e->getFile().' on line '.$e->getLine().')',3);
	// 	}
	// 	if(ENV_MODE == 'DEVELOPMENT' || ENV_MODE == 'MAINTENANCE'){
	// 		$clientIp = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'undetected';
	// 		if(!in_array($clientIp, $bootstrap->configs->env_ip_exemption) && ENV_MODE != 'DEVELOPMENT'){
	// 			showProductionError($e);
	// 		}
	// 		else{
	// 			echo '
	// 				<style>
	// 				 	#exception-handler{ font-size: 14px; text-align: center; font-family: "Open Sans","Arial"; line-height: 1.35em;}
	// 				 	#exception-handler h1 { margin:0;font-size:0.9em;font-weight:bold;text-transform:none;color:#fff; text-transform: uppercase; }
	// 				 	#exception-handler h2 { font-size: 1.2em; }
	// 				 	#exception-handler h3 { font-weight: normal; font-style: italic; display: block; font-size: 0.9em; color: #8f8f8f; }
	// 				 	#exception-handler table{ border:0; font-size:12px; }
	// 				 	#exception-handler td, th{ padding: 5px 15px; border-bottom : 1px solid #eee; color: #7f7f7f; }
	// 				</style>
	// 				<div id="exception-handler">
	// 				    <div style="margin:0 0 25px 0; padding: 10px; color: #fff; background: #2d2d2d;">
	// 				        <h1>Exception</h1>
	// 				    </div>
	// 				    '.get_class($e).'
	// 				    <br/>
	// 				    <p>
	// 				       '.$e->getMessage().' <h3>('.$e->getFile().' on line '.$e->getLine().')</h3>
	// 				    </p>
	// 				    <br/>
	// 				    <center>
	// 				    	<h2>Stack Trace</h2>
	// 				    	<table>
	// 				    		<tr>
	// 				    			<th>Line</th>
	// 				    			<th>File</th>
	// 				    			<th>Trace</th>
	// 				    		</tr> ';
	// 				    			foreach ($e->getTrace() as $trace) {
	// 				    				echo '<tr>';
	// 				    					echo '<td>'.(isset($trace['line']) ? $trace['line'] : '-').'</td>';
	// 				    					echo '<td>'.(isset($trace['file']) ? $trace['file'] : '-').'</td>';
	// 				    					echo '<td>'.((isset($trace['class'])) ? $trace['class'] : '-' ).''.
	// 				    						((isset($trace['function'])) ? '::'.$trace['function'] : '-' );
	// 				    						echo '(';
	// 				    						if(isset($trace['args'])){
	// 				    							if(!empty($trace['args'])){
	// 				    								$args = '';
	// 				    								foreach ($trace['args'] as $arg) {
	// 				    									$args .= gettype($arg).',';
	// 				    								}
	// 				    								echo rtrim($args,',');
	// 				    							}
	// 				    						}
	// 				    						echo ')';
	// 				    				echo '</td>';
	// 				    				echo '</tr>';
	// 				    			}
	// 				   echo '</table>
	// 				    </center>
	// 				</div>
	// 			';
	// 		}
	// 	}
	// 	else{
	// 		showProductionError($e);
	// 	}
	// }
	// function showProductionError($e){
	// 	global $bootstrap;
	// 	if(!headers_sent()){
	// 		if(isset($bootstrap->configs->baseUrl)){
	// 			header('Location: '.$bootstrap->configs->baseUrl);
	// 		}
	// 		else{
	// 			header('HTTP/1.1 503 Service Unavailable');
	// 			header('Status: 503 Service Unavailable');
	// 			echo '<head><title>Service Unavailable</title></head><h1 style="position: absolute;
	// 					top: 20%;
	// 				    width: 100%;
	// 				    font-family: monospace,Arial;
	// 				    text-align: center;
	// 				    color: #000;
	// 				    font-weight: normal;">
	// 				    	Oops, we could not load the page.
	// 				    </h1>';
	// 		}
	// 		exit;
	// 	}
	// }
?>
