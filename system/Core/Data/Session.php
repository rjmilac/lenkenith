<?php
	namespace Lenkenith\Core\Data;

	class Session implements \Lenkenith\Core\Api\LocalizedStorageInterface{

		private $options;

		public function __construct($init){
			$init = (!is_array($init)) ? ( (gettype($init) == 'object') ? (json_decode(json_encode($init), true)) : array() ) : $init;
			$defaults 		= array(
					'name' 						=> 'default_session',
					'clear_cookies_on_destroy'  => true,
					'token_invalidation_after_idle_time' => 1800 // (in ms) 30 minutes 
				);
			$this->options 	= array_merge($defaults, $init);
		}

		protected function hasAlreadyStarted(){
			if (version_compare(phpversion(), '5.4.0', '<')) {
			    return (!isset($_SESSION)) ? false : ((session_id() == '') ? false : true);
			}
			else
			{
			    return (session_status() == PHP_SESSION_NONE) ? false : true;
			}
		}

		public function isExpired($last_activity){
			$last_activity = (strtotime($last_activity)) ? strtotime($last_activity) : $last_activity;
			return ((time() - $last_activity) >= $this->options['token_invalidation_after_idle_time']);
		}

		public function getOptions(){
			return $this->options;
		}

		public function clearCookies(){
			if (isset($_SERVER['HTTP_COOKIE'])) {
			    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
			    foreach($cookies as $cookie) {
			        $parts = explode('=', $cookie);
			        $name = trim($parts[0]);
			        setcookie($name, '', time()-1000);
			        setcookie($name, '', time()-1000, '/');
			    }
			}
			return true;
		}

		public function shutdown($ic = ''){
			$includeCookies = ($ic == '') ? ( (isset($this->options['clear_cookies_on_destroy'])) ? $this->options['clear_cookies_on_destroy'] : true ) : $ic;
			if($this->hasAlreadyStarted()){
				session_destroy();
			}			
			if ($includeCookies) {
			    $this->clearCookies();
			}
			return false;
		}

		public function start($name = ''){
			$name = (empty($name)) ? $this->options['name'] : $name;
			if(!$this->hasAlreadyStarted()){
				session_name($name);
				session_start();
			}
		}

		public function remove($key){
			if($this->has($key)){
				unset($_SESSION[$key]);
				return true;
			}
			return false;
		}

		public function has($key){
			return (isset($_SESSION[$key]));
		}

		public function get($key){
			if($this->has($key)){
				return $_SESSION[$key];
			}
			return false;
		}

		public function store($key,$value){
			$_SESSION[$key] = $value;
			return true;
		}

	}
?>