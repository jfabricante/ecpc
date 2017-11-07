<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cop_model extends CI_Model {

	private $oracle;

	public function __construct() {
		parent::__construct();

		$this->oracle = $this->load->database('oracle', true);
	}

	public function browse(array $params = array('type' => 'object'))
	{
		$fields = array(
				'a.ID',
				'a.CP_NO',
				'a.INVOICE_NO',
				'a.CP_DATE',
				'b.LOT_NO',
				'b.ENTRY_NO',
				'b.PRODUCT_MODEL',
				'a.ETD',
				'a.ETA',
				'a.PAYMENT_DATE',
				'a.TRANSMITTAL_DATE',
				'COUNT(LOT_NO) OVER(PARTITION BY PRODUCT_MODEL, LOT_NO) AS QTY',
			);

		$query = $this->oracle->distinct('LOT_NO')
				->select($fields)
				->from('COP a')
				->join('VIN_ENGINE b', 'a.INVOICE_NO = b.INVOICE_NO', 'INNER')
				->get();

		return $query->result();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->oracle->get_where('COP', array('ID' => $params['id']));

			if ($params['type'] == 'object')
			{
				return $query->row();
			}
			
			return $query->row_array();
		}
	}

	public function store($params)
	{
		$id = $this->input->post('ID') ? $this->input->post('ID') : 0;

		if ($id > 0)
		{
			$this->oracle->update('COP', $params, array('ID' => $id));
		}
		else
		{
			$this->oracle->insert('COP', $params);
		}

		return $this;	
	}

	public function delete()
	{
		$id = $this->input->post('ID');

		$this->oracle->delete('COP', array('ID' => $id));

		return $this;
	}

	public function exist($params)
	{
		$query = $this->oracle->get_where('COP', array('cop' => $params['cop']));

		return $query->num_rows();
	}

	public function fetchRange($params)
	{
		$clause = sprintf("%s BETWEEN TO_DATE('%s', 'MM/DD/YYYY') AND TO_DATE('%s', 'MM/DD/YYYY')", $params['field'], $params['from'], $params['to']);

		$fields = array(
				'a.CP_NO',
				'a.CP_DATE',
				'a.INVOICE_NO',
				'b.ENTRY_NO',
				'b.PRODUCT_MODEL',
				'b.LOT_NO',
				'COUNT(LOT_NO) OVER(PARTITION BY PRODUCT_MODEL, LOT_NO) AS QTY',
				'a.ETD',
				'a.ETA',
				'a.PAYMENT_DATE',
				'a.TRANSMITTAL_DATE',
			);
		
		$query = $this->oracle->distinct('LOT_NO')
				->select($fields)
				->from('COP a')
				->join('VIN_ENGINE b', 'a.INVOICE_NO = b.INVOICE_NO', 'INNER')
				->where($clause)
				->get();


		return $query->result_array();
	}

	protected function _redirect_unauthorized()
	{
		if (count($this->session->userdata) < 3)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Login first!</div>');

			redirect(base_url());
		}
	}
}
