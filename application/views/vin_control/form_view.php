<form action="<?php echo base_url('index.php/vin_control/store'); ?>" method="post">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>
		<h4 class="modal-title"><?php echo $title; ?></h4>
	</div>

	<div class="modal-body">
		<div class="form-group hidden">
			<input type="number" class="form-control" id="id" name="id" value="<?php echo isset($entity->id) ? $entity->id : 0; ?>">
		</div>

		<div class="form-group">
			<label for="code">Code</label>
			<input type="text" class="form-control" id="code" name="code" value="<?php echo isset($entity->code) ? $entity->code : ''; ?>">
		</div>

		<div class="form-group">
			<label for="vin_no">Vin No.</label>
			<input type="text" class="form-control" id="vin_no" name="vin_no" value="<?php echo isset($entity->vin_no) ? $entity->vin_no : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="lot_no">Lot No.</label>
			<input type="text" class="form-control" id="lot_no" name="lot_no" value="<?php echo isset($entity->lot_no) ? $entity->lot_no : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="product_model">Model</label>
			<input type="text" class="form-control" id="product_model" name="product_model" value="<?php echo isset($entity->product_model) ? $entity->product_model : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="model_name">Model Name</label>
			<input type="text" class="form-control" id="model_name" name="model_name" value="<?php echo isset($entity->model_name) ? $entity->model_name : ''; ?>" required>
		</div>
	</div>
	
	<div class="modal-footer">
		<div class="form-group">
			<button type="button" class="btn btn-flat btn-info pull-left" data-dismiss="modal">Close</button>
			<input type="submit" value="Submit" class="btn btn-flat btn-danger">
		</div>
	</div>
	
</form><!-- End Form -->
					