<link href="<?php echo base_url('resources/plugins/select2/css/select2.min.css');?>" rel="stylesheet" >
<style type="text/css">
	.oracle-form-block {
		margin-top: 20px;
	}
</style>
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

							<!-- create report -->
							<div class="col-md-1">
								<button type="button" class="btn btn-flat btn-success" v-on:click="updateDetails">Create Report</button>
							</div>
							<!-- ./create report -->

						</div>

						<?php if ($this->session->userdata('user_access') ==  'Regular'): ?>
							<div class="row oracle-form-block">
								<div class="col-md-3 col-md-offset-6">
									<div class="form-group">
										<label for="lot_from" class="col-md-5 col-form-label">Oracle Lot No. </label>
										<div class="col-md-7">
											<input type="text" name="oracle_lot_no" v-model="oracle_lot_no" class="form-control">
										</div>
									</div>
								</div>

								<div class="col-md-3">
									<button type="button" class="btn btn-flat btn-info" v-on:click="updateOracleLot">Update Oracle Lot</button>
								</div>
							</div>
						<?php endif ?>

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
								<?php if (in_array($this->session->userdata('user_access'), array('Administrator', 'Regular'))): ?>
									<th>Oracle Lot No.</th>
									<th>Original Vin</th>
								<?php endif ?>
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
								<?php if (in_array($this->session->userdata('user_access'), array('Administrator', 'Regular'))): ?>
									<td>{{ oracle_lot_no ? item.ORACLE_LOT_NO = oracle_lot_no : item.ORACLE_LOT_NO }}</td>
									<td>{{ item.JAPAN_VIN }}</td>
								<?php endif ?>
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
			oracle_lot_no: ''
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
		},
		mounted() {
			var self = this

			$(this.$refs.invoice_no).on('change', function() {
				self.invoice_no = $(this).val()
			});

			$(this.$refs.vin_model).on('change', function() {
				self.vinSelected = $(this).val()
				console.log(self.vinSelected)
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
			updateOracleLot: function() {
				console.log(this.vinModelItems)

				this.showModal()
				axios({
					url: appUrl + '/vin_engine/ajax_update_oracle_lot',
					method: 'post',
					contentType: "application/xml; charset=utf-8",
					data: {
						items: this.vinModelItems || '',
					}
				})
				.then((response) => {
					$('#myModal').modal('hide')
					console.log(response)
				})
				.catch((error) => {
					// your action on error success
					console.log(error)
				});
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