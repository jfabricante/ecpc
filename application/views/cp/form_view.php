<form action="<?php echo base_url('index.php/cp/store'); ?>" method="post">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>
		<h4 class="modal-title"><?php echo $title; ?></h4>
	</div>

	<!-- modal body -->
	<div class="modal-body">
		<!-- row -->
		<div class="row">
			<!-- col-md-6 -->
			<div class="col-md-6">
				<div class="form-group hidden">
					<input type="number" class="form-control" id="id" name="id" value="<?php echo isset($entity->id) ? $entity->id : ''; ?>">
				</div>

				<div class="form-group">
					<label for="model">Model</label>
					<input type="text" class="form-control" id="model" name="model" value="<?php echo isset($entity->model) ? $entity->model : ''; ?>" required>
				</div>

				<div class="form-group">
					<label for="series">Series</label>
					<input type="text" class="form-control" id="series" name="series" value="<?php echo isset($entity->series) ? $entity->series : ''; ?>" required>
				</div>

				<div class="form-group">
					<label for="engine_pref">Engine</label>
					<input type="text" class="form-control" id="engine_pref" name="engine_pref" value="<?php echo isset($entity->engine_pref) ? $entity->engine_pref : ''; ?>">
				</div>

				<div class="form-group">
					<label for="piston_displacement">Piston Displacement</label>
					<input type="text" class="form-control" id="piston_displacement" name="piston_displacement" value="<?php echo isset($entity->piston_displacement) ? $entity->piston_displacement : ''; ?>" required>
				</div>

				<div class="form-group">
					<label for="body_type">Body Type</label>
					<input type="text" class="form-control" id="body_type" name="body_type" value="<?php echo isset($entity->body_type) ? $entity->body_type : ''; ?>" required>
				</div>

				<div class="form-group">
					<label for="year_model">Year Model</label>
					<input type="text" class="form-control" id="year_model" name="year_model" value="<?php echo isset($entity->year_model) ? $entity->year_model : ''; ?>" required>
				</div>
			</div>
			<!-- ./col-md-6 -->

			<!-- col-md-6 -->
			<div class="col-md-6">
				<div class="form-group">
					<label for="gross_weight">Gross Weight</label>
					<input type="text" class="form-control" id="gross_weight" name="gross_weight" value="<?php echo isset($entity->gross_weight) ? $entity->gross_weight : ''; ?>" required>
				</div>

				<div class="form-group">
					<label for="net_weight">Net Weight</label>
					<input type="text" class="form-control" id="net_weight" name="net_weight" value="<?php echo isset($entity->net_weight) ? $entity->net_weight : ''; ?>" >
				</div>

				<div class="form-group">
					<label for="cylinder">Cylinder</label>
					<input type="number" class="form-control" id="cylinder" name="cylinder" value="<?php echo isset($entity->cylinder) ? $entity->cylinder : ''; ?>" >
				</div>

				<div class="form-group">
					<label for="fuel">Fuel</label>
					<input type="text" class="form-control" id="fuel" name="fuel" value="<?php echo isset($entity->fuel) ? $entity->fuel : ''; ?>" >
				</div>

				<div class="form-group">
					<label for="stamp">Stamp</label>
					<input type="text" class="form-control" id="stamp" name="stamp" value="<?php echo isset($entity->stamp) ? $entity->stamp : ''; ?>" >
				</div>
			</div>
			<!-- ./col-md-6 -->
		</div>
		<!-- ./row -->
	</div>
	<!-- ./modal-body -->
	
	<div class="modal-footer">
		<div class="form-group">
			<button type="button" class="btn btn-flat btn-info pull-left" data-dismiss="modal">Close</button>
			<input type="submit" value="Submit" class="btn btn-flat btn-danger">
		</div>
	</div>
	
</form><!-- End Form -->
					