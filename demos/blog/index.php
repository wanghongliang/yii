<?php 
/**
cal(10,4,5);
	 function cal(	$len=9,$pos=4,$bingo=3){
	 	
	 	//if( $bingo>$pos ) return array();
	 	$results =array(); 
	 	//从中最大的球数计算
	 	for( $i=0;$i<=$pos;$i++ ){
	 		//echo '基数c:'.($pos-$i).','.($len-$i);
	 		$results[$i]= combineN(  $i,$bingo)* combineN($pos-$i, $len-$bingo);
	 	}
	 	
	 	print_r( $results );
	 	echo 'ok';
	 	 
	 	 
	 }
	 
	 
	 function tc(	$len=9,$pos=4,$bingo=3){
	 		 	//$len = 9;
	 	$codes = array();
	 	for($i=1;$i<=$len;$i++){
	 		$codes[]=$i;
	 	}
	 	
	 	//echo count($codes).'个球';
	 	//$pos = 4;
	 	//$bingo = 3;
	 	
	 	$comCodes =  toCombine($codes,$pos);
	 	
	 	//echo $pos.'个一注,共'.count($comCodes).'注,共中'.$bingo.'个球';
	 	if( $bingo<1 ){ return array(); }
	 	$codes_c = array_slice( $codes , 0,$bingo );
	 	
	  
	 	$j=array();
	 	foreach( $comCodes as $k => $v ){
	 		$i=0;
	 		foreach( $codes_c as $x=>$y ){
	 			if( in_array($y,$v) ){
	 				++$i;
	 			}
	 		}
	 		//if( $i>=1 && $i<=$bingo ){
	 			$j[$i]++;
	 		//}
	 	}
	 	
	 	//print_r( $j );
	 	
	 	return $j;
	 }
	 
	 	function combineN($k,$n){  
		if( $n<$k ){ return 0; } $r=1; while($k){ $r*=$n--/$k--; } return $r; 
	}
	
	
		function toCombine($arr,$size=1) {
		  $len = count($arr);
		  $max = pow(2,$len);//2幂
		  $min = pow(2,$size)-1;//2幂
		  $r_arr = array();
		  for($i=$min; $i<$max;$i++){
			   $count = 0;
			   $t_arr = array();
			   for($j=0; $j<$len; $j++){
				    $a = pow(2, $j);
				    $t = $i&$a;//按位与
				    if($t == $a){
				     $t_arr[] = $arr[$j];
				     $count++;
				    }
			   }  
			   if($count == $size){
			   	 $r_arr[] = $t_arr;   
			   }   
		  }
		  return $r_arr;
		 }	 
exit;
$code = '04-08-11-12-15-16-17-18-21-23-24-25';
 
$arr = explode( '-',$code ); 

//print_r( $arr );
$newArr = toCombine( $arr , 5 );

echo count( $newArr);

echo '<br>';

//echo count( $newArr );
$seach = array('04','08','11' );

$j=0;
foreach( $newArr as $k=>$v ){
	
	$i=0;
	foreach( $seach as $x=>$y ){
		if( in_array($y,$v)){
			++$i;
		}
	}
	if( $i==3 ){
		$j+=1;	
	}
}
echo $j;

function toCombine2($arr,$size=1) {
  $len = count($arr);
  $max = pow(2,$len);//2幂
  $min = pow(2,$size)-1;//2幂
  $r_arr = array();
  for($i=$min; $i<$max;$i++){
	   $count = 0;
	   $t_arr = array();
	   for($j=0; $j<$len; $j++){
		    $a = pow(2, $j);
		    $t = $i&$a;//按位与
		    if($t == $a){
		     $t_arr[] = $arr[$j];
		     $count++;
		    }
	   }  
	   if($count == $size){
	   	 $r_arr[] = $t_arr;   
	   }   
  }
  return $r_arr;
 }
exit;
**/
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