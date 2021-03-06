<?php
/**
2012-02-11
说明：
URL管理类是工厂类，根据配置信息管理和创建rule，
然后根据 request对象和rule对象匹配，然后根据 rule 对象的规则进行解析 url ，生成GET参数

1，解析 url 信息,根据匹配的 rule 规则进行解析，生成GET参数.
2, 生成 url 信息,根据区配的 rule 规则把参数变量替换到 route 规则中去，并把附加的参数按配置方式，追加到URL后面,并生成后缀信息.


**/
/**
 * CUrlManager class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CUrlManager manages the URLs of Yii Web applications.
 *
 * It provides URL construction ({@link createUrl()}) as well as parsing ({@link parseUrl()}) functionality.
 *
 * URLs managed via CUrlManager can be in one of the following two formats,
 * by setting {@link setUrlFormat urlFormat} property:
 * <ul>
 * <li>'path' format: /path/to/EntryScript.php/name1/value1/name2/value2...</li>
 * <li>'get' format:  /path/to/EntryScript.php?name1=value1&name2=value2...</li>
 * </ul>
 *
 * When using 'path' format, CUrlManager uses a set of {@link setRules rules} to:
 * <ul>
 * <li>parse the requested URL into a route ('ControllerID/ActionID') and GET parameters;</li>
 * <li>create URLs based on the given route and GET parameters.</li>
 * </ul>
 *
 * A rule consists of a route and a pattern. The latter is used by CUrlManager to determine
 * which rule is used for parsing/creating URLs. A pattern is meant to match the path info
 * part of a URL. It may contain named parameters using the syntax '&lt;ParamName:RegExp&gt;'.
 *
 * When parsing a URL, a matching rule will extract the named parameters from the path info
 * and put them into the $_GET variable; when creating a URL, a matching rule will extract
 * the named parameters from $_GET and put them into the path info part of the created URL.
 *
 * If a pattern ends with '/*', it means additional GET parameters may be appended to the path
 * info part of the URL; otherwise, the GET parameters can only appear in the query string part.
 *
 * To specify URL rules, set the {@link setRules rules} property as an array of rules (pattern=>route).
 * For example,
 * <pre>
 * array(
 *     'articles'=>'article/list',
 *     'article/<id:\d+>/*'=>'article/read',
 * )
 * </pre>
 * Two rules are specified in the above:
 * <ul>
 * <li>The first rule says that if the user requests the URL '/path/to/index.php/articles',
 *   it should be treated as '/path/to/index.php/article/list'; and vice versa applies
 *   when constructing such a URL.</li>
 * <li>The second rule contains a named parameter 'id' which is specified using
 *   the &lt;ParamName:RegExp&gt; syntax. It says that if the user requests the URL
 *   '/path/to/index.php/article/13', it should be treated as '/path/to/index.php/article/read?id=13';
 *   and vice versa applies when constructing such a URL.</li>
 * </ul>
 *
 * The route part may contain references to named parameters defined in the pattern part.
 * This allows a rule to be applied to different routes based on matching criteria.
 * For example,
 * <pre>
 * array(
 *      '<_c:(post|comment)>/<id:\d+>/<_a:(create|update|delete)>'=>'<_c>/<_a>',
 *      '<_c:(post|comment)>/<id:\d+>'=>'<_c>/view',
 *      '<_c:(post|comment)>s/*'=>'<_c>/list',
 * )
 * </pre>
 * In the above, we use two named parameters '<_c>' and '<_a>' in the route part. The '<_c>'
 * parameter matches either 'post' or 'comment', while the '<_a>' parameter matches an action ID.
 *
 * Like normal rules, these rules can be used for both parsing and creating URLs.
 * For example, using the rules above, the URL '/index.php/post/123/create'
 * would be parsed as the route 'post/create' with GET parameter 'id' being 123.
 * And given the route 'post/list' and GET parameter 'page' being 2, we should get a URL
 * '/index.php/posts/page/2'.
 *
 * It is also possible to include hostname into the rules for parsing and creating URLs.
 * One may extract part of the hostname to be a GET parameter.
 * For example, the URL <code>http://admin.example.com/en/profile</code> may be parsed into GET parameters
 * <code>user=admin</code> and <code>lang=en</code>. On the other hand, rules with hostname may also be used to
 * create URLs with parameterized hostnames.
 *
 * In order to use parameterized hostnames, simply declare URL rules with host info, e.g.:
 * <pre>
 * array(
 *     'http://<user:\w+>.example.com/<lang:\w+>/profile' => 'user/profile',
 * )
 * </pre>
 *
 * Starting from version 1.1.8, one can write custom URL rule classes and use them for one or several URL rules.
 * For example,
 * <pre>
 * array(
 *   // a standard rule
 *   '<action:(login|logout)>' => 'site/<action>',
 *   // a custom rule using data in DB
 *   array(
 *     'class' => 'application.components.MyUrlRule',
 *     'connectionID' => 'db',
 *   ),
 * )
 * </pre>
 * Please note that the custom URL rule class should extend from {@link CBaseUrlRule} and
 * implement the following two methods,
 * <ul>
 *    <li>{@link CBaseUrlRule::createUrl()}</li>
 *    <li>{@link CBaseUrlRule::parseUrl()}</li>
 * </ul>
 *
 * CUrlManager is a default application component that may be accessed via
 * {@link CWebApplication::getUrlManager()}.
 *
 * @property string $baseUrl The base URL of the application (the part after host name and before query string).
 * If {@link showScriptName} is true, it will include the script name part.
 * Otherwise, it will not, and the ending slashes are stripped off.
 * @property string $urlFormat The URL format. Defaults to 'path'. Valid values include 'path' and 'get'.
 * Please refer to the guide for more details about the difference between these two formats.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CUrlManager.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.web
 * @since 1.0
 */
