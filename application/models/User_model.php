<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

	public function __construct() {
		parent::__construct();

		$this->load->database();
	}

	public function access($emp_id)
	{
		$fields = array('a.user_type_id', 'system_id', 'user_type');

		$query = $this->db->select('*')
				->from('user_access_tab AS a')
				->join('user_type_tab AS b', 'a.user_type_id = b.id', 'INNER')
				->where('a.employee_id', $emp_id)
				->where('a.system_id = 34')
				->get();

		return $query->row_array();
	}

	public function users_count()
	{
		return $this->db->get('users_tbl')->num_rows();
	}

	public function truncate_tbl()
	{
		$this->db->truncate('users_tbl');
		$this->db->truncate('users_role_tbl');
	}

	public function update_allowance($params)
	{
		if (is_array($params['employee']))
		{
			$config = array(
					'meal_allowance' => $params['remaining_credit']
				);

			$this->db->update('users_meal_allowance_tbl', $config, array('user_id' => $params['employee']['id']));
		}
	}
	
}