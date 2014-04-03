<?php

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'ImageCrop',
	'preload'=>array('log'),
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),
	'components' => array(
		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => false,
			'rules' => array(
				'site/loadimageonce/<uuid:[-A-Za-z0-9]+>/<part:\d+>' => 'site/loadimageonce',
				'site/loadimage/<uuid:[-A-Za-z0-9]+>/<part:\d+>' => 'site/loadimage',
				'site/viewimage/<uuid:[-A-Za-z0-9]+>' => 'site/viewimage',
			)
		),
		'db' => array(
			'connectionString' => 'mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=yii',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'tester',
			'charset' => 'utf8',
			'schemaCachingDuration' => 31556926,
			'enableParamLogging' => false,
			'enableProfiling' => false,
		),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning, info',
				),
			),
		),
	),

	'params'=>array(
		'imageSavePath'=> dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.
							DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR,
		'imageMaxDimension'=> 4096,
		'imageResizeTo'=> 400,
		'nginxXAccelPath' => '/pimg/',
	),
);