class CUrlManager extends CApplicationComponent
{
	const CACHE_KEY='Yii.CUrlManager.rules';
	const GET_FORMAT='get';
	const PATH_FORMAT='path';

	/**
	 * @var array the URL rules (pattern=>route).
	 */
	public $rules=array();
	/**
	 * @var string the URL suffix used when in 'path' format.
	 * For example, ".html" can be used so that the URL looks like pointing to a static HTML page. Defaults to empty.
	 */
	public $urlSuffix='';
	/**
	 * @var boolean whether to show entry script name in the constructed URL. Defaults to true.
	 */
	public $showScriptName=true;
	/**
	 * @var boolean whether to append GET parameters to the path info part. Defaults to true.
	 * This property is only effective when {@link urlFormat} is 'path' and is mainly used when
	 * creating URLs. When it is true, GET parameters will be appended to the path info and
	 * separate from each other using slashes. If this is false, GET parameters will be in query part.
	 */
	public $appendParams=true;
	/**
	 * @var string the GET variable name for route. Defaults to 'r'.
	 */
	public $routeVar='r';
	/**
	 * @var boolean whether routes are case-sensitive. Defaults to true. By setting this to false,
	 * the route in the incoming request will be turned to lower case first before further processing.
	 * As a result, you should follow the convention that you use lower case when specifying
	 * controller mapping ({@link CWebApplication::controllerMap}) and action mapping
	 * ({@link CController::actions}). Also, the directory names for organizing controllers should
	 * be in lower case.
	 */
	public $caseSensitive=true;
	/**
	 * @var boolean whether the GET parameter values should match the corresponding
	 * sub-patterns in a rule before using it to create a URL. Defaults to false, meaning
	 * a rule will be used for creating a URL only if its route and parameter names match the given ones.
	 * If this property is set true, then the given parameter values must also match the corresponding
	 * parameter sub-patterns. Note that setting this property to true will degrade performance.
	 * @since 1.1.0
	 */
	public $matchValue=false;
	/**
	 * @var string the ID of the cache application component that is used to cache the parsed URL rules.
	 * Defaults to 'cache' which refers to the primary cache application component.
	 * Set this property to false if you want to disable caching URL rules.
	 */
	public $cacheID='cache';
	/**
	 * @var boolean whether to enable strict URL parsing.
	 * This property is only effective when {@link urlFormat} is 'path'.
	 * If it is set true, then an incoming URL must match one of the {@link rules URL rules}.
	 * Otherwise, it will be treated as an invalid request and trigger a 404 HTTP exception.
	 * Defaults to false.
	 */
	public $useStrictParsing=false;
	/**
	 * @var string the class name or path alias for the URL rule instances. Defaults to 'CUrlRule'.
	 * If you change this to something else, please make sure that the new class must extend from
	 * {@link CBaseUrlRule} and have the same constructor signature as {@link CUrlRule}.
	 * It must also be serializable and autoloadable.
	 * @since 1.1.8
	 */
	public $urlRuleClass='CUrlRule';

