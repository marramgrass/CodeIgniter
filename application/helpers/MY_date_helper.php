<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('unix_to_mysql')) {
	function unix_to_mysql($timestamp)
	{
		return mdate('%Y-%m-%d %H:%i:%s', $timestamp);
	}
}


if (!function_exists('start_of_this')) {
	// m[inute], h[our], d[ay], w[eek], M[onth], y[ear]
	// returns a unix timestamp to utc
	function start_of_this($interval = '')
	{
		$date = 0;
		
		switch ($interval) {
			case 'm':
				$date = mktime(date('h'), date('i'), 0);
				break;
			case 'h':
				$date = mktime(date('h'), 0, 0);
				break;
			case 'd':
				$date = mktime(0,0,0);
				break;
			case 'w':
				$date = strtotime('last monday');
				break;
			case 'M':
				$date = mktime(0,0,0, date('m'),1);
				break;
			case 'y':
				$date = mktime(0,0,0,1,1);
				break;
			default:
				$date = 0;
		}
		
		return $date;
	}
}


if (!function_exists('start_of_interval')) {
	// m[inute], h[our], d[ay], w[eek], M[onth], y[ear]
	// returns a unix timestamp to utc for start of this $interval
	function start_of_interval($interval = '', $timestamp = 0)
	{
		$date = 0;
		
		switch ($interval) {
			case 'm':
				$date = mktime(date('h', $timestamp), date('i', $timestamp), 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
				break;
			case 'h':
				$date = mktime(date('h', $timestamp), 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
				break;
			case 'd':
				$date = mktime(0, 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
				break;
			case 'w':
				$date = FALSE;  // not yet implemented
				break;
			case 'M':
				$date = mktime(0, 0, 0, date('n', $timestamp), 1, date('Y', $timestamp));
				break;
			case 'y':
				$date = mktime(0, 0, 0, 1, 1, date('Y', $timestamp));
				break;
			default:
				$date = 0;
		}
		
		return $date;
	}
}


if (!function_exists('date_bits')) {
	// d[ay], M[onth], y[ear]
	// returns a unix timestamp to utc
	function date_bits($component = FALSE)
	{
		$arr = array(0 => '');

		switch ($component) {
			case 'd':
				for ($i = 1; $i <= 31; ++$i) {
					$arr[$i] = $i;
				}
				break;

			case 'M':
				for ($i = 1; $i <= 12; ++$i) {
					$arr[$i] = $i;
				}
				break;

			case 'y':
				for ($i = date('Y'); $i >= 1890; $i--) {
					$arr[$i] = $i;
				}
				break;
			default:
				break;
		}

		return $arr;
	}
}