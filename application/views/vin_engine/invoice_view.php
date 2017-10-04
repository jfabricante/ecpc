<link href="<?php echo base_url('resources/plugins/select2/css/select2.min.css');?>" rel="stylesheet" >
<!-- Items block -->
<section class="content vin_engine">
	<!-- row -->
	<div class="row" id="app">
		<!-- col-md-6 -->
		<div class="col-md-12">
			<!-- Box danger -->
			<?php echo $this->session->flashdata('message'); ?>

			<div class="box box-danger">
				<!-- Content -->
				<div class="box-body">
					<form action="<?php echo base_url('index.php/vin_engine/invoice_process') ?>" method="post">
						<div class="row">

							<div class="col-md-6">
								<div class="form-group">
									<label for="invoice_no" class="col-md-4 col-form-label">Select Invoice No: </label>
									<div class="col-md-8">
										<select name="invoice_no" id="invoice_no" ref="invoice_no" class="select2 form-control">
											<option></option>
											<option v-for="entity of invoiceList">
												{{ entity.invoice_no }}
											</option>
										</select>
									</div>
								</div>
							</div>
							
							<!-- submit -->
							<div class="col-md-2">
								<button type="submit" class="btn btn-flat btn-danger">Create Report</button>
							</div>
							<!-- ./submit -->
						</div>
					</form>
				</div>

				<div class="box-body">
					<!-- Item table -->
					<table class="table table-condensed table-striped table-bordered" id="table">
						<thead>
							<tr>
								<th>Model Name</th>
								<th>Vin No.</th>
								<th>Engine No.</th>
								<th>Security No.</th>
								<th>Lot No.</th>
								<th>Product Model</th>
								<th>Invoice No.</th>
							</tr>
						</thead>

						<tbody>
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
<script src="<?php echo base_url('resources/plugins/select2/js/select2.min.js');?>"></script>
<script src="<?php echo base_url('resources/js/axios/axios.min.js') ?>"></script>
<script src="<?php echo base_url('resources/js/vue/vue.min.js') ?>"></script>
<script src="<?php echo base_url('resources/js/lodash/lodash.js') ?>"></script>
<script type="text/javascript">
	// Vue Script
	const appUrl = '<?php echo base_url('index.php') ?>';

	const app = new Vue({
		el: '#app',
		data: {
			invoiceItems: [],
			invoiceList: [],
			invoice_no: '',
		},
		created() {
			this.fetchInvoiceNo()
		},
		watch: {
			invoice_no: function() {
				this.fetchInvoiceItem()
			}
		},
		mounted() {
			var self = this

			$(this.$refs.invoice_no).on('change', function() {
				self.invoice_no = $(this).val()
			});
		},
		methods: {
			fetchInvoiceNo: function() {
				axios.get(appUrl + '/vin_engine/ajax_fetch_invoice_list')
				.then((response) => {
					this.invoiceList = response.data
					console.log(this.invoiceList)
				})
				.catch((err) => {
					console.log(err.message);
				});
			},
			fetchInvoiceItem: function() {
				axios({
					url: appUrl + '/vin_engine/ajax_fetch_invoice_items',
					method: 'post',
					data: {
						invoice_no: this.invoice_no || ''
					}
				})
				.then((response) => {
					this.invoiceItems = response.data

					if (response.data !== null)
					{
						this.drawTableContent()
					}
				})
				.catch((error) => {
					// your action on error success
					console.log(error)
				});
			},
			// Add rows using datatables method
			drawTableContent: function() {
				let $table = $('#table').DataTable();

				// Empty the row before inserting the new content
				$table.clear().draw();

				// Insert the new content
				for (let entity of this.invoiceItems)
				{
					$table.row.add([ entity.product_model, entity.vin_no, entity.engine_no, entity.security_no, entity.lot_no,
					entity.model_name, entity.invoice_no ]).draw().node();
				}
			}
		}
	})

	// JQuery Script
	$(document).ready(function() {
		$('#invoice_no').select2({})
	});

	// Detroy modal
	$('body').on('hidden.bs.modal', '.modal', function () {
		$(this).removeData('bs.modal');
	}); 
</script>