	private $_urlFormat=self::GET_FORMAT;
	private $_rules=array();
	private $_baseUrl;


	/**
	 * Initializes the application component.
	 */
	public function init()
	{
		parent::init();
		$this->processRules();
	}

	/**
	 * Processes the URL rules.
	 */
	protected function processRules()
	{
		if(empty($this->rules) || $this->getUrlFormat()===self::GET_FORMAT)
			return;
		if($this->cacheID!==false && ($cache=Yii::app()->getComponent($this->cacheID))!==null)
		{
			$hash=md5(serialize($this->rules));
			if(($data=$cache->get(self::CACHE_KEY))!==false && isset($data[1]) && $data[1]===$hash)
			{
				$this->_rules=$data[0];
				return;
			}
		}
		foreach($this->rules as $pattern=>$route)
			$this->_rules[]=$this->createUrlRule($route,$pattern);
		if(isset($cache))
			$cache->set(self::CACHE_KEY,array($this->_rules,$hash));
	}

	/**
	 * Adds new URL rules.
	 * In order to make the new rules effective, this method must be called BEFORE
	 * {@link CWebApplication::processRequest}.
	 * @param array $rules new URL rules (pattern=>route).
	 * @param boolean $append whether the new URL rules should be appended to the existing ones. If false,
	 * they will be inserted at the beginning.
	 * @since 1.1.4
	 */
	public function addRules($rules, $append=true)
	{
		if ($append)
		{
			foreach($rules as $pattern=>$route)
				$this->_rules[]=$this->createUrlRule($route,$pattern);
		}
		else
		{
			foreach($rules as $pattern=>$route)
				array_unshift($this->_rules, $this->createUrlRule($route,$pattern));
		}
	}

	/**
	 * Creates a URL rule instance.
	 * The default implementation returns a CUrlRule object.
	 * @param mixed $route the route part of the rule. This could be a string or an array
	 * @param string $pattern the pattern part of the rule
	 * @return CUrlRule the URL rule instance
	 * @since 1.1.0
	 */
	protected function createUrlRule($route,$pattern)
	{
		if(is_array($route) && isset($route['class']))
			return $route;
		else
			return new $this->urlRuleClass($route,$pattern);
	}

	/**
	 * Constructs a URL.
	 * @param string $route the controller and the action (e.g. article/read)
	 * @param array $params list of GET parameters (name=>value). Both the name and value will be URL-encoded.
	 * If the name is '#', the corresponding value will be treated as an anchor
	 * and will be appended at the end of the URL.
	 * @param string $ampersand the token separating name-value pairs in the URL. Defaults to '&'.
	 * @return string the constructed URL
	 */
	public function createUrl($route,$params=array(),$ampersand='&')
	{
		unset($params[$this->routeVar]);
		foreach($params as $i=>$param)
			if($param===null)
				$params[$i]='';

		if(isset($params['#']))
		{
			$anchor='#'.$params['#'];
			unset($params['#']);
		}
		else
			$anchor='';
		$route=trim($route,'/');
		foreach($this->_rules as $i=>$rule)
		{
			if(is_array($rule))
				$this->_rules[$i]=$rule=Yii::createComponent($rule);
			if(($url=$rule->createUrl($this,$route,$params,$ampersand))!==false)
			{
				if($rule->hasHostInfo)
					return $url==='' ? '/'.$anchor : $url.$anchor;
				else
					return $this->getBaseUrl().'/'.$url.$anchor;
			}
		}
		return $this->createUrlDefault($route,$params,$ampersand).$anchor;
	}

