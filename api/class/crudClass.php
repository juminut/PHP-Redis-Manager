<?php
/**
 * Created by PhpStorm.
 * User: zhangtianyi
 * Date: 18-11-6
 * Time: 下午3:32
 */
include_once 'base.php';

class crudClass extends base
{

    public function setString($key, $val, $db = 0)
    {
        $redis = $this->init($key, 'string', $db);
        $res = $redis->set($key, $val);
        unset($redis);
        return $res;
    }

    public function pushList($key, $val, $direction = 'left', $db = 0)
    {
        $redis = $this->init($key, 'list', $db);
        $res = $redis->push($key, $val, $direction);
        unset($redis);
        return $res;
    }

    /*$key 键名
    $direction 方向
    $limti 一次读取多少条
    $offset 开始位置
    注: $limit = 10 $offset = 0 时 读取的是0到第10条  offset = 2 时 读取的是第11到第20条*/
    public function getList($key, $direction, $offset, $limti, $db = 0)
    {
        $redis = $this->init($key, 'list', $db);
        $res = $redis->get($key, $direction, $offset, $limti);
        unset($redis);
        return $res;
    }
}

$crudClass = new crudClass();
$crudClass->pushList('temps', null,'left');
//$crudClass->getList('temps', 'left', 2,10);
//$crudClass->redis->set('set','temps', '123');
