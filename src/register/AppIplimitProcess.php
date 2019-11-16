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

use EasySwoole\Component\Singleton;
use EasySwoole\Component\TableManager;
use Swoole\Table;
use EasySwoole\EasySwoole\Logger;

/**
 * 自定义ip限制进程
 */
class AppIplimitProcess
{
    /** @var Table */
    protected $table;
    /**
     * [$error 发生错误时提示信息]
     * @var string
     */
    protected $error = 'IP限流的table为空,请管理员检查';
    /**
     * 使用单例模式
     */
	use Singleton;
	/**
	 * [__construct 构造函数]
	 * @Author    como
	 * @DateTime  2019-08-15
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 */
	public function __construct()
	{
        TableManager::getInstance()->add('ipList', [
            'ip' => [
                'type' => Table::TYPE_STRING,
                'size' => 16
            ],
            'count' => [
                'type' => Table::TYPE_INT,
                'size' => 8
            ],
            'lastAccessTime' => [
                'type' => Table::TYPE_INT,
                'size' => 8
            ]
        ], 1024*128);
        $this->table = TableManager::getInstance()->get('ipList');
	}
	/**
	 * [clear 清空限制记录]
	 * @Author    como
	 * @DateTime  2019-08-15
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @return    [type]     [description]
	 */
	public function clear()
	{
		if($this->table){
	        foreach ($this->table as $key => $item){
	            $this->table->del($key);
	        }			
		} else {
			Logger::getInstance()->waring($this->error);//记录waring级别日志并输出到控制台
		}
	}
	/**
	 * [access 进行访问记录]
	 * @Author    como
	 * @DateTime  2019-08-15
	 * @copyright 思智捷管理系统
	 * @version   [1.5.0]
	 * @param     string     $ip [description]
	 * @return    [type]         [description]
	 */
    public function access(string $ip):int
    {
    	if($this->table){
	        $key  = substr(md5($ip), 8,16);
	        $info = $this->table->get($key);
	        if ($info) {
	            $this->table->set($key, [
	                'lastAccessTime' => time(),
	                'count'          => $info['count'] + 1,
	            ]);
	            return $info['count'] + 1;
	        }else{
	            $this->table->set($key, [
	                'ip'             => $ip,
	                'lastAccessTime' => time(),
	                'count'          => $info['count'] + 1,
	            ]);
	            return 1;
	        }    		
    	} else {
    		Logger::getInstance()->waring($this->error);//记录waring级别日志并输出到控制台
    	}
    }
}