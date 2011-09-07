<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_CrudModel extends CI_Model
{
	// set these in your constructor, BEFORE you call parent::__construct()
	protected $_db_table = NULL;
	protected $_db_fields = array();
	
	protected $_data = NULL;
	protected $_results = NULL;
	
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
		
		$this->load->helper('model');
		
		$this->init();
	}
	
	
	public function init()
	{
		$this->_data = array();
		$this->_results = array();
	}
	
	
	public function loadForId($id = FALSE)
	{
		if (!$id) {
			_log(__FUNCTION__.' : no id passed');
			return FALSE;
		}
		
		$this->db->select('*')
					->from($this->_db_table)
					->where('id', $id);
		$query = $this->db->get();
		
		if ($query->num_rows() != 1) {
			_debug(__FUNCTION__.' : no result for id '.$id);
			return FALSE;
		}
		$this->_data = $query->row_array();
		
		return $this;
	}
	
	
	public function where($field = FALSE, $value = FALSE) {
		if ($field && $value && in_array($field, $this->_db_field_names())) {
			$this->db->where($field, $value);
		}
		
		return $this;
	}
	
	
	public function save()
	{
		if (isset($this->_data['timestamp'])) {
			unset($this->_data['timestamp']);
		}
		
		if (isset($this->_data['id']) && (int)$this->_data['id'] > 0) {
			$query = $this->db->where('id', $this->_data['id'])
								->update($this->_db_table, $this->_data);
		}
		else {
			if (isset($this->_data['id'])) {
				unset($this->_data['id']);
			}
			$query = $this->db->insert($this->_db_table, $this->_data);
		}
		
		if ($this->db->affected_rows() != 1) {
			_log(__FUNCTION__.' : error saving '.print_r($this->_data, TRUE));
			return FALSE;
		}
		elseif (!isset($this->_data['id'])) {
			$this->_data['id'] = $this->db->insert_id();
		}
		
		return $this;
	}
	
	
	public function select($fields = FALSE)
	{
		if (FALSE === $fields) {
			_debug("No fields to select");
			return FALSE;
		}
		
		$this->db->select($fields);
		
		return $this;
	}
	
	
	public function by($field = FALSE, $order = FALSE)
	{
		if (!$field) {
			_log(__FUNCTION__.' : no field passed');
			return FALSE;
		}
		
		if (!$order) {
			$order = 'asc';
		}
		else {
			$order = strtolower($order);
		}
		if ($order != 'asc' && $order != 'desc') {
			$order = 'asc';
		}
		
		$this->db->order_by($field, $order);
		
		return $this;
	}
	
	
	public function load()
	{
		$query = $this->db->from($this->_db_table)
							->get();
		
		if ($query->num_rows() < 1) {
			_debug(__FUNCTION__.' : no rows loaded');
		}
		else {
			$this->_results = $query->result_array();
		}
		
		return $this;
	}
	
	
	public function next()
	{
		if (!is_array($this->_results)) {
			_log(__FUNCTION__.' : _results is not an array');
			return FALSE;
		}
		
		$this->_data = array_shift($this->_results);
		if (NULL === $this->_data) {
			_debug('No more results');
			return FALSE;
		}
		else {
			return $this;
		}
	}
	
	
	public function recordExists($field = FALSE, $value = FALSE)
	{
		$exists = FALSE;
		
		if ($field && $value && in_array($field, $this->_db_field_names())) {
			$this->db->select($field)
						->from($this->_db_table)
						->where($field, $value)
						->limit(1);
			$query = $this->db->get();
			
			$exists = ($query->num_rows() > 0);
		}
		
		return $exists;
	}
	
	
	// the magic
	public function __call($name, $arguments)
	{
		if (substr($name, 0, 3) == 'set') {
			// setter
			$property = column_name(substr($name, 3));
			if (isset($arguments[0]) && NULL !== $arguments[0]) {
				$this->_data[$property] = $arguments[0];
			}
			else {
				if (isset($this->_data[$property])) {
					unset($this->_data[$property]);
				}
			}
			
			return $this;
		}
		else {
			// getter
			$property = column_name($name);
			return isset($this->_data[$property]) ? $this->_data[$property] : NULL;
		}
	}
	
	
	public function setColumnData($property, $value = NULL)
	{
		if (NULL === $value) {
			if (isset($this->_data[$property])) {
				unset($this->_data[$property]);
			}
		}
		else {
			$this->_data[$property] = $value;
		}
		
		return $this;
	}
	
	
	public function getColumnData($property)
	{
		return isset($this->_data[$property]) ? $this->_data[$property] : NULL;
	}
	
	
	protected function _checkTable()
	{
		if (!$this->db->table_exists($this->_db_table)) {
			_log('Creating database table '.$this->_db_table);
			$this->load->dbforge();
			$fields = array(
				'id' => array(
					'type' => 'BIGINT',
					'unsigned' => TRUE,
					'auto_increment' => TRUE
				)
			);
			$this->dbforge->add_field(array_merge($fields, $this->_db_fields));
			$this->dbforge->add_field('timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($this->_db_table, TRUE);
		}
	}
	
	
	protected function _db_field_names()
	{
		$fields = array();
		
		foreach ($this->_db_fields as $name => $schema) {
			$fields[] = $name;
		}
		
		return $fields;
	}
}