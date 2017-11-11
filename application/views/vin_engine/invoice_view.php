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

					<!-- form -->
					<form action="<?php echo base_url('index.php/vin_engine/invoice_process') ?>" method="post">
						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<label for="invoice_no">Select Invoice No: </label>
									
									<div>
										<select name="INVOICE_NO" id="invoice_no" ref="invoice_no" class="select2 form-control">
											<option></option>
											<option v-for="entity of invoiceList">
												{{ entity.INVOICE_NO }}
											</option>
										</select>
									</div>
								</div>
							</div>
							
							<!-- Generate ECPC -->
							<div class="col-md-2">
								<br />
								<button type="submit" class="btn btn-flat btn-danger" name="ecpc" value="1">Create Report</button>
							</div>
							<!-- ./Generate ECPC -->

							<!-- Generate ECPC -->
							<div class="col-md-2">
								<br />
								<button type="submit" class="btn btn-flat btn-info" name="summary" value="1">Summary Report</button>
							</div>
							<!-- ./Generate ECPC -->

							<?php if ($this->session->userdata('user_access') == 'Administrator'): ?>
								<!-- file-upload -->
								<div class="col-md-2">
									<div class="form-group">
										<label for="file-upload">Upload Spreadsheet</label>
										<input type="file" name="file-upload" v-model="fileUpload" ref="fileUpload" id="file-upload" accept=".xlsx, .xls, .csv">
									</div>
								</div>
								<!-- ./file-upload -->
							
								<!-- submit -->
								<div class="col-md-2">
									<br />
									<button type="button" class="btn btn-flat btn-danger" v-on:click="updateSecurityCode">Update Security Code</button>
								</div>
								<!-- ./submit -->
							<?php endif ?>
						</div>
					</form>
					<!-- ./form -->

				</div>

				<div class="box-body">
					<!-- Item table -->
					<table class="table table-condensed table-striped table-bordered" id="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Seq.</th>
								<th>Model Name</th>
								<th>Vin No.</th>
								<th>Engine No.</th>
								<th>Security No.</th>
								<th>Lot No.</th>
								<th>Color</th>
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
<script src="<?php echo base_url('resources/js/js-xlsx/cpexcel.js') ?>"></script>
<script src="<?php echo base_url('resources/js/js-xlsx/xlsx.js') ?>"></script>
<script src="<?php echo base_url('resources/js/js-xlsx/jszip.js') ?>"></script>
<script src="<?php echo base_url('resources/js/js-xlsx/xlsx.full.min.js') ?>"></script>
<script type="text/javascript">
	// Vue Script
	var appUrl = '<?php echo base_url('index.php') ?>';
	var tmUrl  = '<?php echo base_url('resources/images/') ?>';

	var app = new Vue({
		el: '#app',
		data: {
			invoiceItems: [],
			invoiceList: [],
			invoice_no: '',
			fileUpload: '',
			excelObject: []
		},
		created() {
			this.fetchInvoiceNo()
		},
		watch: {
			invoice_no: function() {
				this.fetchInvoiceItem()
				console.log(this.invoice_no)
			},
			excelObject: function() {
				this.findSecurityCode()

				this.drawTableContent()
			}
		},
		mounted() {
			var self = this

			$(this.$refs.invoice_no).on('change', function() {
				self.invoice_no = $(this).val()
				self.showModal()
			});

			$(this.$refs.fileUpload).on('change', this.filePicked)
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
						INVOICE_NO: this.invoice_no || ''
					}
				})
				.then((response) => {
					this.invoiceItems = response.data

					$('#myModal').modal('hide')
					console.log(response.data)
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
				var $table = $('#table').DataTable();

				// Empty the row before inserting the new content
				$table.clear().draw();

				var i = 1;

				// Insert the new content
				for (var entity of this.invoiceItems)
				{
					$table.row.add([i, entity.SEQUENCE,  entity.PRODUCT_MODEL, entity.VIN_NO, entity.ENGINE_NO, entity.SECURITY_NO, entity.LOT_NO,
					entity.COLOR, entity.INVOICE_NO ]).draw().node();

					i++
				}
			},
			filePicked: function(oEvent) {
				// Get The File From The Input
				var oFile = oEvent.target.files[0];

				// Create A File Reader HTML5
				var reader = new FileReader();

				// Ready The Event For When A File Gets Selected
				reader.onload = (e) => {
					var data = e.target.result;
					var wb = XLSX.read(data, {type: 'binary'});

					// Assume that the first sheet has its value
					var sheetName = wb.SheetNames[0]

					// Reset the element of excel object
					this.excelObject.splice(0, this.excelObject.length)

					// Assign the json values to excelObject
					this.excelObject.push(XLSX.utils.sheet_to_json(wb.Sheets[sheetName]))

					// Convert it to linear form
					this.excelObject = _.flatten(this.excelObject)

					console.log(this.excelObject)
				};

				// Tell JS To Start Reading The File.. You could delay this if desired
				reader.readAsBinaryString(oFile);
			},
			findSecurityCode: function() {
				if (this.invoiceItems.length > 0)
				{
					for (entity of this.invoiceItems)
					{
						for (excel of this.excelObject)
						{
							if (entity.VIN_NO == excel.VIN)
							{
								entity.SECURITY_NO = excel['IMMOBILIZER CODE']
							}
						}
					}
				}
			},
			updateSecurityCode: function () {
				this.showModal()

				axios({
					url: appUrl + '/vin_engine/update_cbu_security',
					method: 'post',
					data: {
						items: this.invoiceItems,
					}
				})
				.then((response) => {
					// Close the modal
					$('#myModal').modal('hide')

					console.log(response.data)
					/*if (typeof response.data == 'string')
					{
						window.open(appUrl + '/vin_engine/download')
					}
					else
					{
						let objectValues = _.values(response.data)

						alert('Values existed on the resouce ' + objectValues.join(', '))
					}*/
					
				})
				.catch((error) => {
					$('#myModal').modal('hide')
					alert('There was no data to process.')
					// your action on error success
					console.log(error)
				});
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
		$('#invoice_no').select2({})
	});

	// Detroy modal
	$('body').on('hidden.bs.modal', '.modal', function () {
		$(this).removeData('bs.modal');
	}); 
</script>