<?php 

// change the following paths if necessary

//如果有必需请改变下列的目录
$yii=dirname(__FILE__).'/../../framework/yii.php';	//主框架
$config=dirname(__FILE__).'/protected/config/main.php'; //主配置文件

// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);  //定义是否是调试模式

require_once($yii);
//print_r( Yii::createWebApplication($config) );


//$web = new CWebApplication();
 
//print_r( $web );

//创建一个web应用
Yii::createWebApplication($config)->run();

//echo '测试!';


function debug()
{
	$str = '';
	/*
   * 引用其它错误信息报告
   */
    if ( function_exists( 'debug_backtrace' ))
	{
		$backtrace = debug_backtrace();
        $str .='<div style="font-family:Arail;" >错误信息:';
       //print_r($backtrace);
		for( $i = count( $backtrace ) - 1; $i >= 0; --$i )
		{
			
            $str .='<br><br>';
			if (isset( $backtrace[$i]['file'] )) {
				$str .='<div style="color:blue;" >File :'.$backtrace[$i]['file'].'</div>';
			}
			if (isset( $backtrace[$i]['line'] )) {
				$str .='<div style="color:red;" >Line :'.$backtrace[$i]['line'].'</div>';
			}
			if (isset( $backtrace[$i]['class'] )) {
				$str .='<div>Class :'. $backtrace[$i]['class'].'</div>';
			}
            /*
            if (isset( $backtrace[$i]['object'] )) {
				$str .='<br>Object :',$backtrace[$i][5];
                $backtrace[$i]['object']->toString();
			}

            */
			if (isset( $backtrace[$i]['function'] )) {
				$str .='<div>Function :'. $backtrace[$i]['function'].'</div>';
			}
			if (isset( $backtrace[$i]['type'] )) {
				$str .='<div>Type :'. $backtrace[$i]['type'].'</div>';
			}

			
			if (isset( $backtrace[$i]['args'] )) {
				$str .='<div>Args :'. print_r($backtrace[$i]['args']).'</div>';
			}
			
		}
        $str .='</div>';
	}
    $str .='<br/><br/>';
	echo $str;exit;
	return $str;
}