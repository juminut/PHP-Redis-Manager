<?php

class base
{
    private $redis;

    //构造
    public function __construct()
    {
        $redis_connect_list = [];
        include_once '../config.php';
        if (!$redis_connect_list || !$redis_connect_list[0]['host']) throw new Exception('缺少参数 请检查');
        $this->redis = new Redis();
        $this->redis->connect($redis_connect_list[0]['host'], ($redis_connect_list[0]['port'] ? $redis_connect_list[0]['port'] : 6379));
    }

    //初始化
    public function init($key, $type, $db = 0)
    {
        //选择数据库
        if ($db > 0 && $db <= $this->redis->config('get', 'databases')['databases'])
            $this->redis->select($db);

        //如果key存在 则验证类型
        if ($this->redis->exists($key) && !$this->verification($key, $type))
            throw new Exception('类型错误');

        //引入相对应的文件
        if (!is_file($type . 'Class.php'))
            throw new Exception('没有这个类');

        include_once $type . 'Class.php';

        $clasName = $type . 'Class';
        if (($redis = new $clasName($this->redis)))
            return $redis;
        else
            return false;
    }

    //验证类型
    public function verification($key, $type)
    {
        if ($this->getType($key) == $type)
            return true;
        else
            return false;

    }

    //获取类型
    public function getType($key){
        switch ($this->redis->type($key)) {
            case 0:
                throw new Exception('key不存在');
                break;
            case 1:
                $keyType = 'string';
                break;
            case 2:
                $keyType = 'set';
                break;
            case 3:
                $keyType = 'list';
                break;
            case 4:
                $keyType = 'zset';
                break;
            case 5:
                $keyType = 'hash';
                break;
        }
        return $keyType;
    }

    //获取数组维度
    public function arrayDepth($array) {
        if(!is_array($array)) return 0;
        $max_depth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = arrayDepth($value) + 1;

                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }
        return $max_depth;
    }
}

//$obj = new base();
//$redis = $obj->key('temps');
//$redis->getList();
?>


