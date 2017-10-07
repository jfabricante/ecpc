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

							<!-- entry no -->
							<div class="col-md-2">
								<div class="form-group">
									<label for="entry_no">Entry No.</label>
									<input type="text" name="entry_no" class="form-control" id="entry_no" v-model="entryNo">
								</div>
							</div>
							<!-- ./entry no -->

							<!-- entry no -->
							<div class="col-md-2">
								<div class="form-group">
									<label for="lot_no">Lot No.</label>
									<input type="text" name="lot_no" class="form-control" id="lot_no" v-model="lotNo">
								</div>
							</div>
							<!-- ./entry no -->

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
								<td>{{ item.engine_no }}</td>
								<td>{{ item.security_no }}</td>
								<td>{{ item.lot_no = lotNo }}</td>
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

	var app = new Vue({
		el: '#app',
		data: {
			selected: '',
			items: [],
			items2: [],
			fileUpload: '',
			excelObject: [],
			portcode: [],
			serial: [],
			classification: '002',
			entryNo: '',
			colors: [],
			cpList: [],
			cpEntity: {},
			lotNo: ''
		},            
		created() {
			this.fetchPortcode()
			this.fetchSerial()
			this.fetchColor()
			this.fetchCPList()
		},
		watch: {
			excelObject: function() {
				this.assignValues()
			}
		},
		mounted() {
			var self = this

			$(this.$refs.fileUpload).on('change', this.filePicked)

			$(this.$refs.portcode).on('change', function() {
				self.$set(self.portcode, 'selected', $(this).val())
			})

			$(this.$refs.serial).on('change', function() {
				self.$set(self.serial, 'selected', $(this).val())
			})
		},
		methods: {
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
			fetchColor: function() {
				axios.get(appUrl + '/color/ajax_color_list')
				.then((response) => {
					this.color = response.data
					console.log(this.color)
				})
				.catch((err) => {
					console.log(err.message);
				});
			},
			fetchCPList: function() {
				axios.get(appUrl + '/cp/ajax_cp_list')
				.then((response) => {
					this.cpList = response.data
					console.log(this.cpList)
				})
				.catch((err) => {
					console.log(err.message);
				});
			},
			clearItems: function()
			{
				this.items.splice(0, this.items.length)
				this.items2.splice(0, this.items2.length)
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

					// Set the initial value of sheet
					//var sheetName = ''

					// Look for possible sheet in a smarter way
					for (let model of wb.SheetNames)
					{
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
				let engine = _.countBy(this.excelObject, 'ENGINE NO.')
				let vin    = _.countBy(this.excelObject, 'VIN')

				// Get the key
				let engine_keys = _.keys(engine)
				let vin_keys    = _.keys(vin)

				// Verify if has duplicate
				let enginehasDuplicate = engine_keys.map((key) => { return engine[key] > 1 })
				let vinHasDuplicate = vin_keys.map((key) => { return vin[key] > 1 })

				if (_.includes(enginehasDuplicate, true))
				{
					alert('Engine No. has duplicate entry.')
				}
				else if (_.includes(vinHasDuplicate, true))
				{
					alert('Vin has duplicate entry.')
				}
				else
				{
					this.populateCBUItems()
				}
			},
			populateCBUItems: function()
			{
				this.clearItems()

				for (let [index, entity] of this.excelObject.entries())
				{
					//this.model.push(entity['MODEL NAME'])
					this.cpEntity = this.getCpByModel(entity['MODEL NAME'])

					var formattedData = {
							sequence: index + 1,
							product_model: entity['MODEL NAME'] || '',
							vin_no: entity['VIN'] || '',
							engine_no: (this.cpEntity.engine_pref || '') + entity['ENGINE NO.'],
							security_no: '',
							lot_no: '',
							model_name: '',
							invoice_no: entity['INVOICE NO'],
							color: this.color[Number(entity['COLOR'])]
						}

					this.items.push(formattedData)

					// Data for excel generation
					var excelFormat = {
							engine_no: (this.cpEntity.engine_pref || '') + entity['ENGINE NO.'],
							chassis_no: entity['VIN'],
							vin_no: entity['VIN'],
							series: this.cpEntity.series,
							color: this.color[Number(entity['COLOR'])],
							piston_displacement: this.cpEntity.piston_displacement,
							body_type: this.cpEntity.body_type,
							year_model: this.cpEntity.year_model,
							gross_weight: this.cpEntity.gross_weight,
							net_weight: this.cpEntity.net_weight,
							cylinder: this.cpEntity.cylinder,
							fuel: _.toUpper(this.cpEntity.fuel) 
						}
					
					this.items2.push(excelFormat)
				}
			},
			// Return entity of cp object
			getCpByModel: function(searchItem)
			{
				for (let [index, entity] of this.cpList.entries())
				{
					if (searchItem == entity.model)
					{
						return entity
					}
				}
			},
			storeResource: function()
			{
				//console.log(this.items2)
				axios({
					url: appUrl + '/vin_engine/store_cbu_resource',
					method: 'post',
					data: {
						items: this.items,
						portcode: this.portcode.selected,
						serial: this.serial.selected,
						classification: this.classification,
						entry_no: this.entryNo,
						items2: this.items2
					}
				})
				.then((response) => {

					console.log(response)
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