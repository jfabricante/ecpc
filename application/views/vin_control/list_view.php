<!-- Items block -->
<section class="content vin_control">
	<!-- row -->
	<div class="row">
		<!-- col-md-6 -->
		<div class="col-md-9">
			<!-- Box danger -->
			<?php echo $this->session->flashdata('message'); ?>

			<div class="box box-danger">
				<!-- Content -->
				<div class="box-header with-border">
					<a href="<?php echo base_url('index.php/vin_control/form') ?>" data-toggle="modal" data-target=".bs-example-modal-sm">
						<button class="btn btn-flat btn-success pull-right">Add Vin Control <i class="fa fw fa-plus" aria-hidden="true"></i></button>
					</a>
				</div>

				<div class="box-body">
					<!-- Item table -->
					<table class="table table-condensed table-striped table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th>ID</th>
								<th>Group</th>
								<th>Vin No.</th>
								<th>Lot No.</th>
								<th>Model</th>
								<th>Model Name</th>
								<th>Last User</th>
								<th>Last Update</th>
								<th></th>
								<th></th>
							</tr>
						</thead>

						<tbody>
							<?php $count = 1; ?>
							<?php foreach ($entities as $entity): ?>
								<tr>
									<td><?php echo $count; ?></td>
									<td><?php echo $entity->ID; ?></td>
									<td><?php echo $entity->CODE; ?></td>
									<td><?php echo $entity->VIN_NO; ?></td>
									<td><?php echo $entity->LOT_NO; ?></td>
									<td><?php echo $entity->PRODUCT_MODEL; ?></td>
									<td><?php echo $entity->MODEL_NAME; ?></td>
									<td><?php echo $entity->LAST_USER; ?></td>
									<td><?php echo $entity->LAST_UPDATE; ?></td>
									<td>
										<a href="<?php echo base_url('index.php/vin_control/form/' . $entity->ID); ?>"  data-toggle="modal" data-target=".bs-example-modal-sm">
											<i class="fa fa-pencil" aria-hidden="true"></i>
										</a>
									</td>
									<td>
										<a href="<?php echo base_url('index.php/vin_control/notice/' . $entity->ID); ?>" data-toggle="modal" data-target=".bs-example-modal-sm">
											<i class="fa fa-trash" aria-hidden="true"></i>
										</a>
									</td>
								</tr>
								<?php $count++; ?>
							<?php endforeach; ?>
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
	});

	// Detroy modal
	$('body').on('hidden.bs.modal', '.modal', function () {
		$(this).removeData('bs.modal');
	}); 
</script>