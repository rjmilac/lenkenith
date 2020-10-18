<?php
	namespace Lenkenith\Framework\Controller;

	class Context{

		
		private $loader, $layout,  $registry, $config, $request;

		public function __construct(\Lenkenith\Core\Loader $loader,\Lenkenith\Base\Configuration $configuration){
			$this->loader 	= $loader;
			$this->config 	= $configuration;
			$this->layout 	= $this->loader->get('\Lenkenith\Framework\View\Layout');
			$this->registry	= $this->loader->get('\Lenkenith\Core\Registry');
		}

		public function getRegistry(){
			return $this->registry;
		}

		public function getLoader(){
			return $this->loader;
		}

		public function getConfiguration(){
			return $this->config;
		}

		public function getLayout(){
			if(!$this->layout instanceof \Lenkenith\Framework\View\Layout){ 
				$this->layout = $this->loader->get('\Lenkenith\Framework\View\Layout');
			}
			return $this->layout;
		}

	}
?>