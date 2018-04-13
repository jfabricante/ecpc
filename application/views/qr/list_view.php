<style type="text/css">
	.thumbnail {
		width: 100px;
		height: 100px;
	}

	.table td {
		vertical-align: middle !important;
	}
</style>
<!-- QR block -->
<section class="content qr">
	<!-- row -->
	<div class="row">
		<!-- Grid -->
		<div class="col-md-5">
			<!-- Alert message -->
			<?php echo $this->session->flashdata('message'); ?>
			
			<!-- Box danger -->
			<div class="box box-danger">
				<div class="box-header with-border">
					<a href="<?php echo base_url('index.php/qr/form') ?>">
						<button class="btn btn-flat btn-success pull-right">Add QR Code <i class="fa fw fa-plus" aria-hidden="true"></i></button>
					</a>
				</div>

				<div class="box-body">
					<table class="table table-condensed table-striped table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th>Thumbnail</th>
								<th>Filename</th>
							</tr>
						</thead>

						<tbody>
							<?php $count = 1; ?>

							<?php foreach($images as $image): ?>
								<tr>
									<td><?php echo $count ?></td>
									<td>
										<img src="<?php echo base_url('resources/images/qr/' . $image) ?>" class="thumbnail">
									</td>
									<td><?php echo $image ?></td>
								</tr>
								<?php $count++ ?>
							<?php endforeach ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript">
	const QRCode = function() {

		const init = function() {
			const $table = $('.table');

			$table.DataTable();
		}

		return {
			getInstance: function() {
				return init();
			}
		}
	}();

	(function() {
		$(document).ready(function() {
			QRCode.getInstance();
		});
	})();
</script>