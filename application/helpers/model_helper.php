<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('column_name')) {
	function column_name($name)
	{
		$property =  strtolower(preg_replace('/([A-Z])/', '_$1', $name));
		
		if (substr($property, 0, 1) == '_') {
			$property = substr($property, 1);
		}
		
		return $property;
	}
}