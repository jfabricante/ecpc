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
			<input type="number" class="form-control" id="id" name="id" value="<?php echo isset($entity->id) ? $entity->id : ''; ?>">
		</div>

		<div class="form-group">
			<label for="cp_no">CP No.</label>
			<input type="number" class="form-control" id="cp_no" name="cp_no" value="<?php echo isset($entity->cp_no) ? $entity->cp_no : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="invoice_no">Invoice No.</label>
			<select name="invoice_no" id="invoice_no" class="form-control selectpicker" data-live-search="true">
				<option></option>

				<?php foreach($invoices as $row): ?>
					<option value="<?php echo $row->invoice_no; ?>" <?php echo isset($entity->invoice_no) ? $entity->invoice_no == $row->invoice_no ? 'selected' : '' : ''; ?> >
						<?php echo $row->invoice_no; ?>
					</option>
				<?php endforeach; ?>
			</select>
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
<script type="text/javascript">
	$(document).ready(function() {
		$('#invoice_no').selectpicker({});
	});
</script>
					