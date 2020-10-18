<?php
	namespace Lenkenith\Core;
	class UrlRewriteManager {
		private $dbConfig;
		public function __construct(\Lenkenith\Base\DatabaseConfiguration $databaseConfig){
			$this->dbConfig = $databaseConfig;
		}
		public function getRewrite($request_path){
			$rewrite = false;
			try{
				$dbh = @new \PDO('mysql:host='.$this->dbConfig->server.';dbname='.$this->dbConfig->database.'', $this->dbConfig->user, $this->dbConfig->pass);
				$rewrites = $dbh->query('SELECT * from '.$this->dbConfig->prefix.'url_rewrites');
				if($rewrites){
					foreach ($rewrites as $rw) {
						if( (rtrim(trim($request_path),'/')) == (rtrim(trim($rw['request_path']),'/')) ){
							$rewrite = explode('/', $rw['redirect_path']);
							$redirect_type = $rw['redirect_type'];
							$redirect_code = $rw['redirect_code'];
							$path = $rw['redirect_path'];
							break;
						}
					}
				}
				$dbh = null;
				unset($dbh);
			}
			catch(\Exception $e){
				throw new \Lenkenith\Exception\DataConnectionFailureException($e->getMessage(), 1003);	
				return false;			
			}			
			return (!$rewrite) ? false : array('segments' => $rewrite, 'code' => $redirect_code, 'type' => $redirect_type, 'path' => $path);
		}
	}