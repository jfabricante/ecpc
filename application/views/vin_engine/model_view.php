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
					<form v-on:submit.prevent="fetchModelItems" method="post">
						<div class="row">

							<div class="col-md-4">
								<div class="form-group">
									<label for="vin_model" class="col-md-4 col-form-label">Vin Model: </label>
									<div class="col-md-8">
										<select name="vin_model" id="vin_model" ref="vin_model" class="select2 form-control">
											<option></option>
											<option v-for="entity of vinModelList" v-bind:value="entity.product_model">
												{{ entity.product_model }}
											</option>
										</select>
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<label for="lot_from" class="col-md-4 col-form-label">Lot from: </label>
									<div class="col-md-7">
										<select name="lot_from" id="lot_from" ref="lot_from" class="select2 form-control">
											<option></option>
											<option v-for="entity of lot" v-bind:value="entity.lot_no">
												{{ entity.lot_no }}
											</option>
										</select>
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<label for="lot_to" class="col-md-3 col-form-label">to: </label>
									<div class="col-md-7">
										<select name="lot_to" id="lot_to" ref="lot_to" class="select2 form-control">
											<option></option>
											<option v-for="entity of lot" v-bind:value="entity.lot_no">
												{{ entity.lot_no }}
											</option>
										</select>
									</div>
								</div>
							</div>
							
							<!-- submit -->
							<div class="col-md-2">
								<button type="submit" class="btn btn-flat btn-danger">Filter Data</button>
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
			vinModelItems: [],
			vinModelList: [],
			vinSelected: '',
			lot: [],
			lot_from: '',
			lot_to: ''
		},
		created() {
			this.fetchVinModel()
		},
		watch: {
			invoice_no: function() {
				this.fetchInvoiceItem()
			},
			vinSelected: function() {
				this.fetchVinLot()
			}
		},
		mounted() {
			var self = this

			$(this.$refs.invoice_no).on('change', function() {
				self.invoice_no = $(this).val()
			});

			$(this.$refs.vin_model).on('change', function() {
				self.vinSelected = $(this).val()
			});

			$(this.$refs.lot_from).on('change', function() {
				self.lot_from = $(this).val()
				
				// Assign the initial value of lot to
				self.lot_to = $(this).val()

			});

			$(this.$refs.lot_to).on('change', function() {
				self.lot_to = $(this).val()
			});
		},
		methods: {
			fetchVinModel: function() {
				axios.get(appUrl + '/vin/ajax_model_list2')
					.then((response) => {
						this.vinModelList = response.data
						console.log(response.data)
					})
					.catch((e) => {
						console.log(e.message)
					})
			},
			fetchVinLot: function() {
				// Reset the lot
				this.lot = ''

				axios({
					url: appUrl + '/vin/ajax_vin_lot',
					method: 'post',
					data: {
						product_model: this.vinSelected || ''
					}
				})
				.then((response) => {
					this.lot = response.data
				})
				.catch((error) => {
					// your action on error success
					console.log(error)
				});
			},
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
			fetchModelItems: function() {
				axios({
					url: appUrl + '/vin_engine/ajax_fetch_model_items',
					method: 'post',
					data: {
						product_model: this.vinSelected || '',
						lot_from: this.lot_from || '',
						lot_to: this.lot_to || ''
					}
				})
				.then((response) => {
					this.vinModelItems = response.data

					console.log(this.vinModelItems)

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
				for (let entity of this.vinModelItems)
				{
					$table.row.add([ entity.product_model, entity.vin_no, entity.engine_no, entity.security_no, entity.lot_no,
					entity.model_name, entity.invoice_no ]).draw().node();
				}
			}
		}
	})

	// JQuery Script
	$(document).ready(function() {
		$('select').select2({})

	});

	// Detroy modal
	$('body').on('hidden.bs.modal', '.modal', function () {
		$(this).removeData('bs.modal');
	}); 
</script>