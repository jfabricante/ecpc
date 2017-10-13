<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vin_control_model extends CI_Model {

	public function __construct() {
		parent::__construct();

		$this->load->database();
	}

	public function browse(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->db->get('vin_control_tbl')->result();	
		}

		return $this->db->get('vin_control_tbl')->result_array();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->db->get_where('vin_control_tbl', array('id' => $params['id']));

			if ($params['type'] == 'object')
			{
				return $query->row();
			}
			
			return $query->row_array();
		}
	}

	public function readByProductModel($params)
	{
		$query = $this->db->order_by('lot_no', 'DESC')->get_where('vin_control_tbl', $params);

		return $query->row();
	}

	public function store($params)
	{
		$id = $this->input->post('ID') ? $this->input->post('ID') : 0;

		if ($id > 0)
		{
			$this->db->update('vin_control_tbl', $params, array('id' => $id));
		}
		else
		{
			$this->db->insert('vin_control_tbl', $params);
		}

		return $this;
	}

	public function delete()
	{
		$id = $this->input->post('id');

		$this->db->delete('vin_control_tbl', array('id' => $id));

		return $this;
	}

	public function fetchLot($params)
	{
		$query = $this->db->select('lot_no')
				->from('vin_control_tbl')
				->where($params)
				->get();

		return $query->result();
	}
}
