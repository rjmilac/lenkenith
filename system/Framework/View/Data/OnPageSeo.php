<?php
	
	namespace Lenkenith\Framework\View\Data;

	class OnPageSeo{

		private $meta, $defaultMeta, $baseUrl;

		public function __construct(\Lenkenith\Base\Configuration $configuration, Array $prop){
			$configs = $configuration->getConfigs();
			$this->baseUrl = $configs->baseUrl;
			$meta= array(
				'name' 	=> $configs->website->name,
				'title' => $configs->website->title,
				'desc'	=> $configs->website->description,
				'url'	=> $configs->baseUrl,
				'image' => $configs->baseUrl.$configs->website->image,
				'keywords'  => $configs->website->tags
			);
			foreach ($prop as $key => $value) {
				if(empty($value)){
					$prop[$key] = (isset($meta[$key])) ? $meta[$key] : $value;
				}
			}			
			$prop 		= array_merge($meta,$prop);
			$this->meta = new \Lenkenith\Framework\Api\DataObject($prop);
		}

		public function mergeMeta($ov){
			if(is_array($ov)){ 
				$meta = $this->getMeta();
				foreach ($ov as $key => $value) {
					if($meta->hasData($key)){
						$meta->setData($key,$value);
					}
				}
			}
			return $this->getMeta();
		}

		public function getMetaHtml($override = array()){
			if(!empty($override)){
				$meta = $this->mergeMeta($override);
			}
			else{
				$meta = $this->getMeta();
			}
			if($override instanceof \Lenkenith\Framework\Api\DataObjectInterface){
				$meta = $override;
			}
			$html = '<meta name="keywords" content="'.$meta->getKeywords().'">';
			$html .= '<link rel="shortcut icon" href="'.$this->baseUrl.'favicon.ico">';
			$html .= '<title>'.$meta->getTitle().'</title>';
			$html .= '<meta name="description" content="'.htmlspecialchars(strip_tags($meta->getDesc())).'">';
			$html .= '<meta name="url" content="'.$meta->getUrl().'">';
			$html .= '<meta property="og:title" content="'.$meta->getTitle().'" />';
			$html .= '<meta property="og:description" content="'.htmlspecialchars(strip_tags($meta->getDesc())).'" />';
			$html .= '<meta property="og:url" content="'.$meta->getUrl().'" />';
			$html .= '<meta property="og:image" content="'.$meta->getImage().'" />';
			$html .= '<meta property="og:site_name" content="'.$meta->getName().'">';
			$html .= '<meta name="twitter:card" content="summary">';
			$html .= '<meta name="twitter:title" content="'.$meta->getTitle().'">';
			$html .= '<meta name="twitter:url" content="'.$meta->getUrl().'">';
			$html .= '<meta name="twitter:image" content="'.$meta->getImage().'">';
			$html .= '<meta name="twitter:description" content="'.htmlspecialchars(strip_tags($meta->getDesc())).'">';
			return $html;
		}

		public function getMeta(){
			return $this->meta;
		}

	}

?>