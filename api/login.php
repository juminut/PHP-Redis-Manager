<?php
/*
登录
login

username and password from config.php
*/
include 'config.php';
include 'base.php';
$username=$_POST['username'];
$password=$_POST['password'];

$login_success=false;
$user_auth=0;
foreach($login_users as $user){
	if($user['username']==$username and $user['password']==$password){
		$login_success=true;
		$user_auth=$user['user_auth'];
		break;
	}
}
$redis_list=array();
$auth_session="";
if($login_success){
	foreach($redis_connect_list as $redis){
		$redis_list[]=$redis['name'];
	}
	$auth_session=md5($username.$password.time());
	set_session($auth_session,array("user_auth"=>$user_auth));
}
echo json_encode(array("login_success"=>$login_success,"username"=>$username,"user_auth"=>$user_auth,"redis_list"=>$redis_list,"auth_session"=>$auth_session),JSON_UNESCAPED_UNICODE);
?>