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


    /*
     * 把元素压入队列
     * @param string $key 键名
     * @param string array $val 值
     * @param string $direction 操作方向
     * @param int $db 仓库编号 */
    public function listPush($key, $val, $direction = 'left', $db = 0)
    {
        $redis = $this->init($key, 'list', $db);
        $res = $redis->push($key, $val, $direction);
        unset($redis);
        return $res;
    }

    /*
    * 将一个值插入到已存在的列表。如果列表不存在，操作无效
    * @param string $key 键名
    * @param string array $val 值
    * @param string $direction 操作方向
    * @param int $db 仓库编号 */
    public function listPushX($key, $val, $direction = 'left', $db = 0)
    {
        $redis = $this->init($key, 'list', $db);
        $res = $redis->pushX($key, $val, $direction);
        unset($redis);
        return $res;
    }

    /*
     * 读取队列元素 注:不是移除
     * @param string $key 键名
     * @param string $direction 方向
     * @param int $start 开始位置
     * @param int $end 结束位置 */
    public function listGet($key, $direction, $start, $end, $db = 0)
    {
        $redis = $this->init($key, 'list', $db);
        $res = $redis->get($key, $direction, $start, $end);
        unset($redis);
        return $res;
    }

    /*
     * 分页读取队列元素 注:不是移除
     * @param string $key 键名
     * @param string $direction 方向
     * @param int $limti 一次读取多少条
     * @param int $offset 开始位置
     * 注: $limit = 10 $offset = 0 时 读取的是0到第10条  offset = 2 时 读取的是第11到第20条*/
    public function listGetPage($key, $direction, $offset, $limti, $db = 0)
    {
        $redis = $this->init($key, 'list', $db);
        $res = $redis->getPage($key, $direction, $offset, $limti);
        unset($redis);
        return $res;
    }


    /*
     * 阻塞移除一个元素
     * @param array string $key 键名
     * @param string $drection 移除操作方向
     * @param int $timeOut 超时时间
     * @param int $db 仓库编号 */
    public function listBPop($key, $direction = 'left', $timeOut = 0, $db = 0)
    {
        $redis = $this->init($key, 'list', $db);
        $res = $redis->bPop($key, $direction, $timeOut);
        unset($redis);
        return $res;
    }

    /*
     * 移除一个元素并压入另一个队列
     * @param string $key 键名
     * @param string $dstKey 第二个队列名称
     * @param string $PopDirection 移除操作方向
     * @param string $PushDirection 压入操作方向
     * @param int $timeOut 超时时间
     * @param int $db 仓库编号 */
    public function listPopPush($key, $dstKey, $PopDirection = 'right', $PushDirection = 'left', $timeOut = 0, $db = 0)
    {
        $redis = $this->init($key, 'list', $db);
        $res = $redis->poppush($key, $dstKey, $PopDirection, $PushDirection, $timeOut);
        unset($redis);
        return $res;
    }

    /*
     * 阻塞移除一个元素并压入另一个队列
     * @param string $key 键名
     * @param string $dstKey 第二个队列名称
     * @param string $PopDirection 移除操作方向
     * @param string $PushDirection 压入操作方向
     * @param int $timeOut 超时时间
     * @param int $db 仓库编号 */
    public function listBPopPush($key, $dstKey, $PopDirection = 'right', $PushDirection = 'left', $timeOut = 0, $db = 0)
    {
        $redis = $this->init($key, 'list', $db);
        $res = $redis->bpoppush($key, $dstKey, $PopDirection, $PushDirection, $timeOut);
        unset($redis);
        return $res;
    }


    /*
     * 对一个列表进行修剪，就是说，让列表只保留指定区间内的元素，不在指定区间之内的元素都将被删除。
     * @param string $key 键名
     * @param string $direction 方向
     * @param int $start 一次读取多少条
     * @param int $end 开始位置
     * @param int $db 仓库编号
     * 注: $limit = 10 $offset = 0 时 读取的是0到第10条  offset = 2 时 读取的是第11到第20条*/
    public function listTrim($key, $direction, $offset, $limti, $db = 0)
    {
        $redis = $this->init($key, 'list', $db);
        $res = $redis->trim($key, $direction, $offset, $limti);
        unset($redis);
        return $res;
    }


    /*
     * 删除一个或多个哈希表字段
     * @param string $key 键名
     * @param string|array $field 哈希表字段
     * @param int $db 仓库编号
     * @return int 成功数量*/
    public function hashDel($key, $field, $db = 0)
    {
        $redis = $this->init($key, 'hash', $db);
        $res = $redis->del($key, $field);
        unset($redis);
        return $res;
    }

    /*
     * 验证哈希表的指定字段是否存在
     * @param string $key 键名
     * @param string|array $field 哈希表字段
     * @param int $db 仓库编号
     * @return array|bool  多个哈希字段时返回array key对应哈希字段 value对应结果
     *                     单个哈希字段直接返回bool*/
    public function hashExists($key, $field, $db = 0)
    {
        $redis = $this->init($key, 'hash', $db);
        $res = $redis->exists($key, $field);
        unset($redis);
        return $res;
    }

    /*
     * 将哈希表 key 中的字段 field 的值设为 value 。
     * @param string $key 键名
     * @param array $val 键值对
     * @param int $db 仓库编号
     * @return result  */
    public function hashSet($key, $field, $db = 0)
    {
        $redis = $this->init($key, 'hash', $db);
        $res = $redis->set($key, $field);
        unset($redis);
        return $res;
    }

    /*
     * 将哈希表 key 中 不存在!!! 的字段 field 的值设为 value 。
     * @param string $key 键名
     * @param array $val 键值对
     * @param int $db 仓库编号
     * @return array|bool  多个哈希字段时返回array key对应哈希字段 value对应结果
     *                     单个哈希字段直接返回bool*/
    public function hashSetNx($key, $field, $db = 0)
    {
        $redis = $this->init($key, 'hash', $db);
        $res = $redis->setNx($key, $field);
        unset($redis);
        return $res;
    }


    /*
     * 获取存储在哈希表中指定字段的值
     * @param string $key 键名
     * @param string|array $field 哈希表字段
     * @param int $db 仓库编号
     * @return result  多个哈希字段时返回array key对应哈希字段 value对应结果
     *                     单个哈希字段直接返回结果*/
    public function hashGet($key, $field, $db = 0)
    {
        $redis = $this->init($key, 'hash', $db);
        $res = $redis->get($key, $field);
        unset($redis);
        return $res;
    }

    /*
     * 获取存储在哈希表中所有字段的值
     * @param string|array $key 键名
     * @param int $db 仓库编号
     * @return result  多个rediskey时返回array key对应rediskey value对应结果
     *                     单个rediskey直接返回结果*/
    public function hashGetAll($key, $db = 0)
    {
        $redis = $this->init($key, 'hash', $db);
        $res = $redis->getAll($key);
        unset($redis);
        return $res;
    }

    /*
     * 为哈希表 key 中的指定字段的数值加上增量
     * @param string|array $key 键名
     * @param string|array $field 哈希表字段
     * @param int $increment 增量
     * @param int $db 仓库编号
     * @return result  多个rediskey时返回array key对应rediskey value对应结果
     *                     单个rediskey直接返回结果*/
    public function hashIncrement($key, $field, $increment, $db = 0)
    {
        $redis = $this->init($key, 'hash', $db);
        $res = $redis->increment($key, $field, $increment);
        unset($redis);
        return $res;
    }

    /*
   * 选择仓库
   * @param string $index 仓库编号*/
    public function select($index)
    {
        if ($index > 0 && $index <= $this->redis->config('get', 'databases')['databases'])
            return $this->redis->select($index);
        throw new Exception('仓库编号错误');
    }

    /*
     * 执行不存在的方法 多用于redis系统方法 */
    public function __call($name, $arguments)
    {
        if (!method_exists($this->redis, $name))
            throw new Exception('redis没有这个方法');

        return call_user_func_array([$this->redis, $name], $arguments);

    }
}

