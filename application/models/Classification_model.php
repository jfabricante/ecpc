<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Classification_model extends CI_Model {

	private $oracle;

	public function __construct() {
		parent::__construct();

		$this->oracle = $this->load->database('oracle', true);
	}

	public function browse(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->oracle->get('CLASSIFICATION')->result();	
		}

		return $this->oracle->get('CLASSIFICATION')->result_array();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->oracle->get_where('CLASSIFICATION', array('ID' => $params['id']));

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
			$this->oracle->update('CLASSIFICATION', $params, array('ID' => $id));
		}
		else
		{
			$this->oracle->insert('CLASSIFICATION', $params);
		}

		return $this;	
	}

	public function delete()
	{
		$id = $this->input->post('ID');

		$this->oracle->delete('CLASSIFICATION', array('ID' => $id));

		return $this;
	}

	public function exist($params)
	{
		$query = $this->oracle->get_where('CLASSIFICATION', array('PRODUCT_MODEL' => $params['PRODUCT_MODEL']));

		return $query->num_rows();
	}
}
