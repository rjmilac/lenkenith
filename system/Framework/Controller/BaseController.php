<?php
	namespace Lenkenith\Framework\Controller;

	class BaseController {

		private $__loader, $__layout;
		protected $registry, $_data;

		public function __construct(\Lenkenith\Framework\Controller\Context $context){
			$this->registry = $context->getRegistry();
			$this->__loader = $context->getLoader();
			$this->__config = $context->getConfiguration();
			$this->__layout = $context->getLayout();
		}

		protected function getRequest(){
			return $this->registry->get('current_request');
		}

		protected function getLoader(){
			return $this->__loader;
		}

		protected function getConfiguration(){
			return $this->__config;
		}

		protected function getLayout(){
			return $this->__layout;
		}

		protected function loadLibraryFile($module,$file){
			$dirConfig = $this->__config->getConfigs()->directories;
			if(file_exists($dirConfig->app.'/'.$dirConfig->code.'/'.$module.'/'.$dirConfig->library.'/'.ltrim($file.'/'))){
				include $dirConfig->app.'/'.$dirConfig->code.'/'.$module.'/'.$dirConfig->library.'/'.ltrim($file.'/');
				return true;
			}
			return false;
		}

		protected function loadPluginFile($module,$file){
			$dirConfig = $this->__config->getConfigs()->directories;
			if(file_exists($dirConfig->app.'/'.$dirConfig->code.'/'.$module.'/'.$dirConfig->plugin.'/'.ltrim($file.'/'))){
				include $dirConfig->app.'/'.$dirConfig->code.'/'.$module.'/'.$dirConfig->plugin.'/'.ltrim($file.'/');
				return true;
			}
			return false;
		}

		protected function getRedirect($url){
			$baseUrl 	= $this->__config->getConfigs()->baseUrl;
			return $baseUrl.preg_replace('%'.$baseUrl.'%', '', $url);
		}

		protected function redirect($url,$code=302,$continue=false,$js=false){
			$baseUrl 	= $this->__config->getConfigs()->baseUrl;
			$url 		= $baseUrl.preg_replace('%'.$baseUrl.'%', '', $url);
			if($js == false){
				if (!headers_sent()) {
					header("Location: ".$url, true, $code);
				}	
				else{
					echo "<script>window.location='".$url."'</script>";
					echo '<noscript><meta http-equiv="refresh" content="0;url='.$url.'" /></noscript>';
				}		
			}
			else{
				echo "<script>window.location='".$url."'</script>";
				echo '<noscript><meta http-equiv="refresh" content="0;url='.$url.'" /></noscript>';
			}
			if($continue == false){
				exit();
			}
		}

	}
?>