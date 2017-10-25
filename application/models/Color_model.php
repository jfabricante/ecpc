<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Color_model extends CI_Model {

	private $oracle;

	public function __construct() {
		parent::__construct();

		$this->oracle = $this->load->database('oracle', true);
	}

	public function browse(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->oracle->get('COLOR')->result();	
		}

		return $this->oracle->get('COLOR')->result_array();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->oracle->get_where('COLOR', array('ID' => $params['id']));

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
			$this->oracle->update('COLOR', $params, array('ID' => $id));
		}
		else
		{
			$this->oracle->insert('COLOR', $params);
		}

		return $this;	
	}

	public function delete()
	{
		$id = $this->input->post('ID');

		$this->oracle->delete('COLOR', array('ID' => $id));

		return $this;
	}

	public function exist($params)
	{
		$query = $this->oracle->get_where('COLOR', array('PRODUCT_MODEL' => $params['PRODUCT_MODEL']));

		return $query->num_rows();
	}

	public function migrateItems()
	{

		$query = $this->oracle->get('COLOR');

		$old_resource = $this->browse(array('type' => 'array'));

		$this->oracle->insert_batch('COLOR', $old_resource);

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
