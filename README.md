# 思智捷科技ODAPHP框架核心库

odaphp 是一款基于easyswoole的快速开发的php框架，在原有的easyswoole的基础上增强了定时器、任务投递等功能，简化了配置操作、缓存操作、路由操作、以及reqeust、response等操作的复杂性和安全性，完全兼容easyswoole内的所有方法  具体可以查看easyswoole官方文档


## 核心库扩展说明


### Controller类扩展

- 命名空间 

```php
szjcomo\szjcore\Controller
```
- 继承自

```php
EasySwoole\Http\AbstractInterface\Controller
```
- 扩展说明

|  类型 | 方法名称   | 参数说明  |  方法说明 |
| ------------ | ------------ | ------------ | ------------ |
| public  | index()  | 无  | easyswoole 规定继承自控制器必须实现index方法  |
| public  | sessions($key,$value)  | key,value  | 获取session和设置session的值  |
| public  | appResult($info,$data,$err)  | info,data,err  | 统一app返回值,info 返回说明 data的数据 err是否正确  |
| public  | appJson($data,$code)  |data,code  | 响应JSON数据,code默认是200  |
| public  | initialize()  |无 | 全局拦截事件，如做权限控制请在子类中自行完成，返回false不继续执行后面的程序 返回true继续执行  |
| public  | onRequest()  |无 | 重写了easyswoole的onRequest()事件，增强了context功能，不建议子类重写，子类可以实现initialize()事件即可  |
| public  | onException()  |无 | 重写了easyswoole的onException事件，如果控制器出错,统一返回json格式错误信息  |
| public  | _emtpy($action)  |action | 未请求到方法空操作设置,子类可根据业务需要自定义实现  |


- 更多文档正在更新中...