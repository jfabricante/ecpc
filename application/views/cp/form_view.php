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
					<input type="number" class="form-control" id="id" name="ID" value="<?php echo isset($entity->ID) ? $entity->ID : ''; ?>">
				</div>

				<div class="form-group">
					<label for="model">Model</label>
					<input type="text" class="form-control" id="model" name="MODEL" value="<?php echo isset($entity->MODEL) ? $entity->MODEL : ''; ?>" required>
				</div>

				<div class="form-group">
					<label for="series">Series</label>
					<input type="text" class="form-control" id="series" name="SERIES" value="<?php echo isset($entity->SERIES) ? $entity->SERIES : ''; ?>" required>
				</div>

				<div class="form-group">
					<label for="engine_pref">Engine</label>
					<input type="text" class="form-control" id="engine_pref" name="ENGINE_PREF" value="<?php echo isset($entity->ENGINE_PREF) ? $entity->ENGINE_PREF : ''; ?>">
				</div>

				<div class="form-group">
					<label for="piston_displacement">Piston Displacement</label>
					<input type="text" class="form-control" id="piston_displacement" name="PISTON_DISPLACEMENT" value="<?php echo isset($entity->PISTON_DISPLACEMENT) ? $entity->PISTON_DISPLACEMENT : ''; ?>" required>
				</div>

				<div class="form-group">
					<label for="body_type">Body Type</label>
					<input type="text" class="form-control" id="body_type" name="BODY_TYPE" value="<?php echo isset($entity->BODY_TYPE) ? $entity->BODY_TYPE : ''; ?>" required>
				</div>

				<div class="form-group">
					<label for="year_model">Year Model</label>
					<input type="text" class="form-control" id="year_model" name="YEAR_MODEL" value="<?php echo isset($entity->YEAR_MODEL) ? $entity->YEAR_MODEL : ''; ?>" required>
				</div>
			</div>
			<!-- ./col-md-6 -->

			<!-- col-md-6 -->
			<div class="col-md-6">
				<div class="form-group">
					<label for="gross_weight">Gross Weight</label>
					<input type="text" class="form-control" id="gross_weight" name="GROSS_WEIGHT" value="<?php echo isset($entity->GROSS_WEIGHT) ? $entity->GROSS_WEIGHT : ''; ?>" required>
				</div>

				<div class="form-group">
					<label for="net_weight">Net Weight</label>
					<input type="text" class="form-control" id="net_weight" name="NET_WEIGHT" value="<?php echo isset($entity->NET_WEIGHT) ? $entity->NET_WEIGHT : ''; ?>" >
				</div>

				<div class="form-group">
					<label for="cylinder">Cylinder</label>
					<input type="number" class="form-control" id="cylinder" name="CYLINDER" value="<?php echo isset($entity->CYLINDER) ? $entity->CYLINDER : ''; ?>" >
				</div>

				<div class="form-group">
					<label for="fuel">Fuel</label>
					<input type="text" class="form-control" id="fuel" name="FUEL" value="<?php echo isset($entity->FUEL) ? $entity->FUEL : ''; ?>" >
				</div>

				<div class="form-group">
					<label for="stamp">Stamp</label>
					<input type="text" class="form-control" id="stamp" name="STAMP" value="<?php echo isset($entity->STAMP) ? $entity->STAMP : ''; ?>" >
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
					