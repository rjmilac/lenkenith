<?php
	namespace Plop\Cli;

	class XmlSitemap{

		protected $_configuration, $configs;

		private $xmlSitemapGenerator;

		public function __construct(\Lenkenith\Base\Configuration $configuration,
			\Plop\Cli\XmlSitemap\Generator $xmlSitemapGenerator){
			$this->_configuration = $configuration;
			$this->configs = $this->_configuration->getConfigs();
			$this->_xmlSitemapGenerator = $xmlSitemapGenerator;
		}

		public function generate($url = null){
			if($url == null || !filter_var($url, FILTER_VALIDATE_URL)){
				$url = $this->configs->baseUrl;
			}
			return $this->_xmlSitemapGenerator->generate($url);
		}

	}

?>