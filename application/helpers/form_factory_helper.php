<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('arrayify_fields')) {
	function arrayify_fields($fields = array(), $name = '', $mode = 'key')
	{
		$output = array();
		
		if ('key' == $mode) {
			foreach ($fields as $key => $value) {
				$output[$name.'['.$key.']'] = $value;
			}
		}
		else if ('value' == $mode) {
			foreach ($fields as $key => $value) {
				$output[$key] = $name.'['.$value.']';
			}
		}
		
		return $output;
	}
}
else {
	error_log("Can't redefine arrayify_fields()");
}


if (!function_exists('form_factory')) {
	function form_factory($fields = array(), $data = array(), $type = 'text', $error_fields = array())
	{
		$output = '';
		
		if ('text' == $type) {
			foreach ($fields as $field) {
				$output .= '<div';
				$output .= in_array($field['name'], $error_fields) ? ' class="error"' : '';
				$output .= ">\n";
				$output .= '<label for="'.$field['id'].'" class="text">'.$field['label']."</label>\n";
				$output .= '<input type="text" name="'.$field['name'].'" id="'.$field['id'].'"';
				if (isset($field['class'])) {
					$output .= ' class="'.$field['class'].'"';
				}
				if (isset($data[$field['name']])) {
					$output .= ' value="'.$data[$field['name']].'"';
				}
				$output .= ' />'."\n";
				$output .= "</div>\n\n";
			}
		}

		if ('select' == $type) {
			foreach ($fields as $field) {
				$output .= '<div';
				$output .= in_array($field['name'], $error_fields) ? ' class="error"' : '';
				$output .= ">\n";
				$output .= '<label for="'.$field['id'].'" class="select">'.$field['label']."</label>\n";
				$output .= '<select name="'.$field['name'].'" id="'.$field['id'].'"';
				if (isset($field['class'])) {
					$output .= ' class="'.$field['class'].'"';
				}
				$output .= ' />'."\n";
				
				foreach ($field['options'] as $v => $t) {
					$output .= '<option value="' . $v . '"';
					if (isset($data[$field['name']]) && $data[$field['name']] == $v) {
						$output .= ' selected="selected"';
					}
					$output .= '>' . $t . "</option>\n";
				}
				
				$output .= "</select>\n";
				$output .= "</div>\n\n";
			}
		}
		
		if ('radio' == $type) {
			foreach ($fields as $field) {
				$output .= '<p class="radioset';
				$output .= in_array($field['name'], $error_fields) ? ' error' : '';
				$output .= '"><span class="label">'."\n";
				$output .= $field['label'].'</span> <span>';
				foreach ($field['options'] as $option) {
					$output .= '<input type="radio" name="'.$field['name'].'" id="'.$option['id'].'" value="'.$option['value'].'"';
					if (isset($data[$field['name']]) && $data[$field['name']] == $option['value']) {
						$output .= ' checked="checked"';
					}
					$output .= ' />'."\n";
					$output .= '<label for="'.$option['id'].'" class="radio">'.$option['label']."</label>\n";
				}
				$output .= "</span></p>\n\n";
			}
		}
		
		if ('hidden' == $type) {
			foreach ($fields as $key => $value) {
				$output .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />'."\n";
			}
		}
		
		return $output;
	}
}
else {
	error_log("Can't redefine form_factory()");
}