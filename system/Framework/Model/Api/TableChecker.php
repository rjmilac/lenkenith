<?php
	namespace  Lenkenith\Framework\Model\Api;
	class TableChecker {
		private $_context;
		public function __construct(\Lenkenith\Framework\Model\Context $context){
			$this->_context 	= $context;
		}
		protected function getContext(){
			return $this->_context;
		}
		protected function getConfig(){
			return $this->_context->getConfig();
		}
		protected function getConnection($key = 'default'){
			return $this->_context->getConnection($key);
		}
		protected function closeConnection($key){
			return $this->_context->closeConnection($key);
		}
		public function checkTables(array $required_tables){
			$result = true;
			foreach($required_tables as $table) {
				if(!$this->tableExists($table)){
					$result = false;
					throw new \Lenkenith\Exception\TableNotFoundException(
							'Admin Module : Couldn\'t find '.$this->getConfig()->getDatabaseConfiguration()->prefix.$table.' table on the database.'
						);	
					break;
				}
			}
			return $result;
		}
		public function tableExists($table){
			if(is_string($table)){
				try {
					$query = $this->getConnection()->prepare("SHOW TABLES LIKE ?");
					$query->execute(array($this->getConfig()->getDatabaseConfiguration()->prefix.$table));
					return ($query->rowCount() > 0);
			    } catch (\Exception $e) {
			        return false;
			    }
			}	
			return false;
		}
	}

?>