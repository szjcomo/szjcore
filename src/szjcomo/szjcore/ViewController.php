<?php
/**
 * |-----------------------------------------------------------------------------------
 * @Copyright (c) 2014-2018, http://www.sizhijie.com. All Rights Reserved.
 * @Website: www.sizhijie.com
 * @Version: 思智捷管理系统 1.5.0
 * @Author : como 
 * 版权申明：szjshop网上管理系统不是一个自由软件，是思智捷科技官方推出的商业源码，严禁在未经许可的情况下
 * 拷贝、复制、传播、使用szjshop网店管理系统的任意代码，如有违反，请立即删除，否则您将面临承担相应
 * 法律责任的风险。如果需要取得官方授权，请联系官方http://www.sizhijie.com
 * |-----------------------------------------------------------------------------------
 */
namespace szjcomo\szjcore;

use szjcomo\szjcore\Controller;
use EasySwoole\EasySwoole\Config;
use think\Template;

/**
 * 继承自Controller控制器
 */
class ViewController extends Controller
{
	/**
	 * 视频模版
	 */
	protected $view;
	/**
	 * [__construct 调用模版引擎]
	 * @Author    como
	 * @DateTime  2019-08-22
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 */
	public function __construct()
	{
		$this->init();
		parent::__construct();
	}
	/**
	 * [init 初始化功能]
	 * @Author    como
	 * @DateTime  2019-08-22
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    [type]     [description]
	 */
    public function init()
    {
        $this->view             = new Template();
        $tempPath               = Config::getInstance()->getConf('TEMP_DIR');     # 临时文件目录
        $this->view->config(['view_path' => EASYSWOOLE_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR,'cache_path' => "{$tempPath}/cache/"]);
    }

    /**
     * 输出模板到页面
     * @param  string|null $template 模板文件
     * @param array        $vars 模板变量值
     * @param array        $config 额外的渲染配置
     * @author : evalor <master@evalor.cn>
     */
    public function fetch($template = null, $vars = [], $config = [])
    {
        ob_start();
        $this->view->fetch($template, $vars, $config);
        $content = ob_get_clean();
        $this->response()->write($content);
    }
}