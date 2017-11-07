<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load third party
require_once APPPATH . '/third_party/PHPExcel/Classes/PHPExcel.php';

class Cop extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('session');

		$this->_redirect_unauthorized();

		$models = array('cop_model', 'vin_engine_model');

		$this->load->model($models);
	}

	public function list_()
	{
		$data = array(
				'title'    => 'List of CP',
				'content'  => 'cop/list_view',
				'entities' => $this->cop_model->browse()
			);

		$this->load->view('include/template', $data);
	}

	public function form()
	{
		$id = $this->uri->segment(3) ? $this->uri->segment(3) : 0;

		$config = array(
				'id'   => $id,
				'type' => 'object'
			);

		$data = array(
				'title'    => $id ? 'Update Details' : 'Add CP Entry',
				'entity'   => $id ? $this->cop_model->read($config) : '',
				'invoices' => $this->vin_engine_model->fetchInvoice()
			);

		$this->load->view('cop/form_view', $data);
	}

	public function store()
	{
		$id = $this->input->post('ID') ? $this->input->post('ID') : 0;

		// Trim the post data
		$config = array_map('trim', $this->input->post());

		$config['LAST_USER']        = $this->session->userdata('fullname');
		$config['LAST_UPDATE']      = date('d-M-Y');
		$config['CP_DATE']          = date('d-M-Y', strtotime($config['CP_DATE']));
		$config['ETD']              = date('d-M-Y', strtotime($config['ETD']));
		$config['ETA']              = date('d-M-Y', strtotime($config['ETA']));
		$config['PAYMENT_DATE']     = date('d-M-Y', strtotime($config['PAYMENT_DATE']));
		$config['TRANSMITTAL_DATE'] = date('d-M-Y', strtotime($config['TRANSMITTAL_DATE']));
		
		$this->cop_model->store($config);

		if ($id > 0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">CP item has been updated!</div>');
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">CP item has been added!</div>');
		}

		redirect($this->agent->referrer());
	}

	public function notice()
	{
		$data['id'] = $this->uri->segment(3);

		$this->load->view('cop/delete_view', $data);
	}

	public function delete()
	{
		$this->cop_model->delete();

		$this->session->set_flashdata('message', '<div class="alert alert-success">CP item has been deleted!</div>');

		redirect($this->agent->referrer());
	}

	public function ajax_cop_list()
	{
		echo json_encode($this->cop_model->browse(), true);
	}

	protected function _redirect_unauthorized()
	{
		if (count($this->session->userdata()) < 3)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Login first!</div>');

			redirect(base_url());
		}
	}

	public function handle_excel_report()
	{
		$config = $this->cop_model->fetchRange($this->input->post());

		if (count($config))
		{
			$this->_excelReport($config);
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-warning">No items on particular dates!</div>');

			redirect($this->agent->referrer());
		}
	}	

	protected function _excelReport($params)
	{
		if (count($params))
		{
			// Create an instance of PHPExcel
			$excelObj          = new PHPExcel();
			$excelActiveSheet  = $excelObj->getActiveSheet();
			$excelDefaultStyle = $excelObj->getDefaultStyle();

			$excelDefaultStyle->getFont()->setSize(10)->setName('Times New Roman');

			// Set the Active sheet
			$excelObj->setActiveSheetIndex(0);

			// Set alignment
			$excelDefaultStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			// Add header to the excel
			$excelActiveSheet->setCellValue('A1', 'CP No.')
							->setCellValue('B1', 'CP Date')
							->setCellValue('C1', 'Invoice No.')
							->setCellValue('D1', 'Entry No.')
							->setCellValue('E1', 'Model')
							->setCellValue('F1', 'Lot No.')
							->setCellValue('G1', 'Quantity')
							->setCellValue('H1', 'ETD')
							->setCellValue('I1', 'ETA')
							->setCellValue('J1', 'Payment Date')
							->setCellValue('K1', 'Trasmittal Date');

			$excelActiveSheet->getStyle('A1:K1')->getAlignment()->setWrapText(true); 

			$excelActiveSheet->fromArray($params, NULL, 'A2');

			// Paper Orientation
			$excelActiveSheet->getPageSetup()
				    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			// Paper Size
			$excelActiveSheet->getPageSetup()
				->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

			// Set margins
			$excelActiveSheet->getPageMargins()->setTop(0.25);
			$excelActiveSheet->getPageMargins()->setRight(0.25);
			$excelActiveSheet->getPageMargins()->setLeft(0.25);
			$excelActiveSheet->getPageMargins()->setBottom(0.25);

			// Apply the column title on every pages
			$excelActiveSheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1,1);

			// Fit the content to page
			$excelActiveSheet->getPageSetup()->setFitToWidth(1);    
			$excelActiveSheet->getPageSetup()->setFitToHeight(0);


			// Apply background color on cell
			$excelActiveSheet->getStyle('A1:K1')
							->getFill()
							->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							->getStartColor()
							->setRGB('ffff99');

			// Set the alignment for the whole document
			$excelDefaultStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$style = array(
				        'borders' => array(
				            'allborders' => array(
				                'style' => PHPExcel_Style_Border::BORDER_THIN,
				                'color' => array('rgb' => '000000')
				            )
				        ),
				        'alignment' => array(
				        	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
				        ),
				    );

			// Apply header style
			$excelActiveSheet->getStyle("A1:K1")->applyFromArray($style);

			// Excel filename
			$filename = 'cp.xlsx';

			// Content header information
			header('Content-Type: application/vnd.ms-excel'); //mine type
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Cached-Control: max-age=0');

			// Generate excel version using Excel 2017
			$objWriter = PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');

			$objWriter->save('php://output');
		}
	
	}
}