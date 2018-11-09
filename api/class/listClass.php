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
        $redis = new Redis();
        $redis->connect('127.0.0.1');
        $this->redis = $redis;
    }

    //分页读取
    public function getPage($key, $direction, $offset, $limit)
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

        //处理value值
        if (!($data = $this->transformation($val)))
            throw new Exception('value 类型错误');

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

    //将一个值插入到已存在的列表头部 不存在返回空
    public function pushX($key, $val, $direction)
    {
        if (!in_array($direction, ['left', 'right']))
            throw new Exception('direction 参数错误');

        //处理value值
        if (!($data = $this->transformation($val)))
            throw new Exception('value 类型错误');

        $successNum = 0;//成功数量
        foreach ($data as $value) {
            if ($direction == 'left') {
                if ($this->redis->lPushx($key, $value))
                    $successNum += 1;
            } else {
                if ($this->redis->rPushx($key, $value))
                    $successNum += 1;
            }
        }
        return $successNum;
    }

    //阻塞移出并获取列表的第一个元素
    public function bPop($key, $direction = 'left', $timeOut = 0)
    {
        if ($this->arrayDepth($key) > 1)
            throw new Exception('key 类型错误');

        if (!in_array($direction, ['left', 'right']))
            throw new Exception('direction 参数错误');

        if ($direction == 'left')
            $res = $this->redis->blPop($key, $timeOut);
        else
            $res = $this->redis->brPop($key, $timeOut);
        if ($res)
            return $res[1];
        else
            return false;
    }

    //移除和获取列表的第一个元素 并插入第二个队列
    public function poppush($key, $dstKey, $PopDirection = 'right', $PushDirection = 'left', $timeOut = 0)
    {
        if (!in_array($PopDirection, ['left', 'right']))
            throw new Exception('direction 参数错误');

        if ($PopDirection == 'right' && $PushDirection == 'left') {//右边取左边插入
            $res = $this->redis->rpoplpush($key, $dstKey, $timeOut);
        } elseif ($PopDirection == 'right' && $PushDirection == 'right') {//右边取右边插入
            $val = @$this->redis->rPop($key, $timeOut)[1];
            if ($val && $this->redis->rPush($dstKey, $val)) {
                $res = $val;
            } else {
                if (!$this->redis->rPush($key, $val))
                    throw new Exception('执行恢复失败');
                $res = false;
            }
        } elseif ($PopDirection == 'left' && $PushDirection == 'left') {//左边取左边插入
            $val = @$this->redis->lPop($key, $timeOut)[1];
            if ($val && $this->redis->lPush($dstKey, $val)) {
                $res = $val;
            } else {
                if (!$this->redis->lPush($key, $val))
                    throw new Exception('执行恢复失败');
                $res = false;
            }
        } elseif ($PopDirection == 'left' && $PushDirection == 'left') {//左边取右边插入
            $val = @$this->redis->lPop($key, $timeOut)[1];
            if ($val && $this->redis->rPush($dstKey, $val)) {
                $res = $val;
            } else {
                if (!$this->redis->lPush($key, $val))
                    throw new Exception('执行恢复失败');
                $res = false;
            }
        }
        return $res;
    }

    //阻塞移除和获取列表的第一个元素 并插入第二个队列
    public function bpoppush($key, $dstKey, $PopDirection = 'right', $PushDirection = 'left', $timeOut = 0)
    {
        if (!in_array($PopDirection, ['left', 'right']))
            throw new Exception('direction 参数错误');

        if ($PopDirection == 'right' && $PushDirection == 'left') {//右边取左边插入
            $res = $this->redis->brpoplpush($key, $dstKey, $timeOut);
        } elseif ($PopDirection == 'right' && $PushDirection == 'right') {//右边取右边插入
            //考虑是否上锁
            $val = @$this->redis->brPop($key, $timeOut)[1];
            if ($val && $this->redis->rPush($dstKey, $val)) {
                $res = $val;
            } else {
                if (!$this->redis->rPush($key, $val))
                    throw new Exception('执行恢复失败');
                $res = false;
            }
        } elseif ($PopDirection == 'left' && $PushDirection == 'left') {//左边取左边插入
            //考虑是否上锁
            $val = @$this->redis->blPop($key, $timeOut)[1];
            if ($val && $this->redis->lPush($dstKey, $val)) {
                $res = $val;
            } else {
                if (!$this->redis->lPush($key, $val))
                    throw new Exception('执行恢复失败');
                $res = false;
            }
        } elseif ($PopDirection == 'left' && $PushDirection == 'left') {//左边取右边插入
            //考虑是否上锁
            $val = @$this->redis->blPop($key, $timeOut)[1];
            if ($val && $this->redis->rPush($dstKey, $val)) {
                $res = $val;
            } else {
                if (!$this->redis->lPush($key, $val))
                    throw new Exception('执行恢复失败');
                $res = false;
            }
        }
        return $res;
    }

    //读取队列
    public function get($key, $direction, $start, $end)
    {
        if (!in_array($direction, ['left', 'right']))
            throw new Exception('direction 参数错误');

        //范围数值限制
        $len = $this->len($key);
        if ($start < 0)
            throw new Exception('start 参数错误');

        $end = $end1 = abs($end) > abs($len) ? ($end > 0 ? $len : -$len) : $end;

        if ($direction == 'right') { //redis 没有从右往左所以更改参数
            $end = $start == 0 ? -1 : $len - $start - 1;
            if ($end1 < 0)
                $start = abs($end1) - 1;
            else
                $start = $len - $end1 - 1;
        }

        $res = $this->redis->lRange($key, $start, $end);
        return $res;

    }

    //对列表进行修剪
    public function trim($key, $direction, $start, $end)
    {
        if (!in_array($direction, ['left', 'right']))
            throw new Exception('direction 参数错误');

        //范围数值限制
        $len = $this->len($key);
        if ($start < 0)
            throw new Exception('start 参数错误');

        $end = $end1 = abs($end) > abs($len) ? ($end > 0 ? $len : -$len) : $end;
        if ($direction == 'right') { //redis 没有从右往左所以更改参数
            $end = $start == 0 ? -1 : $len - $start - 1;
            if ($end1 < 0)
                $start = abs($end1) - 1;
            else
                $start = $len - $end1 - 1;
        }

        $res = $this->redis->lTrim($key, $start, $end);
        return $res;

    }

    public function __call($name, $arguments)
    {

    }
}