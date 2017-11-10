<link href="<?php echo base_url('resources/plugins/select2/css/select2.css');?>" rel="stylesheet" >
<!-- Items block -->
<section class="content vin_engine">
	<!-- row -->
	<div class="row" id="app">
		<!-- col-md-6 -->
		<div class="col-md-12">
			<!-- Box danger -->
			<?php echo $this->session->flashdata('message'); ?>

			<div class="box box-danger">
				<!-- box-header -->
				<div class="box-header">
					<form method="post" v-on:submit.prevent="storeResource" enctype="multipart/form-data" role="form" id="form">
						<!-- row -->
						<div class="row">
							
							<!-- model picker -->
							<div class="col-md-2">
								<div class="form-group" id="model">
									<label for="vin_model">Model Name</label>
									<select name="vin_model" id="vin_model" ref="vin_model" class="select2 form-control" >
										<option></option>
										<option v-for="option in vinModel" v-bind:value="option.PRODUCT_MODEL">
											{{ option.PRODUCT_MODEL }}
										</option>
									</select>
								</div>
							</div>
							<!-- ./model-picker -->

							<!-- file-upload -->
							<div class="col-md-2">
								<div class="form-group">
									<label for="file-upload">Upload Spreadsheet</label>
									<input type="file" name="file-upload" v-model="fileUpload" ref="fileUpload" id="file-upload" accept=".xlsx, .xls, .csv">
								</div>
							</div>
							<!-- ./file-upload -->

							<!-- portcode picker -->
							<div class="col-md-2">
								<div class="form-group">
									<label for="portcode">Portcode</label>
									<select name="portcode" id="portcode" ref="portcode" class="select2 form-control" >
										<option></option>
										<option v-for="option in portcode" v-bind:value="option.SHORT_CODE">
											{{ option.SHORT_CODE }} - {{ option.DESCRIPTION }}
										</option>
									</select>
								</div>
							</div>
							<!-- ./portcode-picker -->

							<!-- serial picker -->
							<div class="col-md-2">
								<div class="form-group">
									<label for="serial">Serial</label>
									<select name="serial" id="serial" ref="serial" class="select2 form-control" >
										<option></option>
										<option v-for="option in serial" v-bind:value="option.SHORT_CODE">
											{{ option.SHORT_CODE }} - {{ option.DESCRIPTION }}
										</option>
									</select>
								</div>
							</div>
							<!-- ./serial-picker -->

							<div class="col-md-2">
								<div class="form-group">
									<label> </label>
									<input type="submit" name="process" value="Process" ref="process" class="btn btn-flat btn-danger form-control">
								</div>
							</div>
						</div>
						<!-- ./row -->

						<!-- row -->
						<div class="row">
							<!-- last model -->
							<div class="col-md-2">
								<div class="form-group">
									<label>Last Model</label>
									<p>{{ lastModel }}</p>
								</div>
							</div>
							<!-- ./last vin -->

							<!-- last vin -->
							<div class="col-md-2">
								<div class="form-group">
									<label>Last Vin</label>
									<p>{{ lastVin }}</p>
								</div>
							</div>
							<!-- ./last vin -->

							<!-- last lot -->
							<div class="col-md-2">
								<div class="form-group">
									<label>Last Lot</label>
									<p>{{ lastLot }}</p>
								</div>
							</div>
							<!-- ./last lot -->

							<!-- last lot -->
							<div class="col-md-2">
								<div class="form-group">
									<label for="entry_no">Entry No.</label>
									<input type="text" name="entry_no" class="form-control" id="entry_no" v-model="entryNo">
								</div>
							</div>
							<!-- ./last lot -->

							<div class="col-md-2">
								<label for=""></label>
								<button type="button" class="btn btn-flat btn-block btn-info" v-on:click="fetchLastNumber">Set Security</button>
							</div>
						</div>
						<!-- ./row -->
					</form>
				</div>
				<!-- ./box-header -->

				<!-- box-body -->
				<div class="box-body">
					<!-- Item table -->
					<table class="table table-condensed table-striped table-bordered">
						<thead>
							<tr>
								<th>Sequence</th>
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
							<tr v-for="(item, index) in items">
								<td>{{ item.SEQUENCE }}</td>
								<td>{{ item.PRODUCT_MODEL }}</td>
								<td>{{ item.VIN_NO }}</td>
								<td>{{ item.ENGINE_NO }}</td>
								<td>{{ item.SECURITY_NO }}</td>
								<td>{{ item.LOT_NO }}</td>
								<td>{{ item.COLOR }}</td>
								<td>{{ item.INVOICE_NO }}</td>
							</tr>
						</tbody>
					</table>
					<!-- End of table -->
				</div>
				<!-- End of box-body -->
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
<script src="<?php echo base_url('resources/plugins/select2/js/select2.js');?>"></script>
<script src="<?php echo base_url('resources/js/axios/axios.min.js') ?>"></script>
<script src="<?php echo base_url('resources/js/vue/vue.min.js') ?>"></script>
<script src="<?php echo base_url('resources/js/lodash/lodash.js') ?>"></script>
<script src="<?php echo base_url('resources/js/js-xlsx/cpexcel.js') ?>"></script>
<script src="<?php echo base_url('resources/js/js-xlsx/xlsx.js') ?>"></script>
<script src="<?php echo base_url('resources/js/js-xlsx/jszip.js') ?>"></script>
<script src="<?php echo base_url('resources/js/js-xlsx/xlsx.full.min.js') ?>"></script>

