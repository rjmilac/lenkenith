<?php
	namespace Home\Block;
	class Home extends \Lenkenith\Framework\View\Block {
		public function __construct(\Lenkenith\Framework\View\Context $context, Array $data){
			parent::__construct($context,$data);
		}
		public function getFinalOnPageSeoTags(){
			$meta = $this->helper->getOnPageSeo()->mergeMeta(array(
					'title' => 'Home'
				));
			return $this->helper->getOnPageSeo()->getMetaHtml($meta);
		}
	}
?>