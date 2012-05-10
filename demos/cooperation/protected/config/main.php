<?php

// 定义路径别名，用于加载类库文件
// Yii::setPathOfAlias('local','path/to/local-folder');

// 主要配置信息
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..', //应用目录
	'name'=>'合作管理后台',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'defaultController'=>'site',	//默认的控制器

	//针对组件的配置信息
	'components'=>array(
		'user'=>array(
			// 开启COOKIE认证方式
			'allowAutoLogin'=>true,
		), 
		//配置数据库连接组件
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=168cai',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'test',
			'charset' => 'gb2312',
			'tablePrefix' => '',
		), 
		'errorHandler'=>array(
			// 用 'site/error' 方法显示错误信息
            'errorAction'=>'site/error',
        ),

		//定义URL伪静态信息
        'urlManager'=>array(
        	'urlFormat'=>'path',
        	'rules'=>array(
        		'post/<id:\d+>/<title:.*?>'=>'post/view',
        		'posts/<tag:.*?>'=>'post/index',
        		'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
        	),
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),

				array(
					'class'=>'CWebLogRoute',
				),
			
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require(dirname(__FILE__).'/params.php'),
);