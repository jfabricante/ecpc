<link href="<?php echo base_url('resources/plugins/datepicker/css/bootstrap-datepicker.min.css');?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/select/css/bootstrap-select.min.css');?>" rel="stylesheet" >
<!-- Items block -->
<section class="content vin_control">
	<!-- row -->
	<div class="row">
		<!-- col-md-6 -->
		<div class="col-md-12">
			<!-- Box danger -->
			<?php echo $this->session->flashdata('message'); ?>

			<div class="box box-danger">
				<!-- Content -->
				<?php if (in_array($this->session->userdata('user_access'), array('Administrator', 'ReadOnly'))): ?>
					<div class="box-header with-border">
						<form class="form-inline" action="<?php echo base_url('index.php/cop/handle_excel_report'); ?>" method="post">
							<div class="form-group">
								<label for="from">Date Covered From:</label>
								<input type="text" class="form-control datepicker" name="from" id="from" required>
							</div>

							<div class="form-group">
								<label for="to">To:</label>
								<input type="text" class="form-control datepicker" name="to" id="to" required />
							</div>

							<div class="form-group">
								<select name="field" id="field" class="form-control selectpicker" data-live-search="true" required>
									<option></option>
									<option value="CP_DATE">CP DATE</option>
									<option value="ETD">ETD</option>
									<option value="ETA">ETA</option>
									<option value="PAYMENT_DATE">PAYMENT DATE</option>
									<option value="TRANSMITTAL_DATE">TRANSMITTAL DATE</option>
								</select>
							</div>

							<div class="form-group">
								<input type="submit" class="btn btn-flat btn-danger" value="Create Report" />
							</div>

							<a href="<?php echo base_url('index.php/cop/form') ?>" data-toggle="modal" data-target=".bs-example-modal-sm">
								<button class="btn btn-flat btn-success pull-right">Add CP <i class="fa fw fa-plus" aria-hidden="true"></i></button>
							</a>
						</form>
					</div>
				<?php endif ?>
				<div class="box-body">
					<!-- Item table -->
					<table class="table table-condensed table-striped table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th>CP No.</th>
								<th>CP Date</th>
								<th>Invoice No.</th>
								<th>Entry No.</th>
								<th>CP Entry No.</th>
								<th>Model</th>
								<th>Lot No.</th>
								<th>Qty.</th>
								<th>ETD</th>
								<th>ETA</th>
								<th>Payment Date</th>
								<th>Transmittal Date</th>
								<?php if (in_array($this->session->userdata('user_access'), array('Administrator', 'ReadOnly'))): ?>
									<th></th>
									<th></th>
								<?php endif ?>
							</tr>
						</thead>

						<tbody>
							<?php $count = 1; ?>
							<?php foreach ($entities as $entity): ?>
								<tr>
									<td><?php echo $count; ?></td>
									<td><?php echo $entity->CP_NO; ?></td>
									<td><?php echo $entity->CP_DATE ? date('m/d/Y', strtotime($entity->CP_DATE)) : ''; ?></td>
									<td><?php echo $entity->INVOICE_NO; ?></td>
									<td><?php echo $entity->ENTRY_NO; ?></td>
									<td><?php echo $entity->PRODUCT_MODEL; ?></td>
									<td><?php echo $entity->LOT_NO; ?></td>
									<td><?php echo $entity->QTY; ?></td>
									<td><?php echo $entity->ETD ? date('m/d/Y', strtotime($entity->ETD)) : ''; ?></td>
									<td><?php echo $entity->ETA ? date('m/d/Y', strtotime($entity->ETA)) : ''; ?></td>
									<td><?php echo $entity->PAYMENT_DATE ? date('m/d/Y', strtotime($entity->PAYMENT_DATE)) : ''; ?></td>
									<td><?php echo $entity->TRANSMITTAL_DATE ? date('m/d/Y', strtotime($entity->TRANSMITTAL_DATE)) : ''; ?></td>
									<?php if (in_array($this->session->userdata('user_access'), array('Administrator', 'ReadOnly'))): ?>
										<td>
											<?php if ($entity->ID): ?>
												<a href="<?php echo base_url('index.php/cop/form/' . $entity->ID); ?>"  data-toggle="modal" data-target=".bs-example-modal-sm">
													<i class="fa fa-pencil" aria-hidden="true"></i>
												</a>
											<?php endif ?>
										</td>
										<td>
											<?php if ($entity->ID): ?>
												<a href="<?php echo base_url('index.php/cop/notice/' . $entity->ID); ?>" data-toggle="modal" data-target=".bs-example-modal-sm">
													<i class="fa fa-trash" aria-hidden="true"></i>
												</a>
											<?php endif ?>
										</td>
									<?php endif ?>
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
<script src="<?php echo base_url('resources/plugins/datepicker/js/bootstrap-datepicker.min.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/select/js/bootstrap-select.min.js');?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.table').DataTable();
		$('#from, #to').datepicker();
		$('#field').selectpicker();
	});

	// Detroy modal
	$('body').on('hidden.bs.modal', '.modal', function () {
		$(this).removeData('bs.modal');
	}); 
</script>