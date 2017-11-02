<link href="<?php echo base_url('resources/plugins/iCheck/flat/red.css') ?>" rel="stylesheet" >
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
					<a href="<?php echo base_url('index.php/vin/form') ?>" data-toggle="modal" data-target=".bs-example-modal-sm">
						<button class="btn btn-flat btn-success pull-right">Add Vin Model <i class="fa fw fa-plus" aria-hidden="true"></i></button>
					</a>
				</div>

				<div class="box-body">
					<!-- Item table -->
					<table class="table table-condensed table-striped table-bordered" id="dataTable">
						<thead>
							<tr>
								<th>#</th>
								<th>Product Model</th>
								<th>Product Year</th>
								<th>Description</th>
								<th>Lot Size</th>
								<th>QR</th>
								<th>Status</th>
								<th></th>
								<th></th>
							</tr>
						</thead>

						<tbody>
							<?php $count = 1; ?>
							<?php foreach ($entities as $entity): ?>
								<tr>
									<td><?php echo $count; ?></td>
									<td><?php echo $entity->PRODUCT_MODEL; ?></td>
									<td><?php echo $entity->PRODUCT_YEAR; ?></td>
									<td><?php echo $entity->DESCRIPTION; ?></td>
									<td><?php echo $entity->LOT_SIZE; ?></td>
									<td><?php echo $entity->QR; ?></td>
									<td>
										<input type="checkbox" name="model-state" class="model-state checkbox" <?php echo $entity->STATUS ? 'checked' : ''  ?> value="<?php echo $entity->ID ?>">
									</td>
									<td>
										<a href="<?php echo base_url('index.php/vin/form/' . $entity->ID); ?>"  data-toggle="modal" data-target=".bs-example-modal-sm">
											<i class="fa fa-pencil" aria-hidden="true"></i>
										</a>
									</td>
									<td>
										<a href="<?php echo base_url('index.php/vin/notice/' . $entity->ID); ?>" data-toggle="modal" data-target=".bs-example-modal-sm">
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
<script src="<?php echo base_url('resources/plugins/iCheck/icheck.min.js');?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		var appUrl = "<?php echo base_url(); ?>";

		// Put the event before intializing datatables that on every checkbox
		$('.model-state').on('ifChecked', function() {
			var $id = $(this).val();

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: appUrl + 'index.php/vin/ajax_update_state',
				data: {
					ID: $id,
					STATUS: 1
				},
				success: function(data) 
				{
					console.log(data);
				}
			});
		});

		$('.model-state').on('ifUnchecked', function() {
			var $id = $(this).val();

			// Remove access
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: appUrl + 'index.php/vin/ajax_update_state',
				data: {
					ID: $id,
					STATUS: 0
				},
				success: function(data) 
				{
					console.log(data);
				}
			});
		});

		// Add icheck style on every checkbox
		$('#dataTable').DataTable({
			"fnDrawCallback": function() {
				$('.model-state').iCheck({
					checkboxClass: 'icheckbox_flat-red',
				});
			}
		});

	});

	// Detroy modal
	$('body').on('hidden.bs.modal', '.modal', function () {
		$(this).removeData('bs.modal');
	}); 
</script>