<script type="text/javascript">
	var appUrl = '<?php echo base_url('index.php') ?>';
	var tmUrl  = '<?php echo base_url('resources/images/') ?>';

	var app = new Vue({
		el: '#app',
		data: {
			vinModel: [],
			selected: '',
			items: [],
			vinControl: '',
			calculatedLength: 0,
			vinPref: '',
			vinSuff: '',
			lastVin: '',
			lastLot: '',
			lastModel: '',
			separator: 0,
			fileUpload: '',
			excelObject: [],
			portcode: [],
			serial: [],
			classification: '003',
			entryNo: '',
			modelLot: '',
			security: {},
		},            
		created() {
			this.fetchVinModel()
			this.fetchPortcode()
			this.fetchSerial()
		},
		watch: {
			selected: function() {
				this.fetchVinControlEntity()
			},
			excelObject: function() {
				this.assignValues()
			},
			security: function() {
				this.updateItems()
			}
		},
		mounted() {
			var self = this

			$(this.$refs.vin_model).on('change', function() {
				self.selected = self.getSelectedModel($(this).val())
				//console.log(self.selected)
			})

			$(this.$refs.fileUpload).on('change', this.filePicked)

			$(this.$refs.portcode).on('change', function() {
				self.$set(self.portcode, 'selected', $(this).val())
			})

			$(this.$refs.serial).on('change', function() {
				self.$set(self.serial, 'selected', $(this).val())
			})

			$(this.$refs.process).on('click', this.showModal)
		},
		methods: {
			fetchVinModel: function() {
				axios.get(appUrl + '/vin/ajax_model_list')
				.then((response) => {
					this.vinModel = response.data
					console.log(this.vinModel)
				})
				.catch((err) => {
					console.log(err.message);
				});
			},
			fetchPortcode: function() {
				axios.get(appUrl + '/portcode/ajax_portcode_list')
				.then((response) => {
					this.portcode = response.data
				})
				.catch((err) => {
					console.log(err.message);
				});
			},
			fetchSerial: function() {
				axios.get(appUrl + '/serial/ajax_serial_list')
				.then((response) => {
					this.serial = response.data
				})
				.catch((err) => {
					console.log(err.message);
				});
			},
			showModal: function() {
				$("#myModal").modal({backdrop: 'static', keyboard: false})

				$('#myModal').on('shown.bs.modal', function() {
					$(this).find('.modal-body').html('<img src="' + tmUrl + 'loading.gif" class="img-responsive"/>')
				});
			},
			getSelectedModel: function(searchItem)
			{
				for (let [index, value] of this.vinModel.entries())
				{
					if (searchItem == value.PRODUCT_MODEL)
					{
						return value
					}
				}

				return false
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
			updateItems: function()
			{
				if (this.items.length > 0)
				{
					for (let entity of this.items)
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
			populateItems: function()
			{
				if (this.selected instanceof Object)
				{
					// console.log(this.selected)
					// Clear items before populate
					this.clearItems()

					for (var i = 1; i <= this.selected.LOT_SIZE; i++)
					{
						this.items.push(this.formatData(i))
					}
				}
			},
			formatData: function(count)
			{
				// Increment the value
				this.vinSuff = Number(this.vinSuff) + 1

				// Pad to get the exact number of character lost
				this.vinSuff = _.padStart(this.vinSuff, this.calculatedLength, 0)

				// Concatenate the prefix and suffix
				this.vinControl.VIN_NO = this.vinPref + this.vinSuff

				var formattedData = {
						SEQUENCE: count,
						PRODUCT_MODEL: this.vinControl.PRODUCT_MODEL ? this.vinControl.PRODUCT_MODEL : this.selected.PRODUCT_MODEL,
						VIN_NO: this.vinControl.VIN_NO || '',
						ENGINE_NO: this.selected.ENGINE_PREF || '',
						SECURITY_NO: '',
						LOT_NO: Number(this.vinControl.LOT_NO) + 1 || '',
						MODEL_NAME: this.vinControl.MODEL_NAME || '',
						INVOICE_NO: '',
						COLOR: 'NA'
					}

				return formattedData
			},
			clearItems: function()
			{
				this.items.splice(0, this.items.length)
			},
			fetchVinControlEntity: function()
			{
				axios({
					url: appUrl + '/vin_control/ajax_vin_control_entity',
					method: 'post',
					data: {
						PRODUCT_MODEL: this.selected.PRODUCT_MODEL,
					}
				})
				.then((response) => {
					this.vinControl = response.data
					console.log(response.data)

					if (response.data !== null)
					{
						this.separateVin()
						this.populateItems()
					}
					else
					{
						this.clearItems()
						this.calculatedLength = 0
						this.separator = 0
						this.vinPref = ''
						this.vinSuff = ''
					}
				})
				.catch((error) => {
					// your action on error success
					console.log(error)
				});
			},
			// Get the occurence of number from right to left
			occurenceOfNumber: function(params)
			{
				var count = 0

				for(var i = params.length -1; i > 0; i--)
				{
					if (isNaN(params.charAt(i)))
					{
						break
					}
					count++
				}

				return count
			},
			// Separate vin into two parts as prefix and suffix
			separateVin: function()
			{
				// Assign the last vin and last lot
				this.modelLot  = this.vinControl.LOT_NO
				this.lastVin   = this.vinControl.VIN_NO
				this.lastLot   = this.vinControl.LAST_LOT
				this.lastModel = this.vinControl.LAST_MODEL

				// Assign the point of reference
				this.calculatedLength = this.occurenceOfNumber(this.vinControl.VIN_NO)
				this.separator = this.vinControl.VIN_NO.length - this.calculatedLength

				// Set the prefix from start to separator
				this.vinPref = this.vinControl.VIN_NO.substr(0, this.separator)

				// Set the vin suffix from separator until the end of the string
				this.vinSuff = this.vinControl.VIN_NO.substr(this.separator)
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
					//var sheetName = wb.SheetNames[0]

					// Set the initial value of sheet
					var sheetName = ''

					// Look for possible sheet in a smarter way
					for (let model of wb.SheetNames)
					{
						// Implement a strict cheking of excel sheet
						if (this.selected.PRODUCT_MODEL + '-' + (Number(this.modelLot) + 1) === model)
						{
							sheetName = model
							break
						}
					}
					console.log(sheetName)
					if (sheetName === '')
					{
						alert('Cannot find sheet that match on the model.')
						console.log(this.selected.PRODUCT_MODEL)
					}
					else
					{
						// Reset the element of excel object
						this.excelObject.splice(0, this.excelObject.length)

						// Assign the json values to excelObject
						this.excelObject.push(XLSX.utils.sheet_to_json(wb.Sheets[sheetName]))

						// Convert it to linear form
						this.excelObject = _.flatten(this.excelObject)	
					}
				};

				// Tell JS To Start Reading The File.. You could delay this if desired
				reader.readAsBinaryString(oFile);
			},
			assignValues: function()
			{
				// Count the occurence of engine number
				let result = _.countBy(this.excelObject, 'Engine No.')

				// Get the key
				let keys = _.keys(result)

				// Verify if has duplicate
				let hasDuplicate = keys.map((key) => { return result[key] > 1 })

				if (_.includes(hasDuplicate, true))
				{
					alert('Engine No. has duplicate.')
				}
				else
				{
					console.log(this.selected)
					if (this.selected.LOT_SIZE == this.excelObject.length)
					{
						for(let [index, excel] of this.excelObject.entries())
						{
							this.items[index].ENGINE_NO += excel["Engine No."] + (this.selected.STAMP || '')
							this.items[index].INVOICE_NO = excel["Invoice No."]
						}
					}
					else
					{
						alert('Lot Size and Excel Sheet content does not match in terms of items.')
					}
				}
			},
			storeResource: function()
			{
				axios({
					url: appUrl + '/vin_engine/store_ckd_resource',
					method: 'post',
					data: {
						items: this.items,
						selected_model: this.selected,
						PORTCODE: this.portcode.selected,
						SERIAL: this.serial.selected,
						CLASSIFICATION: this.classification,
						vin_control: this.vinControl,
						ENTRY_NO: this.entryNo,
					}
				})
				.then((response) => {
					// Close the modal
					$('#myModal').modal('hide')

					if (typeof response.data == 'string')
					{
						window.open(appUrl + '/vin_engine/download')
					}
					else
					{
						let objectValues = _.values(response.data)

						alert('Values existed on the resouce ' + objectValues.join(', '))
					}
					
				})
				.catch((error) => {
					$('#myModal').modal('hide')
					alert('There was no data to process.')
					// your action on error success
					console.log(error)
				});
			},
		},
	});

	$(document).ready(function() {
		$('select').select2()
	});

	// Detroy modal
	$('body').on('hidden.bs.modal', '.modal', function () {
		$(this).removeData('bs.modal');
	}); 
</script>