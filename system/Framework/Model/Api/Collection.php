<?php
	namespace  Lenkenith\Framework\Model\Api;
	class Collection implements \Lenkenith\Framework\Model\Api\CollectionInterface{
		private $query, $items, $dataObjectClass;
		public function __construct($query, $dataObjectClass = false){
			$this->query = $query;
			$this->dataObjectClass = $dataObjectClass;
			if(!$this->dataObjectClass instanceof \Lenkenith\Framework\Api\DataObjectInterface){
				$this->dataObjectClass = 'Lenkenith\Framework\Api\DataObject';
			}
			$this->items = array();
			$this->loadItems();
		}
		private function loadItems(){
			if($this->query instanceof \PDOStatement){
				$result = $this->query->fetchAll();
				foreach ($result as $item) {
					$this->items[] = new $this->dataObjectClass($item);
				}
			}
			else if(is_array($this->query)){
				$this->items = new $this->dataObjectClass($this->query);
			}		
			else{
				$this->items = $this->query;
			}	
			return $this;
		}
		public function count(){
			if($this->query instanceof \PDOStatement){
				return $this->query->rowCount();
			}
			return count($this->query);
		}
		public function getItems(){
			return $this->items;
		}
		public function getFirstItem(){
			$item = $this->items;
			if(isset($this->items[0])){
				$item = $this->items[0];
			}
			if(is_array($item) && !$item instanceof $this->dataObjectClass){
				$item = new $this->dataObjectClass($item);
			}
			return $item;
		}
	}
?>