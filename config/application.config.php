<?php
	$_CONFIGS = array(
		// Env mode (DEVELOPMENT, PRODUCTION, OR MAINTENANCE) defaults to DEVELOPMENT
		'env_mode' 			=> 'DEVELOPMENT',
		// IP whitelist on env mode implementation (e.g. IPs that are not affected with env restrictions,like MAINTENANCE mode)
		'env_ip_exemption'  => array('::1'),
		// inner directory path where the app is installed if it is not on the root.
		'installation_dir'  => 'base_skel/',
		// (No use) Default base url in case auto env detection mechanism fails.
		'default_base' 		=> '',
		// extension config for developers use only
		'work_offline' 		=> true,
		// skin theme config
		'theme' 			=> 'default',
		// caching config
		'caching' 			=> array(
				// skin resources will be cached. Cached version of a resource is given to a skin resource request.
				'enable_skin_caching' => false
			),
		// directory configs
		'directories'  		=> array(
				// if you changed the next line from 'system', 			
				'system' 		=> 'system', 
				// edit also the directories with name 'system' on system/Core/Bootstrap.php
				'pub' 			=> 'pub',
				'utils' 		=> 'utils',
				'uploads' 		=> 'uploads',
				'skin' 			=> 'skin',
				'app' 			=> 'app',
				'code' 			=> 'code',
				'skin' 			=> 'skin',
				'controller' 	=> 'Controller',
				'model' 		=> 'Model',
				'block' 		=> 'Block',
				'helper' 		=> 'Helper',
				'plugin' 		=> 'Plugin',
				'view' 			=> 'View',
				'library' 		=> 'Library'
			),
		// website configs
		'website' 			=> array(
				'name' 			=> 'My Web Application',
				'title' 		=> 'My Web Application',
				'description' 	=> 'Lorem Ipsum',
				'image' 		=> 'favicon.ico',
				'tags' 			=> 'website, about'
			),
		// session configs
		'session' 			=> array(
				// session name to use
				'name' 			=> 'default_session',
				// clear all cookies on session destroy
				'clear_cookies_on_destroy'  => true,
				// Invalidate token if last activity was >= (1800 seconds) 30 minutes ago (unix timestamp diff)
				'token_invalidation_after_idle_time' => 1800 
			),
		// default module routes
		'default_modules' 	=> array(
				// default
				'default' 		=> array('module' => 'Home', 'controller' => 'Index', 'action' => 'index'),
				// no routes confirmed with the given request
				'no_route' 		=> array('module' => 'NoRoute', 'controller' => 'PageNotFound', 'action' => 'index'),
				// environment / request route is on maintenance mode with the request coming from unexempted ip
				'maintenance' 	=> array('module' => 'NoRoute', 'controller' => 'Maintenance', 'action' => 'index')
			)
	);
?>