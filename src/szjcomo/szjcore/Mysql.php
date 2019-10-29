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

//使用mysql链接池进行mysql操作
use szjcomo\mysqliPool\Mysql as szjcomoMysql;
/**
 * 再次封装mysql查询功能
 */
class Mysql 
{
	/**
	 * [name 获取表名]
	 * @Author    como
	 * @DateTime  2019-08-14
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     string     $tableName [description]
	 * @param     string     $prefix    [description]
	 * @return    [type]                [description]
	 */
	public static function DB(string $name = 'mysql0')
	{
		return szjcomoMysql::defer($name);
	}
}