<?php
/**
 * Created by PhpStorm.
 * User: zhangtianyi
 * Date: 18-11-6
 * Time: 下午2:52
 */

class listClass
{
    private $redis;

    //初始化
    public function __construct(object $redis)
    {
    // $redis = new Redis();
    // $redis ->connect('127.0.0.1');
        $this->redis = $redis;
    }

    //获列表
    // public function getList(string $key, $limit, $offset)
    public function get()
    {
        var_dump($this->redis);
    }

    public function set($key, $val, $direction)
    {
        if (in_array($direction,['left','right']))
            throw new Exception('direction 参数错误');

        switch ($this->arrayDepth($val)) {
            case 1:
                $this->redis->lpush();
                break;
            case 2:
                $this->redis->rpush();
                break;
            default:
                throw new Exception('value 类型错误');
        }
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        throw new Exception('方法不存在');
    }
}