	/**
	 * Creates a URL based on default settings.
	 * @param string $route the controller and the action (e.g. article/read)
	 * @param array $params list of GET parameters
	 * @param string $ampersand the token separating name-value pairs in the URL.
	 * @return string the constructed URL
	 */
	protected function createUrlDefault($route,$params,$ampersand)
	{
		if($this->getUrlFormat()===self::PATH_FORMAT)
		{
			$url=rtrim($this->getBaseUrl().'/'.$route,'/');
			if($this->appendParams)
			{
				$url=rtrim($url.'/'.$this->createPathInfo($params,'/','/'),'/');
				return $route==='' ? $url : $url.$this->urlSuffix;
			}
			else
			{
				if($route!=='')
					$url.=$this->urlSuffix;
				$query=$this->createPathInfo($params,'=',$ampersand);
				return $query==='' ? $url : $url.'?'.$query;
			}
		}
		else
		{
			$url=$this->getBaseUrl();
			if(!$this->showScriptName)
				$url.='/';
			if($route!=='')
			{
				$url.='?'.$this->routeVar.'='.$route;
				if(($query=$this->createPathInfo($params,'=',$ampersand))!=='')
					$url.=$ampersand.$query;
			}
			else if(($query=$this->createPathInfo($params,'=',$ampersand))!=='')
				$url.='?'.$query;
			return $url;
		}
	}

	/**
	 * Parses the user request.
	 * @param CHttpRequest $request the request application component
	 * @return string the route (controllerID/actionID) and perhaps GET parameters in path format.
	 */
	public function parseUrl($request)
	{
		
		
		//定义两种路由方式，一种是 path /article/list ,另一种是 &com=article&act=list 
		if($this->getUrlFormat()===self::PATH_FORMAT)
		{
			
			//获取url pathinfo信息,便于分析url
			$rawPathInfo=$request->getPathInfo();
			
			
			//移除url后缀字符串,如: .html
			$pathInfo=$this->removeUrlSuffix($rawPathInfo,$this->urlSuffix);
			
			
			//匹配定义的url规则
			foreach($this->_rules as $i=>$rule)
			{
				//数组配置信息
				if(is_array($rule))
					$this->_rules[$i]=$rule=Yii::createComponent($rule);
					
				if(($r=$rule->parseUrl($this,$request,$pathInfo,$rawPathInfo))!==false)
					return isset($_GET[$this->routeVar]) ? $_GET[$this->routeVar] : $r;
			}
			
			//是否进行404报错
			if($this->useStrictParsing)
				throw new CHttpException(404,Yii::t('yii','Unable to resolve the request "{route}".',
					array('{route}'=>$pathInfo)));
			else
				return $pathInfo;
		}
		else if(isset($_GET[$this->routeVar]))
			return $_GET[$this->routeVar];
		else if(isset($_POST[$this->routeVar]))
			return $_POST[$this->routeVar];
		else
			return '';
	}

	/**
	 * Parses a path info into URL segments and saves them to $_GET and $_REQUEST.
	 * @param string $pathInfo path info
	 */
	public function parsePathInfo($pathInfo)
	{
		if($pathInfo==='')
			return;
		$segs=explode('/',$pathInfo.'/');
		$n=count($segs);
		for($i=0;$i<$n-1;$i+=2)
		{
			$key=$segs[$i];
			if($key==='') continue;
			$value=$segs[$i+1];
			if(($pos=strpos($key,'['))!==false && ($m=preg_match_all('/\[(.*?)\]/',$key,$matches))>0)
			{
				$name=substr($key,0,$pos);
				for($j=$m-1;$j>=0;--$j)
				{
					if($matches[1][$j]==='')
						$value=array($value);
					else
						$value=array($matches[1][$j]=>$value);
				}
				if(isset($_GET[$name]) && is_array($_GET[$name]))
					$value=CMap::mergeArray($_GET[$name],$value);
				$_REQUEST[$name]=$_GET[$name]=$value;
			}
			else
				$_REQUEST[$key]=$_GET[$key]=$value;
		}
	}

