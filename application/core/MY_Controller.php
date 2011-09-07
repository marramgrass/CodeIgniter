<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'other_sources/facebook/src/facebook.php';

class MY_FacebookController extends CI_Controller
{
	protected $_facebook		= false;
	protected $_fbid			= false;
	protected $_signed_request	= false;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->library('session');
		$this->load->config('facebook');
		$this->load->helper('logger');
		
		$this->_initializeSignedRequest();
	}
	
	
	protected function _initializeSignedRequest()
	{
		$this->_facebook = new Facebook(array(
			'appId' => $this->config->item('fb_app_id'),
			'secret' => $this->config->item('fb_app_secret'),
			'cookie' => true)
		);
		
		// check for an FB signed_request, and stash it to the session
		// or grab an old one from the session
		$this->_signed_request = $this->_facebook->getSignedRequest();
		if ($this->_signed_request) {
			// _log('Have signed request');
			$this->session->set_userdata('fbsr', serialize($this->_signed_request));
		}
		else {
			// _log('Retrieving SR from session in '.$_SERVER['HTTP_USER_AGENT']);
			$sess_sr = $this->session->userdata('fbsr');
			if ($sess_sr) {
				$this->_signed_request = unserialize($sess_sr);
			}
			else {
				
			}
		}
		// then set _fbid
		if (isset($this->_signed_request['user_id'])) {
			$this->_fbid = $this->_signed_request['user_id'];
			// _log('FBID: '.$this->_fbid);
		}
	}
	
	
	protected function _checkLikeStatus()
	{
		$liked = false;
		
		if ($this->_signed_request && array_key_exists('page', $this->_signed_request) && array_key_exists('liked', $this->_signed_request['page'])) {
				$liked = $this->_signed_request['page']['liked'];
		}
		else if ($this->_signed_request) {
			// _log($this->_signed_request.'  '.$_SERVER['HTTP_USER_AGENT']);
		}
		else {
			// _log('No signed request  '.$_SERVER['HTTP_USER_AGENT']."  ----  ".print_r($this->_signed_request, TRUE));
		}
		
		return $liked;
	}
	
	
	protected function _checkAuthStatus()
	{
		return (BOOL)$this->_fbid;
	}
	
	
	protected function _getFbidFromAccessToken($token)
	{
		$fbid = false;
		
		$this->_facebook->setAccessToken($token);
		$fbid = $this->_facebook->getUser();
		
		return $fbid;
	}
}