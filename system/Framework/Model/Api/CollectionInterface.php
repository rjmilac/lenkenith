<?php
	namespace Lenkenith\Framework\Model\Api;

	interface CollectionInterface {
		function count();  
		function getItems();
		function getFirstItem();
	}

?>