	/**
	 * Creates a path info based on the given parameters.
	 * @param array $params list of GET parameters
	 * @param string $equal the separator between name and value
	 * @param string $ampersand the separator between name-value pairs
	 * @param string $key this is used internally.
	 * @return string the created path info
	 */
	public function createPathInfo($params,$equal,$ampersand, $key=null)
	{
		$pairs = array();
		foreach($params as $k => $v)
		{
			if ($key!==null)
				$k = $key.'['.$k.']';

			if (is_array($v))
				$pairs[]=$this->createPathInfo($v,$equal,$ampersand, $k);
			else
				$pairs[]=urlencode($k).$equal.urlencode($v);
		}
		return implode($ampersand,$pairs);
	}

	/**
	 * Removes the URL suffix from path info.
	 * @param string $pathInfo path info part in the URL
	 * @param string $urlSuffix the URL suffix to be removed
	 * @return string path info with URL suffix removed.
	 */
	public function removeUrlSuffix($pathInfo,$urlSuffix)
	{
		if($urlSuffix!=='' && substr($pathInfo,-strlen($urlSuffix))===$urlSuffix)
			return substr($pathInfo,0,-strlen($urlSuffix));
		else
			return $pathInfo;
	}

	/**
	 * Returns the base URL of the application.
	 * @return string the base URL of the application (the part after host name and before query string).
	 * If {@link showScriptName} is true, it will include the script name part.
	 * Otherwise, it will not, and the ending slashes are stripped off.
	 */
	public function getBaseUrl()
	{
		if($this->_baseUrl!==null)
			return $this->_baseUrl;
		else
		{
			if($this->showScriptName)
				$this->_baseUrl=Yii::app()->getRequest()->getScriptUrl();
			else
				$this->_baseUrl=Yii::app()->getRequest()->getBaseUrl();
			return $this->_baseUrl;
		}
	}

	/**
	 * Sets the base URL of the application (the part after host name and before query string).
	 * This method is provided in case the {@link baseUrl} cannot be determined automatically.
	 * The ending slashes should be stripped off. And you are also responsible to remove the script name
	 * if you set {@link showScriptName} to be false.
	 * @param string $value the base URL of the application
	 * @since 1.1.1
	 */
	public function setBaseUrl($value)
	{
		$this->_baseUrl=$value;
	}

	/**
	 * Returns the URL format.
	 * @return string the URL format. Defaults to 'path'. Valid values include 'path' and 'get'.
	 * Please refer to the guide for more details about the difference between these two formats.
	 */
	public function getUrlFormat()
	{
		return $this->_urlFormat;
	}

	/**
	 * Sets the URL format.
	 * @param string $value the URL format. It must be either 'path' or 'get'.
	 */
	public function setUrlFormat($value)
	{
		if($value===self::PATH_FORMAT || $value===self::GET_FORMAT)
			$this->_urlFormat=$value;
		else
			throw new CException(Yii::t('yii','CUrlManager.UrlFormat must be either "path" or "get".'));
	}
}


/**
 * CBaseUrlRule is the base class for a URL rule class.
 *
 * Custom URL rule classes should extend from this class and implement two methods:
 * {@link createUrl} and {@link parseUrl}.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CUrlManager.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.web
 * @since 1.1.8
 */
