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
			<input type="number" class="form-control" id="id" name="id" value="<?php echo isset($entity->id) ? $entity->id : ''; ?>">
		</div>

		<div class="form-group">
			<label for="product_model">Product Model</label>
			<input type="text" class="form-control" id="product_model" name="product_model" value="<?php echo isset($entity->product_model) ? $entity->product_model : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="product_name">Product Year</label>
			<input type="text" class="form-control" id="product_year" name="product_year" value="<?php echo isset($entity->product_year) ? $entity->product_year : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="description">Description</label>
			<input type="text" class="form-control" id="description" name="description" value="<?php echo isset($entity->description) ? $entity->description : ''; ?>">
		</div>

		<div class="form-group">
			<label for="lot_size">Lot Size</label>
			<input type="number" class="form-control" id="lot_size" name="lot_size" value="<?php echo isset($entity->lot_size) ? $entity->lot_size : ''; ?>" required>
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
					