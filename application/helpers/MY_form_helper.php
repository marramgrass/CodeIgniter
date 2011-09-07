<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('error_class')) {
	function error_class($name, $error_fields, $class = 'error')
	{
		return in_array($name, $error_fields) ? $class : '';
	}
}
else {
	error_log("Can't redefine error_class()");
}