abstract class CBaseUrlRule extends CComponent
{
	/**
	 * @var boolean whether this rule will also parse the host info part. Defaults to false.
	 */
	public $hasHostInfo=false;
	/**
	 * Creates a URL based on this rule.
	 * @param CUrlManager $manager the manager
	 * @param string $route the route
	 * @param array $params list of parameters (name=>value) associated with the route
	 * @param string $ampersand the token separating name-value pairs in the URL.
	 * @return mixed the constructed URL. False if this rule does not apply.
	 */
	abstract public function createUrl($manager,$route,$params,$ampersand);
	/**
	 * Parses a URL based on this rule.
	 * @param CUrlManager $manager the URL manager
	 * @param CHttpRequest $request the request object
	 * @param string $pathInfo path info part of the URL (URL suffix is already removed based on {@link CUrlManager::urlSuffix})
	 * @param string $rawPathInfo path info that contains the potential URL suffix
	 * @return mixed the route that consists of the controller ID and action ID. False if this rule does not apply.
	 */
	abstract public function parseUrl($manager,$request,$pathInfo,$rawPathInfo);
}

/**
 * CUrlRule represents a URL formatting/parsing rule.
 *
 * It mainly consists of two parts: route and pattern. The former classifies
 * the rule so that it only applies to specific controller-action route.
 * The latter performs the actual formatting and parsing role. The pattern
 * may have a set of named parameters.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CUrlManager.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.web
 * @since 1.0
 */
class CUrlRule extends CBaseUrlRule
{
	/**
	 * @var string the URL suffix used for this rule.
	 * For example, ".html" can be used so that the URL looks like pointing to a static HTML page.
	 * Defaults to null, meaning using the value of {@link CUrlManager::urlSuffix}.
	 */
	public $urlSuffix;
	/**
	 * @var boolean whether the rule is case sensitive. Defaults to null, meaning
	 * using the value of {@link CUrlManager::caseSensitive}.
	 */
	public $caseSensitive;
	/**
	 * @var array the default GET parameters (name=>value) that this rule provides.
	 * When this rule is used to parse the incoming request, the values declared in this property
	 * will be injected into $_GET.
	 */
	public $defaultParams=array();
	/**
	 * @var boolean whether the GET parameter values should match the corresponding
	 * sub-patterns in the rule when creating a URL. Defaults to null, meaning using the value
	 * of {@link CUrlManager::matchValue}. When this property is false, it means
	 * a rule will be used for creating a URL if its route and parameter names match the given ones.
	 * If this property is set true, then the given parameter values must also match the corresponding
	 * parameter sub-patterns. Note that setting this property to true will degrade performance.
	 * @since 1.1.0
	 */
	public $matchValue;
	/**
	 * @var string the HTTP verb (e.g. GET, POST, DELETE) that this rule should match.
	 * If this rule can match multiple verbs, please separate them with commas.
	 * If this property is not set, the rule can match any verb.
	 * Note that this property is only used when parsing a request. It is ignored for URL creation.
	 * @since 1.1.7
	 */
	public $verb;
	/**
	 * @var boolean whether this rule is only used for request parsing.
	 * Defaults to false, meaning the rule is used for both URL parsing and creation.
	 * @since 1.1.7
	 */
	public $parsingOnly=false;
	/**
	 * @var string the controller/action pair
	 */
	public $route;
	/**
	 * @var array the mapping from route param name to token name (e.g. _r1=><1>)
	 */
	public $references=array();
	/**
	 * @var string the pattern used to match route
	 */
	public $routePattern;
	/**
	 * @var string regular expression used to parse a URL
	 */
	public $pattern;
	/**
	 * @var string template used to construct a URL
	 */
	public $template;
	/**
	 * @var array list of parameters (name=>regular expression)
	 */
	public $params=array();
	/**
	 * @var boolean whether the URL allows additional parameters at the end of the path info.
	 */
	public $append;
	/**
	 * @var boolean whether host info should be considered for this rule
	 */
	public $hasHostInfo;

