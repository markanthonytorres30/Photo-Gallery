<?php

/// Start session
session_start();

/// Because we don't care about notices
if(function_exists("error_reporting")){
	error_reporting(E_ERROR | E_WARNING);
}

/// Autoload classes
function my_autoload($class){
	if(file_exists(dirname(__FILE__)."/src/classes/$class.php")){
		require(dirname(__FILE__)."/src/classes/$class.php");
	}else{
		return FALSE;
	}
}

spl_autoload_register("my_autoload");

/// Take care of nasty exceptions
function exception_handler($exception) {
  echo "<div class='exception'>" , $exception->getMessage(), "</div>\n";
}
set_exception_handler('exception_handler');

ini_set('upload_max_filesize','10M');

function protect_user_send_var($var){
	if(is_array($var))
		return array_map('protect_user_send_var', $var);
	else 
		return addslashes($var);
}


if (!get_magic_quotes_gpc()){
	$_POST = protect_user_send_var($_POST);
	$_COOKIE = protect_user_send_var($_COOKIE);
	$_GET = protect_user_send_var($_GET);
}

if(isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == 'text/xml'){
// Nope, definitely not ready yet.
//	new API();
}else{
	new Index();
}
?>