<form action="<?php echo base_url('index.php/vin_control/store'); ?>" method="post">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>
		<h4 class="modal-title"><?php echo $title; ?></h4>
	</div>

	<div class="modal-body">
		<div class="form-group hidden">
			<input type="number" class="form-control" id="id" name="ID" value="<?php echo isset($entity->ID) ? $entity->ID : 0; ?>">
		</div>

		<div class="form-group">
			<label for="code">Code</label>
			<input type="text" class="form-control" id="code" name="CODE" value="<?php echo isset($entity->CODE) ? $entity->CODE : ''; ?>">
		</div>

		<div class="form-group">
			<label for="vin_no">Vin No.</label>
			<input type="text" class="form-control" id="vin_no" name="VIN_NO" value="<?php echo isset($entity->VIN_NO) ? $entity->VIN_NO : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="lot_no">Lot No.</label>
			<input type="text" class="form-control" id="lot_no" name="LOT_NO" value="<?php echo isset($entity->LOT_NO) ? $entity->LOT_NO : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="product_model">Model</label>
			<input type="text" class="form-control" id="product_model" name="PRODUCT_MODEL" value="<?php echo isset($entity->PRODUCT_MODEL) ? $entity->PRODUCT_MODEL : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="model_name">Model Name</label>
			<input type="text" class="form-control" id="model_name" name="MODEL_NAME" value="<?php echo isset($entity->MODEL_NAME) ? $entity->MODEL_NAME : ''; ?>" required>
		</div>
	</div>
	
	<div class="modal-footer">
		<div class="form-group">
			<button type="button" class="btn btn-flat btn-info pull-left" data-dismiss="modal">Close</button>
			<input type="submit" value="Submit" class="btn btn-flat btn-danger">
		</div>
	</div>
	
</form><!-- End Form -->
					