	/**
	 * Constructor.
	 * @param string $route the route of the URL (controller/action)
	 * @param string $pattern the pattern for matching the URL
	 */
	public function __construct($route,$pattern)
	{
		
		//route 定义匹配后路由变量 
		if(is_array($route))
		{
			foreach(array('urlSuffix', 'caseSensitive', 'defaultParams', 'matchValue', 'verb', 'parsingOnly') as $name)
			{
				if(isset($route[$name]))
					$this->$name=$route[$name];
			}
			if(isset($route['pattern']))
				$pattern=$route['pattern'];
			$route=$route[0];
		}
		
		//去掉 后缀 /
		$this->route=trim($route,'/');


		//定义正则表达式转换数组
		$tr2['/']=$tr['/']='\\/';
		
		
		//route 是否定义了引用 <controller>/<action>
		if(strpos($route,'<')!==false && preg_match_all('/<(\w+)>/',$route,$matches2))
		{
			//生成引用数组
			foreach($matches2[1] as $name)
				$this->references[$name]="<$name>";
		}
		
		
		//比转字符串是否相等,这里定义是否为 http或https 
		$this->hasHostInfo=!strncasecmp($pattern,'http://',7) || !strncasecmp($pattern,'https://',8);

		if($this->verb!==null)  //preg_split 取得搜索字符串的成分
			$this->verb=preg_split('/[\s,]+/',strtoupper($this->verb),-1,PREG_SPLIT_NO_EMPTY);

		//匹配url配置信息中的 pattern
		if(preg_match_all('/<(\w+):?(.*?)?>/',$pattern,$matches))
		{	
			//生成数组
			$tokens=array_combine($matches[1],$matches[2]);
			
			//$name 是 :前的字符串 $value 是:后面的正则表达式
			foreach($tokens as $name=>$value)
			{
				
				//如果正则表达式为空，定义不为 \/的所有字符串
				if($value==='')
					$value='[^\/]+';
				$tr["<$name>"]="(?P<$name>$value)"; //定义$tr数组
				if(isset($this->references[$name])) //
					$tr2["<$name>"]=$tr["<$name>"]; //定义 $tr2数组
				else
					$this->params[$name]=$value;  //定义 $this->params
			}
		}
		
		//去掉 *
		$p=rtrim($pattern,'*');
		$this->append = ( $p!==$pattern ); //当后面有 * 点时，就以目录结构追加URL参数
		
		//去掉了 /*
		$p=trim($p,'/');  
		
		//把 $pattern 换成 <controller>
		$this->template=preg_replace('/<(\w+):?.*?>/','<$1>',$p);
		
		//把 pattern 编译成 /^(?p<controller>[^\/]+)(?p<action>[^\/]+))
		$this->pattern='/^'.strtr($this->template,$tr).'\/';
		
		//是否有 * ,有* 就不加$
		if($this->append)
			$this->pattern.='/u';
		else
			$this->pattern.='$/u';
		
		
		//把有引用的<controller>转成<$1>
		if($this->references!==array())
			$this->routePattern='/^'.strtr($this->route,$tr2).'$/u';  //把类似于<controller>/<action> 替换成正则

		if(YII_DEBUG && @preg_match($this->pattern,'test')===false)
			throw new CException(Yii::t('yii','The URL pattern "{pattern}" for route "{route}" is not a valid regular expression.',
				array('{route}'=>$route,'{pattern}'=>$pattern)));
	}

