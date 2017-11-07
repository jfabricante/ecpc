<link href="<?php echo base_url('resources/plugins/select/css/bootstrap-select.min.css');?>" rel="stylesheet" >
<form action="<?php echo base_url('index.php/vin/store'); ?>" method="post">
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
			<label for="product_model">Product Model</label>
			<input type="text" class="form-control" id="product_model" name="PRODUCT_MODEL" value="<?php echo isset($entity->PRODUCT_MODEL) ? $entity->PRODUCT_MODEL : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="product_name">Product Year</label>
			<input type="text" class="form-control" id="product_year" name="PRODUCT_YEAR" value="<?php echo isset($entity->PRODUCT_YEAR) ? $entity->PRODUCT_YEAR : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="description">Description</label>
			<input type="text" class="form-control" id="description" name="DESCRIPTION" value="<?php echo isset($entity->DESCRIPTION) ? $entity->DESCRIPTION : ''; ?>">
		</div>

		<div class="form-group">
			<label for="lot_size">Lot Size</label>
			<input type="number" class="form-control" id="lot_size" name="LOT_SIZE" value="<?php echo isset($entity->LOT_SIZE) ? $entity->LOT_SIZE : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="cp_id">CP</label>
			<select name="CP_ID" id="cp_id" class="form-control selectpicker" data-live-search="true">
				<option></option>

				<?php foreach($cp_items as $row): ?>
					<option value="<?php echo $row->ID; ?>" <?php echo isset($entity->CP_ID) ? $entity->CP_ID == $row->ID ? 'selected' : '' : ''; ?> >
						<?php echo $row->MODEL . ' - '. $row->YEAR_MODEL; ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="form-group">
			<label for="qr">QR Images</label>
			<input type="text" class="form-control" id="qr" name="QR" value="<?php echo isset($entity->QR) ? $entity->QR : ''; ?>" >
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
		$('#cp_id').selectpicker({});
	});
</script>
					