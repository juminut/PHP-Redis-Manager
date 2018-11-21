<?php
/**
 * Created by PhpStorm.
 * User: zhangtianyi
 * Date: 18-11-9
 * Time: 上午11:34
 */

class hashClass extends base
{
    //初始化 阻止父类执行
    public function __construct(object $redis)
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1');
        $this->redis = $redis;
    }

    //删除指定字段
    public function del($key, $field)
    {
        if ($this->arrayDepth($field) > 1)
            throw new Exception('field 类型错误 超过一维数组');
        if (is_string($field) || is_numeric($field))
            $field = [$field];

        $successNum = 0;//成功数量
        foreach ($field as $val) {
            if ($this->redis->hdel($key, $val))
                $successNum += 1;
        }
        return $successNum;
    }

    //验证哈希表的指定字段是否存在
    public function exists($key, $field)
    {

        if (is_string($field) || is_numeric($field))
            $field = [$field];

        if ($this->arrayDepth($field) > 1)
            throw new Exception('field 类型错误 不是一维数组');


        foreach ($field as $val) {
            if ($this->redis->hExists($key, $val))
                $res[$val] = true;
            else
                $res[$val] = false;
        }

        //如果只有一个字段
        if (count($res) == 1) {
            //暂定这么取值
            $res = array_values($res)[0];
        }
        return $res;
    }

    //获取指定字段
    public function get($key, $field)
    {
        if ($this->arrayDepth($field) > 1)
            throw new Exception('field 类型错误 不是一维数组');
        if (is_string($field) || is_numeric($field))
            $field = [$field];

        //Hmget这个也可以
        foreach ($field as $val) {
            if (($val = $this->redis->hGet($key, $val)))
                $res[$val] = $val;
            else
                $res[$val] = null;
        }

        //如果只有一个字段
        if (count($res) == 1) {
            //暂定这么取值
            $res = array_values($res)[0];
        }
        return $res;

    }

    //获取全部字段值
    public function getAll($key)
    {
        if ($this->arrayDepth($key) > 1)
            throw new Exception('field 类型错误 不是一维数组');
        if (is_string($key) || is_numeric($key))
            $key = [$key];

        foreach ($key as $value) {
            if (($val = $this->redis->hGetAll($value)))
                foreach ($val as $enval)
                    $res[$value][] = $this->decode($enval);
            else
                $res[$value] = [];
        }
        //如果只有一个字段
        if (count($res) == 1) {
            //暂定这么取值
            $res = array_values($res)[0];
        }
        return $res;
    }

    //设置
    public function set($key, array $val)
    {
        //if ($this->arrayDepth($val) > 1)
        //throw new Exception('field 类型错误 不是一维数组');
        //转码
        foreach ($val as &$value) {
            $value = $this->encode($value);
        }

        $res = $this->redis->hMSet($key, $val);
        return $res;
    }

    //设置
    public function setNx($key, array $val)
    {
        if ($this->arrayDepth($val) > 1)
            throw new Exception('field 类型错误 不是一维数组');

        foreach ($val as $k => $value) {
            //转码
            $value = $this->encode($value);
            $res[$k] = $this->redis->hSetNx($key, $k, $value);
        }

        if (count($res) == 1) {
            //暂定这么取值
            $res = array_values($res)[0];
        }
        return $res;
    }

    public function increment($key, $field, $increment)
    {
        if (!is_numeric($increment))
            throw new Exception('value 类型错误 不是一个数值');
        if (is_string($field) || is_numeric($field))
            $field = [$field];
        if ($this->arrayDepth($field) > 1)
            throw new Exception('field 类型错误 不是一维数组');
        foreach ($field as $value) {
            $redValue = $this->redis->hGet($key, $value);
            if (!is_numeric($redValue)) {
                $res[$value] = false;
                continue;
            }

            //if (strstr($increment,'.'))
            $res[$value] = $this->redis->hIncrByFloat($key, $value, $increment);
            //else
            //$res[$value] = $this->redis->hIncrBy($key,$value,$increment);
        }
        if (count($res) == 1) {
            //暂定这么取值
            $res = array_values($res)[0];
        }
        return $res;
    }
}