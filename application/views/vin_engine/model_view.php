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
				<div class="box-header">
					<form v-on:submit.prevent="fetchModelItems" method="post">
						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<label for="vin_model" class="col-md-4 col-form-label">Vin Model: </label>
									<div class="col-md-8">
										<select name="vin_model" id="vin_model" ref="vin_model" class="select2 form-control">
											<option></option>
											<option v-for="entity of vinModelList" v-bind:value="entity.PRODUCT_MODEL">
												{{ entity.PRODUCT_MODEL }}
											</option>
										</select>
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<label for="lot_from" class="col-md-4 col-form-label">Lot from: </label>
									<div class="col-md-8">
										<select name="lot_from" id="lot_from" ref="lot_from" class="select2 form-control">
											<option></option>
											<option v-for="entity of lot" v-bind:value="entity.LOT_NO">
												{{ entity.LOT_NO }}
											</option>
										</select>
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<label for="lot_to" class="col-md-3 col-form-label">to: </label>
									<div class="col-md-9">
										<select name="lot_to" id="lot_to" ref="lot_to" class="select2 form-control">
											<option></option>
											<option v-for="entity of lot" v-bind:value="entity.LOT_NO">
												{{ entity.LOT_NO }}
											</option>
										</select>
									</div>
								</div>
							</div>
							
							<!-- submit -->
							<div class="col-md-1">
								<button type="submit" class="btn btn-flat btn-danger">Filter Data</button>
							</div>
							<!-- ./submit -->
							<?php if ($this->session->userdata('user_access') == 'Administrator'): ?>
								<!-- Set security number -->
								<!-- <div class="col-md-1">
									<button type="button" class="btn btn-flat btn-info" v-on:click="fetchLastNumber">Set Security</button>
								</div> -->
								<!-- ./Set security number -->
							<?php endif ?>

							<!-- Set security number -->
							<div class="col-md-1">
								<button type="button" class="btn btn-flat btn-success" v-on:click="updateDetails">Create Report</button>
							</div>
							<!-- ./Set security number -->

						</div>
					</form>
				</div>

				<div class="box-body">
					<!-- Item table -->
					<table class="table table-condensed table-striped table-bordered" id="table" ref="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Seq.</th>
								<th>Model Name</th>
								<th>Vin No.</th>
								<th>Engine No.</th>
								<?php if ($this->session->userdata('user_access') == 'Administrator'): ?>
									<th>Security No.</th>
								<?php endif ?>
								<th>Lot No.</th>
								<th>Color</th>
								<th>Invoice No.</th>
							</tr>
						</thead>

						<tbody>
							<tr v-for="(item, index) in vinModelItems">
								<td>{{ index + 1 }}</td>
								<td>{{ item.SEQUENCE }}</td>
								<td>{{ item.PRODUCT_MODEL }}</td>
								<?php if ($this->session->userdata('user_access') == 'Administrator'): ?>
									<td><input type="text" v-model="item.VIN_NO" class="form-control"/></td>
								<?php else: ?>
									<td>{{ item.VIN_NO }}</td>
								<?php endif ?>
								<?php if ($this->session->userdata('user_access') == 'Administrator'): ?>
									<td><input type="text" v-model="item.ENGINE_NO" class="form-control"/></td>
								<?php else: ?>
									<td>{{ item.ENGINE_NO }}</td>
								<?php endif ?>
								<?php if ($this->session->userdata('user_access') == 'Administrator'): ?>
									<td><input type="text" v-model="item.SECURITY_NO" class="form-control"/></td>
								<?php endif ?>
								<td>{{ item.LOT_NO }}</td>
								<td>{{ item.COLOR }}</td>
								<td>{{ item.INVOICE_NO }}</td>
							</tr>
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
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="myModal">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-body">
				
			</div>
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
	const tmUrl  = '<?php echo base_url('resources/images/') ?>';

	const app = new Vue({
		el: '#app',
		data: {
			vinModelItems: [],
			vinModelList: [],
			vinSelected: '',
			lot: [],
			lot_from: '',
			lot_to: '',
			security: {},
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
			},
			security: function() {
				this.updateItems()
			}
		},
		mounted() {
			var self = this

			$(this.$refs.invoice_no).on('change', function() {
				self.invoice_no = $(this).val()
			});

			$(this.$refs.vin_model).on('change', function() {
				self.vinSelected = $(this).val()
				console.log(self.vinSelected)
				self.security = {}
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
				axios.get(appUrl + '/vin_engine/ajax_distinct_model')
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
					url: appUrl + '/vin_engine/ajax_distinct_lot',
					method: 'post',
					data: {
						PRODUCT_MODEL: this.vinSelected || ''
					}
				})
				.then((response) => {
					console.log(response.data)
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
			fetchLastNumber: function() {
				axios.get(appUrl + '/security/get_last_number')
				.then((response) => {
					this.security = response.data
					console.log(this.security)
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
						// this.drawTableContent()
					}
				})
				.catch((error) => {
					// your action on error success
					console.log(error)
				});
			},
			updateDetails: function() {
				this.showModal()
				axios({
					url: appUrl + '/vin_engine/ajax_update_details',
					method: 'post',
					contentType: "application/xml; charset=utf-8",
					data: {
						items: this.vinModelItems || '',
						security: this.security || '',
					}
				})
				.then((response) => {
					$('#myModal').modal('hide')
					window.open(appUrl + '/vin_engine/download_masterlist')
					window.open(appUrl + '/vin_engine/download_pdf')
				})
				.catch((error) => {
					// your action on error success
					console.log(error)
				});
			},
			updateItems: function()
			{
				if (this.vinModelItems.length > 0)
				{
					for (entity of this.vinModelItems)
					{
						this.securitySequence()
						entity.SECURITY_NO = this.security.SECURITY_NO
					}

				}
			},
			securitySequence: function()
			{
				// Array of restriction
				const excep = ['0000', '1111', '2222', '3333', '4444', '5555', '6666', '7777', '8888', '9999']

				this.security.SECURITY_NO = Number(this.security.SECURITY_NO) + 1

				// Increment by 1 if the value is in excep array
				if (_.includes(excep, this.security.SECURITY_NO.toString()))
				{
					this.security.SECURITY_NO = Number(this.security.SECURITY_NO) + 1
				}
			},
			// Add rows using datatables method
			drawTableContent: function() {
				let $table = $('#table').DataTable();

				// Empty the row before inserting the new content
				$table.clear().draw();

				// Insert the new content
				let i = 1;
				for (let [index, entity] of this.vinModelItems.entries())
				{
					<?php if($this->session->userdata('user_access') == 'Administrator'): ?>
						$table.row.add([i, entity.SEQUENCE, entity.PRODUCT_MODEL, '<input type="text" class="form-control" v-model="vinModelItems[index].VIN_NO">', entity.ENGINE_NO, entity.SECURITY_NO, entity.LOT_NO,
					<?php else: ?>
						$table.row.add([i, entity.SEQUENCE, entity.PRODUCT_MODEL, entity.VIN_NO, entity.ENGINE_NO, entity.LOT_NO,
					<?php endif ?>
					
					entity.COLOR, entity.INVOICE_NO ]).draw().node();
					i++
				}
			},
			showModal: function() {
				$("#myModal").modal({backdrop: 'static', keyboard: false})

				$('#myModal').on('shown.bs.modal', function() {
					$(this).find('.modal-body').html('<img src="' + tmUrl + 'loading.gif" class="img-responsive"/>')
				});
			},
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