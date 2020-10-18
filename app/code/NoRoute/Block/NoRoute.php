<?php
	namespace NoRoute\Block;
	class NoRoute extends \Lenkenith\Framework\View\Block {
		public function getFinalOnPageSeoTags(){
			$meta = array(
					'title' => 'Page not found | '.$this->context->getConfig()->website->name,
					'url' 	=> $this->context->getConfig()->baseUrl.$this->getRequest()->getRawRequestPath()
				);
			return $this->helper->getOnPageSeo()->getMetaHtml($meta);
		}
	}
?>