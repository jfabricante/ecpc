<!-- Items block -->
<section class="content vin_engine">
	<!-- row -->
	<div class="row">
		<!-- col-md-6 -->
		<div class="col-md-12">
			<!-- Box danger -->
			<?php echo $this->session->flashdata('message'); ?>

			<div class="box box-danger">
				<!-- Content -->

				<div class="box-body">
					<!-- Item table -->
					<table class="table table-condensed table-striped table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th>Model Name</th>
								<th>Vin No.</th>
								<th>Engine No.</th>
								<th>Lot</th>
								<th>Security</th>
								<th>Color</th>
								<th>Invoice</th>
								<th>Year Model</th>
								<th>Entry</th>
								<th>Key No.</th>
								<th>CP</th>
								<th>Series</th>
							</tr>
						</thead>

						<tbody>
							<?php $count = 1; ?>
							<?php if ($entities): ?>
								<?php foreach ($entities as $entity): ?>
									<tr data-id="<?php echo $entity->ID ?>">
										<td><?php echo $count; ?></td>
										<td><?php echo $entity->PRODUCT_MODEL; ?></td>
										<td><input type="text" class="vin form-control" name="VIN_NO" value="<?php echo $entity->VIN_NO; ?>" /></td>
										<td><input type="text" class="engine form-control" name="ENGINE_NO" value="<?php echo $entity->ENGINE_NO; ?>" /></td>
										<td><?php echo $entity->LOT_NO; ?></td>
										<td><input type="text" class="security form-control" name="SECURITY_NO" value="<?php echo $entity->SECURITY_NO; ?>" /></td>
										<td><?php echo $entity->COLOR; ?></td>
										<td><?php echo $entity->INVOICE_NO; ?></td>
										<td><?php echo $entity->YEAR_MODEL; ?></td>
										<td><?php echo $entity->ENTRY_NO; ?></td>
										<td><?php echo $entity->KEY_NO; ?></td>
										<td><?php echo $entity->CP_NO; ?></td>
										<td><?php echo $entity->SERIES; ?></td>
									</tr>
									<?php $count++; ?>
								<?php endforeach; ?>
							<?php endif ?>
						</tbody>
					</table>
					<!-- End of table -->
				</div>
				<!-- End of content -->
			</div>
			<!-- End of danger -->
		</div>
		<!-- End of col-md-6 -->
	</div>
	<!-- End of row -->
</section>
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      ...
    </div>
  </div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('.table').DataTable();

		// Update Vin 
		$('.vin').on('keyup', function() {
			$self   = $(this);
			$parent = $self.parent().parent();
			$id     = $parent.attr('data-id');

			$.post('<?php echo base_url('index.php/vin_engine/update_entity') ?>', {
				ID: $id,
				VIN_NO: $self.val()
			}).done(function($data) {
				console.log($data);
			});
		});

		// Update Engine 
		$('.engine').on('keyup', function() {
			$self   = $(this);
			$parent = $self.parent().parent();
			$id     = $parent.attr('data-id');

			$.post('<?php echo base_url('index.php/vin_engine/update_entity') ?>', {
				ID: $id,
				ENGINE_NO: $self.val()
			}).done(function($data) {
				console.log($data);
			});
		});

		// Update Security 
		$('.security').on('keyup', function() {
			$self   = $(this);
			$parent = $self.parent().parent();
			$id     = $parent.attr('data-id');

			$.post('<?php echo base_url('index.php/vin_engine/update_entity') ?>', {
				ID: $id,
				SECURITY_NO: $self.val()
			}).done(function($data) {
				console.log($data);
			});
		});
	});

	// Detroy modal
	$('body').on('hidden.bs.modal', '.modal', function () {
		$(this).removeData('bs.modal');
	}); 
</script>