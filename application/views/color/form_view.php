<link href="<?php echo base_url('resources/plugins/select/css/bootstrap-select.min.css');?>" rel="stylesheet" >
<form action="<?php echo base_url('index.php/color/store'); ?>" method="post">
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
			<label for="code">Color Code</label>
			<input type="text" class="form-control" id="code" name="CODE" value="<?php echo isset($entity->CODE) ? $entity->CODE : ''; ?>" required>
		</div>

		<div class="form-group">
			<label for="name">Color Name</label>
			<input type="text" class="form-control" id="name" name="NAME" value="<?php echo isset($entity->NAME) ? $entity->NAME : ''; ?>" required>
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
					