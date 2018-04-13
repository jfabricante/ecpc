<!-- Form block -->
<section class="content qr-form">
	<!-- Row -->
	<div class="row">
		<!-- Grid -->
		<div class="col-md-5">
			<!-- Alert message -->
			<?php echo $this->session->flashdata('message'); ?>

			<!-- Box -->
			<div class="box box-danger">
				<!-- header -->
				<div class="box-header with-border">
					<a href="<?php echo base_url('index.php/qr/index') ?>">
						<button class="btn btn-flat btn-success pull-right"><i class="fa fw fa-chevron-left" aria-hidden="true"> Back</i></button>
					</a>
				</div>
				<!-- End of header -->
				<!-- Form -->
				<form action="<?php echo 'store' ?>" method="post" enctype="multipart/form-data">
					<!-- Box body -->
					<div class="box-body">
						<div class="form-group">
							<label for="qr_code">Browse Files</label>
							<input type="file" name="qr_code[]" id="qr_code" accept=".png" multiple required>
						</div>
					</div>
					<!-- End of box body -->

					<!-- Footer -->
					<div class="box-footer">
						<div class="form-group text-right">
							<button type="submit" class="btn btn-flat btn-danger">
								Submit <i class="fa fw fa-paper-plane"></i>
							</button>
						</div>
					</div>
					<!-- End of box footer -->
				</form>
				<!-- End of form -->
			</div>
			<!-- End of Box -->
		</div>
		<!-- End of Grid -->
	</div>
	<!-- End Row -->
</section>
<!-- End form block -->