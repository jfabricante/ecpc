<link href="<?php echo base_url('resources/plugins/datepicker/css/bootstrap-datepicker.min.css');?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/select/css/bootstrap-select.min.css');?>" rel="stylesheet" >
<form action="<?php echo base_url('index.php/cop/store'); ?>" method="post">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>
		<h4 class="modal-title"><?php echo $title; ?></h4>
	</div>

	<div class="modal-body">
		<div class="form-group hidden">
			<input type="number" class="form-control" id="id" name="ID" value="<?php echo isset($entity->ID) ? $entity->ID : ''; ?>">
		</div>

		<div class="form-group">
			<label for="cp_no">CP No.</label>
			<input type="number" class="form-control" id="cp_no" name="CP_NO" value="<?php echo isset($entity->CP_NO) ? $entity->CP_NO : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="invoice_no">Invoice No.</label>
			<select name="INVOICE_NO" id="invoice_no" class="form-control selectpicker" data-live-search="true">
				<option></option>

				<?php foreach($invoices as $row): ?>
					<option value="<?php echo $row->INVOICE_NO; ?>" <?php echo isset($entity->INVOICE_NO) ? $entity->INVOICE_NO == $row->INVOICE_NO ? 'selected' : '' : ''; ?> >
						<?php echo $row->INVOICE_NO; ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="form-group">
			<label for="completion_date">Completion Date</label>
			<input type="text" class="form-control datepicker" id="completion_date" name="COMPLETION_DATE" value="<?php echo isset($entity->COMPLETION_DATE) ? date('m/d/Y',  strtotime($entity->COMPLETION_DATE)) : ''; ?>" required>
		</div>

	</div>
	
	<div class="modal-footer">
		<div class="form-group">
			<button type="button" class="btn btn-flat btn-info pull-left" data-dismiss="modal">Close</button>
			<input type="submit" value="Submit" class="btn btn-flat btn-danger">
		</div>
	</div>
	
</form><!-- End Form -->
<script src="<?php echo base_url('resources/plugins/select/js/bootstrap-select.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/datepicker/js/bootstrap-datepicker.min.js');?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#invoice_no').selectpicker({});

		$('#completion_date').datepicker({});
	});
</script>
					