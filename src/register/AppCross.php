<?php
/**
 * |-----------------------------------------------------------------------------------
 * @Copyright (c) 2014-2018, http://www.sizhijie.com. All Rights Reserved.
 * @Website: www.sizhijie.com
 * @Version: 思智捷信息科技有限公司
 * @Author : szjcomo 
 * |-----------------------------------------------------------------------------------
 */

namespace szjcomo\szjcore\register;

use EasySwoole\Http\Response;
use EasySwoole\EasySwoole\Config;

/**
 * 跨载请球注册
 */
class AppCross
{
	/**
	 * [register 跨域请求检查注册]
	 * @author 	   szjcomo
	 * @createTime 2019-11-05
	 * @return     [type]     [description]
	 */
	public static function register(Response $response,$callback = null)
	{
		$startCross = Config::getInstance()->getConf('APP_CROSS_DOMAIN');
		if($startCross === true){
			if(!empty($callback)){
				call_user_func($callback,$response);
			} else {
	            $response->withHeader('Access-Control-Allow-Origin', '*');
	            $response->withHeader('Access-Control-Allow-Methods', 'GET, POST');
	            $response->withHeader('Access-Control-Allow-Credentials', 'true');
	            $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');   
			}
		}
		return true;
	}
}