	/**
	 * Creates a URL based on this rule.
	 * @param CUrlManager $manager the manager
	 * @param string $route the route
	 * @param array $params list of parameters
	 * @param string $ampersand the token separating name-value pairs in the URL.
	 * @return mixed the constructed URL or false on error
	 */
	public function createUrl($manager,$route,$params,$ampersand)
	{
		if( $this->parsingOnly )
			return false;

		if($manager->caseSensitive && $this->caseSensitive===null || $this->caseSensitive)
			$case='';
		else
			$case='i';

		$tr=array();
		
		//是否是当前已匹配的route，如果不是，分析是否和当前 rule对象匹配，匹配的话将生成对应的URL
		if($route!==$this->route)
		{
			if($this->routePattern!==null && preg_match($this->routePattern.$case,$route,$matches))
			{
				foreach($this->references as $key=>$name)
					$tr[$name]=$matches[$key];
			}
			else
				return false;
		}


		//对比默认参数和传值参数中有相同键和值的，全部清空
		foreach($this->defaultParams as $key=>$value)
		{
			if(isset($params[$key]))
			{
				if($params[$key]==$value)
					unset($params[$key]);
				else
					return false;
			}
		}

		//清空对象中的params,和参数中的params对比，不存在则清除
		foreach($this->params as $key=>$value)
			if(!isset($params[$key]))
				return false;

		if($manager->matchValue && $this->matchValue===null || $this->matchValue)
		{
			foreach($this->params as $key=>$value)
			{
				if(!preg_match('/'.$value.'/'.$case,$params[$key]))
					return false;
			}
		}
		
		
		//对象中的参数
		foreach($this->params as $key=>$value)
		{
			$tr["<$key>"]=urlencode($params[$key]);
			unset($params[$key]);
		}

		//后缀
		$suffix=$this->urlSuffix===null ? $manager->urlSuffix : $this->urlSuffix;

		//把模板转为/post/list
		$url=strtr($this->template,$tr);
		
		
		//是否有主机信息,对比当前转化的url和主机信息是否相同
		if($this->hasHostInfo)
		{
			$hostInfo=Yii::app()->getRequest()->getHostInfo();
			if(stripos($url,$hostInfo)===0)
				$url=substr($url,strlen($hostInfo));
		}

		//参数是否已转化成功
		if(empty($params))
			return $url!=='' ? $url.$suffix : $url;
		
		//还有参数，将继续转换
		if($this->append)
			$url.='/'.$manager->createPathInfo($params,'/','/').$suffix;
		else
		{
			if($url!=='')
				$url.=$suffix;
			$url.='?'.$manager->createPathInfo($params,'=',$ampersand);
		}

		return $url;
	}

	/**
	 * Parses a URL based on this rule.
	 * @param CUrlManager $manager the URL manager
	 * @param CHttpRequest $request the request object
	 * @param string $pathInfo path info part of the URL
	 * @param string $rawPathInfo path info that contains the potential URL suffix
	 * @return mixed the route that consists of the controller ID and action ID or false on error
	 */
	public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
	{
		
		//$this->verb 定义忽略分析请求的方式
		if($this->verb!==null && !in_array($request->getRequestType(), $this->verb, true))
			return false;
		
		
		//是否区分大小写
		if($manager->caseSensitive && $this->caseSensitive===null || $this->caseSensitive)
			$case='';
		else
			$case='i';

		//后缀去掉
		if($this->urlSuffix!==null)
			$pathInfo=$manager->removeUrlSuffix($rawPathInfo,$this->urlSuffix);
		
		//是否启用严格的URL解析,不匹配后缀，返回false
		// URL suffix required, but not found in the requested URL
		if($manager->useStrictParsing && $pathInfo===$rawPathInfo)
		{
			//有后缀但没有匹配成功，返回false
			$urlSuffix=$this->urlSuffix===null ? $manager->urlSuffix : $this->urlSuffix;
			if($urlSuffix!='' && $urlSuffix!=='/')
				return false;
		}
		
		if($this->hasHostInfo)
			$pathInfo=strtolower($request->getHostInfo()).rtrim('/'.$pathInfo,'/');

		$pathInfo.='/';

		if(preg_match($this->pattern.$case,$pathInfo,$matches))
		{
			foreach($this->defaultParams as $name=>$value)
			{
				if(!isset($_GET[$name]))
					$_REQUEST[$name]=$_GET[$name]=$value;
			}
			$tr=array();
			foreach($matches as $key=>$value)
			{
				if(isset($this->references[$key]))
					$tr[$this->references[$key]]=$value;
				else if(isset($this->params[$key]))
					$_REQUEST[$key]=$_GET[$key]=$value;
			}
			if($pathInfo!==$matches[0]) // there're additional GET params
				$manager->parsePathInfo(ltrim(substr($pathInfo,strlen($matches[0])),'/'));
			if($this->routePattern!==null)
				return strtr($this->route,$tr);
			else
				return $this->route;
		}
		else
			return false;
	}
}
