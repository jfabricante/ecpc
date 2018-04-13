<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class QR extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('session');

		$helpers = array('form', 'directory');

		$this->load->helper($helpers);

		$this->_redirect_unauthorized();
	}

	public function index()
	{
		$data = array(
				'title'   => 'List of QR Codes',
				'content' => 'qr/list_view',
				'images'  => directory_map(FCPATH . '/resources/images/qr/')
			);

		$this->load->view('include/template', $data);	
	}

	public function form()
	{
		$data = array(
				'title'   => 'Add New QR',
				'content' => 'qr/form_view',
			);

		$this->load->view('include/template', $data);
	}

	public function store()
	{
		$this->_handleUpload();
	}

	protected function _handleUpload()
	{
		$existing_images = directory_map(FCPATH . '/resources/images/qr/');

		$images = $_FILES['qr_code']['name'];

		// Verify images
		$matches = array_intersect($_FILES['qr_code']['name'], $existing_images);

		if ($matches)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">The QR code ' . join($matches, ', ') . ' is already exist in the system!</div>');

			if (count($matches) > 1)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-danger">The following QR code: ' . join($matches, ', ') . ' are already exist in the system!</div>');
			}

			redirect(base_url('index.php/qr/form'));
		}
		else
		{
			for($i = 0; $i < count($_FILES['qr_code']['name']); $i++)
			{
				move_uploaded_file($_FILES['qr_code']['tmp_name'][$i], './resources/images/qr/' . $_FILES['qr_code']['name'][$i]);
			}

			$this->session->set_flashdata('message', '<div class="alert alert-success">The QR code has been uploaded successfully!</div>');

			if (count($_FILES['qr_code']['name']) > 1)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">The QR code(s) has been uploaded successfully!</div>');
			}

			redirect(base_url('index.php/qr/index'));
		}
		
	}

	protected function _redirect_unauthorized()
	{
		if (count($this->session->userdata()) < 3)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Login first!</div>');

			redirect(base_url());
		}
	}
}
