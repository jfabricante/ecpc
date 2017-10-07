<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vin_engine_model extends CI_Model {

	public function __construct() {
		parent::__construct();

		$this->load->database();
	}

	public function browse(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->db->get('vin_engine_tbl')->result();	
		}

		return $this->db->get('vin_engine_tbl')->result_array();
	}

	public function fetchFields()
	{
		$fields = array('vin_no', 'engine_no');

		$query = $this->db->select($fields)
				->from('vin_engine_tbl')
				->get();

		return $query->result_array();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->db->get_where('vin_engine_tbl', array('id' => $params['id']));

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
			$this->db->update('vin_engine_tbl', $params, array('id' => $id));
		}
		else
		{
			$this->db->insert('vin_engine_tbl', $params);
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

	public function fetchInvoice()
	{
		$query = $this->db->select('invoice_no')
				->from('vin_engine_tbl')
				->distinct('invoice_no')
				->get();

		return $query->result();
	}

	public function fetchInvoiceItem($params)
	{
		$fields = array(
					'a.portcode',
					'a.serial',
					'a.entry_no',
					'a.vin_no',
					'a.engine_no',
					'a.classification',
					'a.lot_no',
					'a.invoice_no',
					'a.year',
					'a.color',
					'c.series',
					'c.piston_displacement',
					'c.body_type',
					'c.year_model',
					'c.gross_weight',
					'c.net_weight',
					'c.cylinder',
					'c.fuel',
				);

		// If the first query has no result
		$query = $this->db->select($fields)
				->from('vin_engine_tbl AS a')
				->join('vin_model_tbl AS b', 'a.product_model = b.product_model', 'INNER')
				->join('cp_tbl AS c', 'b.cp_id = c.id', 'INNER')
				->where($params)
				->get();

		// Generate query that directs to cp details model
		if (!$query->num_rows())
		{
			$query = $this->db->select($fields)
					->from('vin_engine_tbl AS a')
					->join('cp_tbl AS c', 'a.product_model = c.model', 'INNER')
					->where($params)
					->get();
		}

		return $query->result();
	}

	public function fetchInvoiceView($params)
	{
		$query = $this->db->get_where('vin_engine_tbl', $params);
	
		return $query->result();
	}

	public function fetchModelItems($params)
	{
		$clause = sprintf('lot_no BETWEEN %s AND %s', $params['lot_from'], $params['lot_to']);

		$query = $this->db->from('vin_engine_tbl')
				->where('product_model', $params['product_model'])
				->where($clause)
				->get();

		return $query->result();
	}

	public function store_batch(array $data)
	{
		$this->db->insert_batch('vin_engine_tbl', $data);
	}

	public function fetchDistinctModel()
	{
		$query = $this->db->distinct('product_model')
				->select('product_model')
				->get('vin_engine_tbl');

		return $query->result();
	}

	public function fetchDistinctLot($params)
	{
		$query = $this->db->distinct('lot_no')
				->select('lot_no')
				->order_by('lot_no', 'ASC')
				->where($params)
				->get('vin_engine_tbl');

		return $query->result();
	}
}
