<?php
	$_CONSOLE['handler_info'] = [
			'app' => 'lenkenith',
			'version' => '1.0'
		];
	$_CONSOLE['command_list'] = [
		'xmlsitemap' => [
			'action' => [
				'generate' => [
					'params' => [
						'start' => 0
					]
				]
			],
			'class' => '\Plop\Cli\XmlSitemap'
		]					
	];
?>