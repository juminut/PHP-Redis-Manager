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

    public function setList($key, $val, $direction = 'left', $db = 0)
    {
        $redis = $this->init($key, 'list', $db);
        $res = $redis->set($key, $val, $direction);
        unset($redis);
        return $res;
    }
}

$crudClass = new crudClass();
$crudClass->setList('temps', 1);