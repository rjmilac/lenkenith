<?php
	namespace Lenkenith\Framework\Model\Api;

	interface FactoryInterface {
		function addAttributeToFilter($attribute, $filter);
		function addFilterGroup($filters);
		function addAttributeToSelect($attribute);
		function addAttributeToSort($attribute,$direction);
		function setPageSize($size);
		function setCurrPage($page);
		function join($table, $condition, $method, $alias);
		function load($returnCollection);
		function create();
		function getCollection();
		function _clear();
	}

?>