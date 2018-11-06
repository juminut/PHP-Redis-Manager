<?php
//保存用户登录信息
function set_session($session_id,$array){
	if (!file_exists("session.php")) { //检查文件是否存在
		$array=array(array("session_id"=>$session_id,"time"=>time(),"array"=>$array));
		write($array);
	}else{
		if(get_session($session_id)===false){
			$arrays=include "session.php";
			$arrays=json_decode($arrays,true);
			foreach($arrays as $key=>$arr){
				if($arrays[$key]['time']<time()-1800){
					unset($arrays[$key]);
				}
			}
			$arrays[]=array("session_id"=>$session_id,"time"=>time(),"array"=>$array);
			write($arrays);
		}
	}
}
//判断用户是否登录，及保存的信息
function get_session($session_id){
	if (file_exists("session.php")) { //检查文件是否存在
		$arrays=include "session.php";
		$arrays=json_decode($arrays,true);
		foreach($arrays as $key=>$arr){
			if($arr['session_id']==$session_id){
				$arrays[$key]['time']=time();
				write($arrays);
				return $arr['array'];
				break;
			}
		}
	}
	return false;
}

function write($arrays){
	$txt="<?php\r\nreturn '".json_encode($arrays,JSON_UNESCAPED_UNICODE)."'; ?>";
	file_put_contents("session.php",$txt);
}
?>