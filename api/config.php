<?php
$redis_connect_list=array(
	array(
		"name"=>'link1',
		"host"=>'127.0.0.1',
		"port"=>'6379',
		"auth"=>''
	),
	array(
		"name"=>'link2',
		"host"=>'127.0.0.1',
		"port"=>'6379',
		"auth"=>''
	)
);

$login_users=array(
	array(
		"username"=>'admin',
		"password"=>'admin',
		"user_auth"=>1//0 check  ,1 check edit
	),
	array(
		"username"=>'admin2',
		"password"=>'admin2',
		"user_auth"=>0//0 check  ,1 check edit
	)
);
?>