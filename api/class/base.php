<?php

class base
{
    //对外暴露redis对象
    public $redis;

    //构造
    public function __construct()
    {
        $redis_connect_list = [];
        include_once __DIR__ . '/../config.php';
        if (!$redis_connect_list || !$redis_connect_list['host']) throw new Exception('缺少参数 请检查');
        $this->redis = new Redis();
        $this->redis->connect($redis_connect_list['host'], ($redis_connect_list['port'] ? $redis_connect_list['port'] : 6379));
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

    //获取长度
    public function len($key)
    {
        switch ($this->redis->type($key)) {
            case 2: //set
                $len = $this->redis->sCard($key);
                break;
            case 3: //list
                $len = $this->redis->lLen($key);
                break;
            case 4: //zset
                $len = $this->redis->zCard($key);
                break;
            case 5: //hash
                $len = $this->redis->hLen($key);
                break;
            default:
                throw new Exception('key 类型错误');
                break;
        }
        return $len;
    }

    //获取类型
    public function getType($key)
    {
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
    public function arrayDepth($array)
    {
        if (!is_array($array)) return 0;
        $max_depth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = $this->arrayDepth($value) + 1;
                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }
        return $max_depth;
    }


    //转换一维数组
    public function transformation($val)
    {
        //判断数组维度 并处理
        switch ($this->arrayDepth($val)) {
            case 0: //字符串或空
                $data[] = $val;
                break;
            case 1:
                $data = $val;
                break;
            case 2: //二维 并转一维
                //array_column 可以替代下面foreach
                foreach ($val as $values)
                    foreach ($values as $value)
                        $data[] = $value;
                break;
            case 3: //三维
                break;
            default://多维
                $data = false;
        }
        return $data;
    }


    /*public function systemFunction($name, $key, $values, $db = 0)
    {

        if (!method_exists($this->redis, $name))
            throw new Exception('redis没有这个方法');

        //选择数据库
        if ($db > 0 && $db <= $this->redis->config('get', 'databases')['databases'])
            $this->redis->select($db);


        if ($this->redis->exists($key)) {
            $type = $this->getType($key);
            if (gettype($values) == 'string') {
                if (!in_array($type, [])){

                }
                    #code....
            } else {
                throw new Exception('类型错误');
            }
        }

        echo $this->redis->$name($key, $values);

    }*/
}

//$obj = new base();
//$redis = $obj->key('temps');
//$redis->getList();


