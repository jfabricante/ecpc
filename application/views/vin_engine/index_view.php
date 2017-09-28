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
					<form action="<?php //echo base_url('index.php/vin_engine/store'); ?>" v-on:submit.prevent="analyzer" method="post" enctype="multipart/form-data">
						<!-- row -->
						<div class="row">
							<!-- model picker -->
							<div class="col-md-4">
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
							<div class="col-md-3">
								<div class="form-group" id="file-upload">
									<label for="file-upload">Upload Spreadsheet</label>
									<input type="file" name="upload" accept=".xlsx, .xls, .csv">
								</div>
							</div>
							<!-- ./file-upload -->

							<div class="col-md-3">
								<input type="submit" name="check" value="Check" class="btn btn-flat btn-danger">
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
								<th>Product Model</th>
								<th>Invoice No.</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, index) in items">
								<td>{{ item.sequence }}</td>
								<td>{{ item.product_model }}</td>
								<td>{{ item.vin_no }}</td>
								<td><input type="text" class="form-control" v-bind:value="item.engine_no" v-model="items[index].engine_no"></td>
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
			separator: 0
		},            
		created() {
			this.fetchVinModel()	
		},
		watch: {
			selected: function() {
				console.log(this.selected)
				this.fetchVinControlEntity()
			},
		},
		mounted() {
			var self = this

			$(this.$refs.vin_model).on("change", function() {
				self.selected = self.getSelectedModel($(this).val())
			})
		},
		methods: {
			fetchVinModel: function() {
				axios.get(appUrl + '/vin/ajax_model_list')
				.then((response) => {
					this.vinModel = response.data
				})
				.catch(function (err) {
					console.log(err.message);
				});
			},
			getSelectedModel: function(searchItem)
			{
				for(var [index, value] of this.vinModel.entries())
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

					for(var i = 1; i <= this.selected.lot_size; i++)
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

				})
				.catch((error) => {
					// your action on error success
					console.log(error)
				});
			},
		},
	});

	$(document).ready(function() {
		//$('.table').DataTable();
	});

	$('select').select2()

	// Detroy modal
	$('body').on('hidden.bs.modal', '.modal', function () {
		$(this).removeData('bs.modal');
	}); 
</script>