<?php
/**
 * |-----------------------------------------------------------------------------------
 * @Copyright (c) 2014-2018, http://www.sizhijie.com. All Rights Reserved.
 * @Website: www.sizhijie.com
 * @Version: 思智捷信息科技有限公司
 * @Author : szjcomo 
 * |-----------------------------------------------------------------------------------
 */

namespace szjcomo\szjcore;

use think\Template 								as ThinkTemplate;
use EasySwoole\EasySwoole\Config  				as EasySwooleConfig;

/**
 * 视图渲染
 */
class Views extends Controller
{
	/**
	 * [$viewsObj 视图引擎对象]
	 * @var null
	 */
	protected $viewsObj = null;

	/**
	 * [__construct 构造函数]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 */
	public function __construct()
	{
		$config 		= EasySwooleConfig::getInstance()->getConf('APP_TEMPLATE_CONFIG');
		$this->viewsObj = new ThinkTemplate($config);
		parent::__construct();
	}
	/**
	 * [fetch 获取模版]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      [type]     $template [description]
	 * @param      array      $vars     [description]
	 * @param      array      $config   [description]
	 * @return     [type]               [description]
	 */
	public function fetch($template = null, $vars = [], $config = [])
	{
        ob_start();
        $this->viewsObj->fetch($template, $vars, $config);
        $content = ob_get_clean();
        return $this->response()->write($content);
	}
	/**
	 * [assign 设置数据]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      string     $name  [description]
	 * @param      [type]     $value [description]
	 * @return     [type]            [description]
	 */
	public function assign($name,$value = null)
	{
		if(empty($name)) return $this; 
		if(is_array($name) && empty($value)) $this->viewsObj->assign($name);
		if(is_string($name)) $this->viewsObj->assign([$name=>$value]);
		return $this;
	}
	/**
	 * [getViewsObj 获取视图对象]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @return     [type]     [description]
	 */
	public function getViewsObj()
	{
		return $this->viewsObj;
	}

}