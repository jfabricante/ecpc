<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Serial_model extends CI_Model {

	public function __construct() {
		parent::__construct();

		$this->load->database();
	}

	public function browse(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->db->get('serial_tbl')->result();	
		}

		return $this->db->get('serial_tbl')->result_array();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->db->get_where('serial_tbl', array('id' => $params['id']));

			if ($params['type'] == 'object')
			{
				return $query->row();
			}
			
			return $query->row_array();
		}
	}

	public function store($params)
	{
		$id = $this->input->post('id') ? $this->input->post('id') : 0;

		if ($id > 0)
		{
			$this->db->update('serial_tbl', $params, array('id' => $id));
		}
		else
		{
			$this->db->insert('serial_tbl', $params);
		}

		return $this;	
	}

	public function delete()
	{
		$id = $this->input->post('id');

		$this->db->delete('serial_tbl', array('id' => $id));

		return $this;
	}

	public function exist($params)
	{
		$query = $this->db->get_where('serial_tbl', array('product_model' => $params['product_model']));

		return $query->num_rows();
	}
}
