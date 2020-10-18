<?php
	namespace Lenkenith\Framework\View;
	class Layout{
		protected $configs;
		public $helper;
		private $__loader, $configuration, $registry;
		public function __construct(\Lenkenith\Core\Loader $loader,
			\Lenkenith\Base\Configuration $configuration,
			\Lenkenith\Core\Registry $registry){
			$this->configuration 	= $configuration;
			$this->configs 			= $configuration->getConfigs();
			$this->__loader 		= $loader;		
			$this->registry 		= $registry;			
			$this->helper 			= $this->__loader->get('\Lenkenith\Framework\View\Helper\Page');
		}
		public function getConfig(){
			return $this->configs;
		}
		public function getRegistry(){
			return $this->registry;
		}
		public function getLoader(){
			return $this->__loader;
		}
		public function getTemplateFilePath($template){
			$templateFrags = explode('::', $template);
			if(!isset($templateFrags[1])){ 
				throw new \Lenkenith\Exception\InvalidArgumentsException("'".$template."' invalid argument supplied for Layout::getTemplateFile(template). Proper format should be 'Module::path/to/your/file/excluding/module-view-directory'", 1011); 
			}
			$moduleDir = $this->configs->directories->app.'/'.$this->configs->directories->code.'/'.$templateFrags[0];
			if(is_dir($moduleDir)){
				$filePath = $moduleDir.'/'.$this->configs->directories->view.'/'.$templateFrags[1];
				if(file_exists($filePath)){
					return $filePath;
				}
				else{
					throw new \Lenkenith\Exception\FileNotFoundException("'".$filePath."' template file not found", 1011);	
				}
			}
			else{
				throw new \Lenkenith\Exception\ModuleNotFoundException("'".$templateFrags[0]."' module doesn't exists", 1011);				
			}
		}
		private function createTemplate($template, $_block = null, $args = array()){
			if(!empty($_block)){
				if(is_subclass_of($_block, '\Lenkenith\Framework\View\Block') || $_block instanceof \Lenkenith\Framework\View\Block){
					$block = $_block;
				}
				else{
					$block = $this->__loader->create($_block,$args);	
				}				
			}
			return $this->__loader->create(
					'\Lenkenith\Framework\View\Template',
					array(
						$this->getTemplateFilePath($template),
						$this,
						(isset($block)) ? $block : $this->__loader->create('\Lenkenith\Framework\View\Block')
					)
				);
		}
		public function loadTemplateFile($template,$_block = null, $args = array()){
			return $this->createTemplate($template, $_block, $args)->load();
		}
		public function getTemplate($template,$_block = null, $args = array()){
			return $this->createTemplate($template, $_block, $args);
		}
	}
?>