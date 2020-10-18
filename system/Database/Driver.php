<?php
	namespace Lenkenith\Database;

	class Driver {

		private $_dbConfig;
		private $_connTmpCache;

		public function __construct(\Lenkenith\Core\Loader $loader,\Lenkenith\Base\Configuration $configuration){
			$this->_dbConfig 		= $configuration->getDatabaseConfiguration();
			$this->_connTmpCache 	= $loader->create('\Lenkenith\Database\Data\Cache');
		}

		public function get($key = 'default'){
			if($this->_connTmpCache->has($key)){
				return ($this->_connTmpCache->get($key) != null) ? $this->_connTmpCache->get($key) : $this->connect($key);
			}
			return $this->connect($key);
		}

		public function connect($key = 'default'){
			if($this->_dbConfig->driver == 'mysqli'){
				$connection =  mysqli_connect($this->_dbConfig->server, $this->_dbConfig->user, $this->_dbConfig->pass, $this->_dbConfig->database);
				if(!$connection){
					throw new \Lenkenith\Exception\DataConnectionFailureException('Database Adapter : '.$e->getMessage(), 5103);	
					return false;	
				}
				else{
					
				}
			}
			else{
				try{
					$now 	= new \DateTime();
					$mins 	= $now->getOffset() / 60;
					$sgn 	= ($mins < 0 ? -1 : 1);
					$mins 	= abs($mins);
					$hrs 	= floor($mins / 60);
					$mins 	-= $hrs * 60;
					$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
					$connection = new \PDO('mysql:host='.$this->_dbConfig->server.';dbname='.$this->_dbConfig->database, $this->_dbConfig->user, $this->_dbConfig->pass);
					$connection->exec("SET SESSION time_zone='$offset';");
				}
				catch(\Exception $e){
					throw new \Lenkenith\Exception\DataConnectionFailureException('Database Adapter : '.$e->getMessage(), 5103);		
					return false;			
				}
			}			
			$this->_connTmpCache->store($key,$connection);
			return $connection;
		}

		public function close($key = 'default'){
			if(gettype($key) == 'string'){
				if($this->_connTmpCache->has($key)){
					$this->_connTmpCache->store($key,null);
					$this->_connTmpCache->remove($key);
					return true;
				}
			}
			if($key instanceof \mysqli){
				mysqli_close($key);
			}
		}

	}
?>