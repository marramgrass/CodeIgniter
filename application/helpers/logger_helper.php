<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('scalarify')) {
	function scalarify($var) {
		if (!is_scalar($var)) {
			$var = print_r($var, TRUE);
		}
		
		return $var;
	}
}

if (!function_exists('_debug')) {
	function _debug($message)
	{
		if (config_item('debug')) {
			error_log(scalarify($message));
		}
	}
}


if (!function_exists('_log')) {
	function _log($message)
	{
		error_log(scalarify($message));
	}
}


if (!function_exists('_pre')) {
	function _pre($message, $ret = FALSE)
	{
		$message = '<pre>'.scalarify($message).'</pre>';
	
		if ($ret) {
			return $message;
		}
		else {
			echo $message;
		}
	}
}