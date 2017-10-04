<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vin_model extends CI_Model {

	public function __construct() {
		parent::__construct();

		$this->load->database();
	}

	public function browse(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->db->get('vin_model_tbl')->result();
		}

		return $this->db->get('vin_model_tbl')->result_array();
	}

	public function browse_with_cp()
	{
		$fields = array(
				'a.id',
				'a.product_model',
				'a.product_year',
				'a.description',
				'a.lot_size',
				'a.cp_id',
				'b.engine_pref',
			);

		$query = $this->db->select("*")
				->from('vin_model_tbl AS a')
				->join('cp_tbl AS b', 'a.cp_id = b.id', 'LEFT')
				->order_by('a.product_model')
				->get();

		return $query->result();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->db->get_where('vin_model_tbl', array('id' => $params['id']));

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
			$this->db->update('vin_model_tbl', $params, array('id' => $id));
		}
		else
		{
			$this->db->insert('vin_model_tbl', $params);
		}

		return $this;	
	}

	public function delete()
	{
		$id = $this->input->post('id');

		$this->db->delete('vin_model_tbl', array('id' => $id));

		return $this;
	}

	public function exist($params)
	{
		$query = $this->db->get_where('vin_model_tbl', array('product_model' => $params['product_model']));

		return $query->num_rows();
	}
}
