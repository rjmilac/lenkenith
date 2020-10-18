<?php
	namespace Admin\Block;
	class Setup extends \Lenkenith\Framework\View\Block {
		public function getFinalOnPageSeoTags(){
			$meta = array(
					'title' => 'Admin Setup | '.$this->context->getConfig()->website->name,
					'url' 	=> $this->context->getConfig()->baseUrl.'admin/login'
				);
			return $this->helper->getOnPageSeo()->getMetaHtml($meta);
		}
	}
?>