<!-- Items block -->
<section class="content cp">
	<!-- row -->
	<div class="row">
		<!-- col-md-6 -->
		<div class="col-md-12">
			<!-- Box danger -->
			<?php echo $this->session->flashdata('message'); ?>

			<div class="box box-danger">
				<!-- Content -->
				<div class="box-header with-border">
					<a href="<?php echo base_url('index.php/cp/form') ?>" data-toggle="modal" data-target=".bs-example-modal-sm">
						<button class="btn btn-flat btn-success pull-right">Add CP Details <i class="fa fw fa-plus" aria-hidden="true"></i></button>
					</a>
				</div>

				<div class="box-body">
					<!-- Item table -->
					<table class="table table-condensed table-striped table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th>Model</th>
								<th>Series</th>
								<th>Engine</th>
								<th>Piston</th>
								<th>Body Type</th>
								<th>Year Model</th>
								<th>Gross Weight</th>
								<th>Net Weight</th>
								<th>Cylinder</th>
								<th>Fuel</th>
								<th>Stamp</th>
								<th></th>
								<th></th>
							</tr>
						</thead>

						<tbody>
							<?php $count = 1; ?>
							<?php foreach ($entities as $entity): ?>
								<tr>
									<td><?php echo $count; ?></td>
									<td><?php echo $entity->MODEL; ?></td>
									<td><?php echo $entity->SERIES; ?></td>
									<td><?php echo $entity->ENGINE_PREF; ?></td>
									<td><?php echo $entity->PISTON_DISPLACEMENT; ?></td>
									<td><?php echo $entity->BODY_TYPE; ?></td>
									<td><?php echo $entity->YEAR_MODEL; ?></td>
									<td><?php echo $entity->GROSS_WEIGHT; ?></td>
									<td><?php echo $entity->NET_WEIGHT; ?></td>
									<td><?php echo $entity->CYLINDER; ?></td>
									<td><?php echo $entity->FUEL; ?></td>
									<td><?php echo $entity->STAMP; ?></td>
									<td>
										<a href="<?php echo base_url('index.php/cp/form/' . $entity->ID); ?>"  data-toggle="modal" data-target=".bs-example-modal-sm">
											<i class="fa fa-pencil" aria-hidden="true"></i>
										</a>
									</td>
									<td>
										<a href="<?php echo base_url('index.php/cp/notice/' . $entity->ID); ?>" data-toggle="modal" data-target=".bs-example-modal-sm">
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
  <div class="modal-dialog modal-md" role="document">
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