<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'other_sources/facebook/src/facebook.php';

class MY_FacebookController extends CI_Controller
{
	protected $_facebook		= false;
	protected $_fbid			= false;
	protected $_signed_request	= false;
	protected $_page			= false;
	
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
			'secret' => $this->config->item('fb_app_secret')
			)
		);
		
		// check for an FB signed_request, and stash it to the session
		// or grab an old one from the session
		// handle the page array separately, as it won't be included in
		// a signed_request that comes via the JS SDK
		$this->_signed_request = $this->_facebook->getSignedRequest();
		if ($this->_signed_request) {
			// _log('Have signed request');
			$this->session->set_userdata('fbsr', serialize($this->_signed_request));
			
			if (array_key_exists('page', $this->_signed_request)) {
				$this->_page = $this->_signed_request['page'];
				$this->session->set_userdata('fbpage', serialize($this->_page));
			}
			else {
				$sess_page = $this->session->userdata('fbpage');
				$this->_page = $sess_page ? unserialize($sess_page) : false;
			}
		}
		else {
			// _log('Retrieving SR from session in '.$_SERVER['HTTP_USER_AGENT']);
			$sess_sr = $this->session->userdata('fbsr');
			$this->_signed_request = $sess_sr ? unserialize($sess_sr) : false;
			
			$sess_page = $this->session->userdata('fbpage');
			$this->_page = $sess_page ? unserialize($sess_page) : false;
		}
		
		// then set _fbid
		if ($this->_signed_request && isset($this->_signed_request['user_id'])) {
			$this->_fbid = $this->_signed_request['user_id'];
			// _log('FBID: '.$this->_fbid);
		}
	}
	
	
	protected function _checkLikeStatus()
	{
		$liked = false;
		
		if ($this->_page &&  array_key_exists('liked', $this->_page)) {
				$liked = $this->_page['liked'];
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