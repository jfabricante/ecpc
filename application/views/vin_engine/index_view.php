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
							<!-- classification picker -->
							<div class="col-md-2">
								<div class="form-group">
									<label for="classification">Classification</label>
									<select name="classification" id="classification" ref="classification" class="select2 form-control" >
										<option></option>
										<option v-for="option in classification" v-bind:value="option.short_code">
											{{ _.padStart(option.short_code, 3, '0') }} - {{ option.description }}
										</option>
									</select>
								</div>
							</div>
							<!-- ./classification-picker -->
							
							<!-- model picker -->
							<div class="col-md-2">
								<div class="form-group" id="model">
									<label for="vin_model">Model Name</label>
									<select name="vin_model" id="vin_model" ref="vin_model" class="select2 form-control" >
										<option></option>
										<option v-for="option in vinModel" v-bind:value="option.product_model">
											{{ option.product_model }}
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
										<option v-for="option in portcode" v-bind:value="option.short_code">
											{{ option.short_code }} - {{ option.description }}
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
										<option v-for="option in serial" v-bind:value="option.short_code">
											{{ option.short_code }} - {{ option.description }}
										</option>
									</select>
								</div>
							</div>
							<!-- ./serial-picker -->

							<div class="col-md-2">
								<div class="form-group">
									<label> </label>
									<input type="submit" name="process" value="Process" class="btn btn-flat btn-danger form-control">
								</div>
							</div>
						</div>
						<!-- ./row -->

						<!-- row -->
						<div class="row">
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
								<th>Product Model</th>
								<th>Invoice No.</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, index) in items">
								<td>{{ item.sequence }}</td>
								<td>{{ item.product_model }}</td>
								<td>{{ item.vin_no }}</td>
								<!-- <td><input type="text" class="form-control" v-bind:value="item.engine_no" v-model="items[index].engine_no"></td> -->
								<td>{{ item.engine_no }}</td>
								<td>{{ item.security_no }}</td>
								<td>{{ item.lot_no }}</td>
								<td>{{ item.model_name }}</td>
								<td>{{ item.invoice_no }}</td>
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
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      ...
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
			separator: 0,
			fileUpload: '',
			excelObject: [],
			porcode: [],
			serial: [],
			classification: [],
			entryNo: ''
		},            
		created() {
			this.fetchVinModel()
			this.fetchPortcode()
			this.fetchSerial()
			this.fetchClassification()
		},
		watch: {
			selected: function() {
				this.fetchVinControlEntity()
			},
			excelObject: function() {
				this.assignValues()
			}
		},
		mounted() {
			var self = this

			$(this.$refs.vin_model).on("change", function() {
				self.selected = self.getSelectedModel($(this).val())
			})

			$(this.$refs.fileUpload).on('change', this.filePicked)

			$(this.$refs.portcode).on('change', function() {
				self.$set(self.portcode, 'selected', $(this).val())
			})

			$(this.$refs.serial).on('change', function() {
				self.$set(self.serial, 'selected', $(this).val())
			})

			$(this.$refs.classification).on('change', function() {
				self.$set(self.classification, 'selected', $(this).val())
			})

		},
		methods: {
			fetchVinModel: function() {
				axios.get(appUrl + '/vin/ajax_model_list')
				.then((response) => {
					this.vinModel = response.data
				})
				.catch((err) => {
					console.log(err.message);
				});
			},
			fetchPortcode: function() {
				axios.get(appUrl + '/portcode/ajax_portcode_list')
				.then((response) => {
					this.portcode = response.data
					console.log(this.portcode)
				})
				.catch((err) => {
					console.log(err.message);
				});
			},
			fetchSerial: function() {
				axios.get(appUrl + '/serial/ajax_serial_list')
				.then((response) => {
					this.serial = response.data
					console.log(this.serial)
				})
				.catch((err) => {
					console.log(err.message);
				});
			},
			fetchClassification: function() {
				axios.get(appUrl + '/classification/ajax_classification_list')
				.then((response) => {
					this.classification = response.data
					console.log(this.classification)
				})
				.catch((err) => {
					console.log(err.message);
				});
			},
			getSelectedModel: function(searchItem)
			{
				for (let [index, value] of this.vinModel.entries())
				{
					if (searchItem == value.product_model)
					{
						return value
					}
				}

				return false
			},
			populateItems: function()
			{
				if (this.selected instanceof Object)
				{
					// Clear items before populate
					this.clearItems()

					for (var i = 1; i <= this.selected.lot_size; i++)
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
				this.vinSuff = _.padStart(this.vinSuff, this.calculatedLength, 0);

				// Concatenate the prefix and suffix
				this.vinControl.vin_no = this.vinPref + this.vinSuff

				var formattedData = {
						sequence: count,
						product_model: this.vinControl.product_model || '',
						vin_no: this.vinControl.vin_no || '',
						engine_no: this.selected.engine_pref || '',
						security_no: '',
						lot_no: Number(this.vinControl.lot_no) + 1 || '',
						model_name: this.vinControl.model_name || '',
						invoice_no: ''
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
						product_model: this.selected.product_model,
					}
				})
				.then((response) => {
					this.vinControl = response.data
					this.lastVin    = this.vinControl.vin_no
					this.lastLot    = this.vinControl.lot_no

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
				// Assign the point of reference
				this.calculatedLength = this.occurenceOfNumber(this.vinControl.vin_no)
				this.separator = this.vinControl.vin_no.length - this.calculatedLength

				// Set the prefix from start to separator
				this.vinPref = this.vinControl.vin_no.substr(0, this.separator)

				// Set the vin suffix from separator until the end of the string
				this.vinSuff =  this.vinControl.vin_no.substr(this.separator)
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
						// Split into two to get the model prefix
						//model = model.split('-')

						/*if (this.selected.product_model.includes(model[0]))
						{
							sheetName = model.join('-')
							break
						}*/

						// Implement a strict cheking of excel sheet
						if (this.selected.product_model === model)
						{
							sheetName = model
							break
						}
					}

					if (sheetName === '')
					{
						alert('Cannot find sheet that match on the model.')
						console.log(this.selected.product_model)
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
					if (this.selected.lot_size == this.excelObject.length)
					{
						for(let [index, excel] of this.excelObject.entries())
						{
							this.items[index].engine_no += excel["Engine No."] + (this.selected.stamp || '')
							this.items[index].invoice_no = excel["MC Invoice No."]
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
					url: appUrl + '/vin_engine/store_resource',
					method: 'post',
					data: {
						items: this.items,
						selected_model: this.selected,
						portcode: this.portcode.selected,
						serial: this.serial.selected,
						classification: this.classification.selected,
						vin_control: this.vinControl,
						entry_no: this.entryNo
					}
				})
				.then((response) => {

					console.log(response)

					if (response.data !== null)
					{
						window.open(appUrl + '/vin_engine/download')
					}
					
				})
				.catch((error) => {
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