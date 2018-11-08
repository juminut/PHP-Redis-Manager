<?php
/**
 * 
 */

class stringClass extends base
{

    //初始化 阻止父类执行
    public function __construct(object $redis)
    {
//        $redis = new Redis();
//        $redis->connect('127.0.0.1');
        $this->redis = $redis;
    }

    //读取 get value
    public function get($key)
    {
        $res = $this->redis->get($key);
        return $res;
    }


    //压入 save
	/*
	$milliseconds with a ttl of this value milliseconds.
	$insert on the value max of the 0, Will set a key, if it doesn't exist
			on the value min of the 0, Will set the key, if it does exist
			on the value is 0, Will set the key ,anyway it exits or not
	*/
    public function set($key, $val, $milliseconds=-1, $insert=0)
    {
		$optional=array();
		if($milliseconds>0){
			$optional=array("PX"=>$milliseconds);
		}
		if($insert!=0){
			if($insert>0){
				$optional[]="NX";
			}else{
				$optional[]="XX";
			}
		}
		if ($this->redis->set($key, $val,$optional))
			return 1;
    }

    public function __call($name, $arguments)
    {

    }
}