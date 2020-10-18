<?php
	namespace Lenkenith\Framework\Model\Api;
	class Factory implements \Lenkenith\Framework\Model\Api\FactoryInterface{		
		protected $opts, $resultCollection, $__resourceModel;
		public function __construct(\Lenkenith\Framework\Model\Api\ResourceModelInterface $resourceModel){
			$this->__resourceModel = $resourceModel;
		}
		public function getResourceModel(){
			return $this->__resourceModel;
		}
		public function load($returnCollection = true){ 
			if(is_numeric($returnCollection)){
				$this->opts['filter'] 	= array( $this->__resourceModel->getBasePrimaryKey() => $returnCollection );
				$returnCollection 		= true;
			}
			$this->resultCollection = new \Lenkenith\Framework\Model\Api\Collection($this->__resourceModel->fetch($this->opts));
			$this->opts 			= array();
			return ($returnCollection === true) ? $this->resultCollection : $this;
		}
		public function addAttributeToFilter($attribute, $filter){
			if(!isset($this->opts['filter'])) { $this->opts['filter'] = array(); }
			$this->opts['filter'][$attribute] = $filter;
			return $this;
		}
		public function addFilterGroup($filters){
			if(!isset($this->opts['filter_group'])) { $this->opts['filter_groupr'] = array(); }
			$this->opts['filter_group'][] = $filters;
			return $this;
		}
		public function setPageSize($size){
			if(!isset($this->opts['page_size'])) { $this->opts['page_size'] = ''; }
			$this->opts['page_size'] = $size;
			return $this;
		}
		public function setCurrPage($page){
			if(!isset($this->opts['curr_page'])) { $this->opts['curr_page'] = ''; }
			$this->opts['curr_page'] = $page;
			return $this;
		}
		public function addAttributeToSort($attribute,$direction = 'ASC'){
			if(!isset($this->opts['sort'])) { $this->opts['sort'] = array(); }
			$attributeSort = $attribute.' '.$direction;
			array_push($this->opts['sort'],$attributeSort);
			return $this;
		}
		public function addAttributeToSelect($attribute){
			if(!isset($this->opts['select'])) { $this->opts['select'] = array(); }
			if(!is_array($attribute)) { array_push($this->opts['select'],$attribute); }
			else{ $this->opts['select'] = $attribute; }
			return $this;
		}
		public function join($table, $condition , $method = 'JOIN', $alias = null){
			if(!isset($this->opts['join'])) { $this->opts['join'] = array(); }
			$table_frags = preg_split("/ as /i", $table);
			$alias = (isset($table_frags[1])) ? $table_frags[1] : $alias;
			$alias = ($alias == null) ? strtolower(preg_replace('% %', '', $table)) : $alias;	
			$this->opts['join'][] = array('table' => $table_frags[0], 'on' => $condition, 'method' => $method, 'alias' => $alias);
			return $this;
		}
		public function create(){
			$this->_clear();
			return $this;
		}
		public function _clear(){
			$this->opts = array();
			$this->resultCollection = array();
			return $this;
		}
		public function getCollection(){
			return (!empty($this->resultCollection)) ? $this->resultCollection : $this->load(true);;
		}
		public function isEmpty(){
			return (empty($this->resultCollection)) ? true : false;
		}
	}
?>