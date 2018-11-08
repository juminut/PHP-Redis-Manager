<?php
/**
 * Created by PhpStorm.
 * User: zhangtianyi
 * Date: 18-11-6
 * Time: 下午2:52
 */

class listClass extends base
{

    //初始化 阻止父类执行
    public function __construct(object $redis)
    {
//        $redis = new Redis();
//        $redis->connect('127.0.0.1');
        $this->redis = $redis;
    }

    //读取
    public function get($key, $direction, $offset, $limit)
    {
        if (!in_array($direction, ['left', 'right']))
            throw new Exception('direction 参数错误');
        $len = $this->len($key);
        if ($offset > ceil($len / $limit) - 1)
            return [];

        $start = $offset * $limit;
        $end = $offset + $limit - 1;

        if ($direction == 'right') { //redis 没有从右往左所以更改参数
            $start = $len - ($offset + 1) * $limit;
            $start = $start >= 0 ? $start : 0;
            $end = $offset > 0 ? -$limit * $offset - 1 : -1;
        }

        $res = $this->redis->lRange($key, $start, $end);
        if ($direction == 'right') //反转
            $res = array_reverse($res);
        return $res;
    }


    //压入
    public function push($key, $val, $direction)
    {
        if (!in_array($direction, ['left', 'right']))
            throw new Exception('direction 参数错误');

        //判断数组维度 并处理
        switch ($this->arrayDepth($val)) {
            case 1: //一维
                //if (!count($val))
                //    throw new Exception('value 为空');
                break;
            case 2: //二维 并转一维
                //array_column 可以替代下面foreach
                foreach ($val as $values)
                    foreach ($values as $value)
                        $data[] = $value;
                break;
            case 0: //字符串或空
                $data[] = $val;
                break;
            default://多维
                throw new Exception('value 类型错误');
        }

        $successNum = 0;//成功数量
        foreach ($data as $value) {
            if ($direction == 'left') {
                if ($this->redis->lpush($key, $value))
                    $successNum += 1;
            } else {
                if ($this->redis->rpush($key, $value))
                    $successNum += 1;
            }
        }
        return $successNum;
    }

    public function __call($name, $arguments)
    {

    }
}