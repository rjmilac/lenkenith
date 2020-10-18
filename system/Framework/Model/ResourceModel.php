<?php

	namespace Lenkenith\Framework\Model;

	class ResourceModel{
		private $_context;
		protected $_baseTable = null;
		protected $_basePrimaryKey = 'id';
		public function __construct(\Lenkenith\Framework\Model\Context $context){
			$this->_context 	= $context;
		}
		public function setBasePrimaryKey($key){
			if(is_string($key)){
				 $this->_basePrimaryKey = $key;
			}
			return $this;
		}
		public function getBasePrimaryKey(){
			return  $this->_basePrimaryKey;
		}
		public function setBaseTable($table){
			if(is_string($table) || is_array($table)){
				$this->_baseTable = $table;
			}
			return $this;
		}
		public function getBaseTable(){
			return $this->_baseTable;
		}
		public function fetch(Array $options){
			if(!isset($options['primary_id_column'])) { $options['primary_id_column'] = $this->_basePrimaryKey; }
			if(!isset($options['from'])) { $options['from'] = $this->_baseTable; }
			$query_data = $this->composeFetchQuery($options);
			$query 		= $this->getConnection()->prepare($query_data['statement']);
			if($query){
				$query->execute($query_data['params']);
				return $query;
			}
			else{
				return array();
			}
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
		protected function getOptr($oprt_code){
			switch ($oprt_code) {
				case 'eq':
					return '=';
					break;
				
				default:
					return $oprt_code;
					break;
			}
		}
		protected function composeFetchQuery(Array $options){
			$params 	= array();
			$pageSize   = (isset($options['page_size'])) ? $options['page_size'] : '';
			$currPage 	= (isset($options['curr_page'])) ? $options['curr_page'] : 1;
			$toSort 	= (isset($options['sort'])) ? $options['sort'] : array();
			$toSelect 	= (isset($options['select'])) ? $options['select'] : array('*');
			$toFilter 	= (isset($options['filter'])) ? $options['filter'] : array();
			$filterGrp 	= (isset($options['filter_group'])) ? $options['filter_group'] : array();
			$toJoin 	= (isset($options['join'])) ? $options['join'] : '';	
			$pkIdCol 	= (isset($options['primary_id_column'])) ? $options['primary_id_column'] : 'entity_id';		

			$statement 	= "SELECT ".implode(',',$toSelect)." ";		

			$from = (isset($options['from'])) ? $options['from'] : ''; $as = 'a';
			if(is_array($from)){
				foreach ($options['from'] as $key => $value) {
					$from = $key; $as = $value;
				}
			}
			$statement 	.= "FROM ".$this->getConfig()->getDatabaseConfiguration()->prefix.$from." AS ".$as." ";

			if(!empty($toJoin) && is_array($toJoin)){
				foreach ($toJoin as $j) {
					if(!isset($j['on']) || !isset($j['table'])){
						throw new \Lenkenith\Exception\InvalidArgumentsException('Invalid "join" parameters.');
					}
					$method = (!isset($j['method'])) ? 'JOIN' : $j['method'];
					$alias 	= (!isset($j['alias'])) ? ' AS b' : ' AS '.$j['alias'].' ';
					$statement .= $method." ".$this->getConfig()->getDatabaseConfiguration()->prefix.$j['table'].$alias;
					$statement .= ' ON ('.$j['on'].') ';
				}

			}

			$where 		= (!is_array($toFilter) && empty($filterGrp)) ? $toFilter : ''; $filterPointer = 0;
			if(!empty($filterGrp) && is_array($filterGrp)){
				$fg_pointer = 0;
				foreach ($filterGrp as $fg) {
					if($fg_pointer != 0){
						$where .= (isset($fg['conj'])) ? " ".$fg['conj']." " : ' AND ';
					}
					$where 	.= ' ( ';
					$filters = (!isset($fg['filters'])) ? array() : $fg['filters'];
					$fg_item_pointer = 0;
					foreach ($filters as $key => $value) {
						if($key == 'entity_id'){ $key = $pkIdCol; }
						if(!is_array($value)){ $value = array('optr' => 'eq', 'value' => $value, 'conj' => 'AND'); }
						if($fg_item_pointer != 0){
							$where .= (isset($value['conj'])) ? " ".$value['conj']." " : ' AND ';
						}
						if(!isset($value['optr'])) { $value['optr'] = 'eq'; }
						array_push($params, $value['value']);
						$where .= $key." ".$this->getOptr($value['optr'])." ?";	
						$fg_item_pointer++;
					}
					$where .= ' ) ';
					$fg_pointer++;
				}
				$filterPointer = $fg_pointer;
			}
			
			if(!empty($toFilter) && is_array($toFilter)){
				foreach ($toFilter as $key => $value) {
					if($key == 'entity_id'){ $key = $pkIdCol; }
					if(!is_array($value)){ $value = array('optr' => 'eq', 'value' => $value, 'conj' => 'AND'); }
					if($filterPointer != 0){
						$where .= (isset($value['conj'])) ? " ".$value['conj']." " : ' AND ';
					}
					if(!isset($value['optr'])) { $value['optr'] = 'eq'; }
					array_push($params, $value['value']);
					$where .= $key." ".$this->getOptr($value['optr'])." ?";	
					$filterPointer++;
				}
			}
			$statement 	.= "WHERE ".$where." ";

			if(!empty($toSort)){
				$statement .= "ORDER BY ".implode(', ',$toSort)." ";
			}			

			if(!empty($pageSize)){
				$statement .= "LIMIT ";
				$statement .= (($pageSize * $currPage ) - $pageSize).', ';
				$statement .= $pageSize;
			}
			return array('statement' => $statement, 'params' => $params);			
		}

	}
?>