<?php
	namespace Lenkenith\Framework\View\Helper;
	class Page{
		protected $metaTags, $opSeo;
		public $configs, $cacheManager;
		public function __construct(\Lenkenith\Base\Configuration $configuration,
			\Lenkenith\Core\CacheManager $cacheManager){	
			$this->configuration 	= $configuration;
			$this->configs 			= $configuration->getConfigs();
			$this->cacheManager 	= $cacheManager;
			$this->opSeo			= new \Lenkenith\Framework\View\Data\OnPageSeo($configuration, array());
		}
		public function getOnPageSeo(){
			return $this->opSeo;
		}
		public function getOnPageSeoMeta(){
			return $this->opSeo->getMeta();
		}
		public function getBaseUrl(){
			return $this->configs->baseUrl;
		}
		public function getQueryStringData($name = ''){
			if(!empty($name)){
				return ((isset($_GET[$name])) ? $_GET[$name] : null);
			}
			return ((isset($_GET)) ? $_GET : null);
		}
		public function getPostData($name = ''){
			if(!empty($name)){
				return ((isset($_POST[$name])) ? $_POST[$name] : null);
			}
			return ((isset($_POST)) ? $_POST : null);
		}
		public function getTheme(){
			return $this->configs->theme;
		}
		public function getServerTimestamp($format = 'F-d-Y h:i:s A'){
			return date($format, time());
		}
		public function getSkinFontStylePath($src,$getFromCache = true,$copyResourcesToCache=true){
			$parentDr = $this->configs->directories->app.'/'.$this->configs->directories->skin.'/'.$this->getTheme().'/fonts';
			$resource = $parentDr.'/'.ltrim($src,'/');
			if($getFromCache && $this->configs->caching->enable_skin_caching){
				$destination 	= $this->getTheme().'/fonts/'.ltrim($src,'/');	
				$destinationDr 	= $this->getTheme().'/fonts';
				if($copyResourcesToCache){
					$this->cacheManager->recurseCopy($parentDr,$destinationDr);
				}	
				$resource = $this->cacheManager->load($resource,$destination);
			}
			return $this->getBaseUrl().$resource;
		}
		public function getSkinResourcePath($src,$getFromCache = true){
			$resource = $this->configs->directories->app.'/'.$this->configs->directories->skin.'/'.$this->getTheme().'/'.ltrim($src,'/');
			if($getFromCache && $this->configs->caching->enable_skin_caching){
				$destination = $this->getTheme().'/'.ltrim($src,'/');
				$resource = $this->cacheManager->load($resource,$destination);
			}
			return $this->getBaseUrl().$resource;
		}	
		public function getModuleResourcePath($module,$src,$getFromCache = true){
			$resource 		= false;
			$resourceDir 	= $this->configs->directories->app.'/'.$this->configs->directories->code;
			$resourceDir 	.= '/'.$module.'/Web/'.$src;
			if(file_exists($resourceDir)){
				$base_dir = $this->configs->directories->app.'/'.$this->configs->directories->skin.'/'.$this->getTheme();
				$destDir = '/generated_resource/'.$module;
				$dirs = explode('/', $destDir); $concat_dir = '';
				foreach ($dirs as $dir) {
					$concat_dir .= $dir.'/';
					if(!is_dir($base_dir.$concat_dir) && !is_file($base_dir.$concat_dir)){
						mkdir($base_dir.rtrim($concat_dir,'/'));
					}
				}
				$dirs = explode('/', $src); $concat_dir = ''; $base_dir = $base_dir.$destDir.'/';
				array_pop($dirs);
				foreach ($dirs as $dir) {
					$concat_dir .= $dir.'/';
					if(!is_dir($base_dir.$concat_dir) && !is_file($base_dir.$concat_dir)){
						mkdir($base_dir.rtrim($concat_dir,'/'));
					}
				}
				if(copy($resourceDir, $base_dir.$src)){
					return $this->getSkinResourcePath('/generated_resource/'.$module.'/'.$src, $getFromCache);
				}
			}
			return $resource;
		}
		public function arrayToDataObject(Array $array){
			return new \Lenkenith\Framework\DataObject($array);
		}	
		public function imageResize($opts=array()){			
			return $this->getBaseUrl().$this->configs->directories->utils.'/zth?'.http_build_query($opts);
		}
		public function redirect($url,$code=302,$continue=false,$js=false){
			$baseUrl 	= $this->getBaseUrl();
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