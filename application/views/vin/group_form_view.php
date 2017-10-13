<!-- Tags resources -->
<link href="<?php echo base_url('resources/plugins/tags/css/jquery.tagit.css') ?>" rel="stylesheet" >
<link href="<?php echo base_url('resources/plugins/tags/css/tagit.ui-zendesk.css') ?>" rel="stylesheet" >
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url('resources/templates/AdminLTE-2.3.5/plugins/jQueryUI/jquery-ui.js');?>"></script>
<script src="<?php echo base_url('resources/plugins/tags/js/tag-it.min.js'); ?>"></script>

<script>
	$(function() {
		var available_tags;
		var appUrl = "<?php echo base_url('index.php/vin/ajax_model_name'); ?>";

		$.ajax({
			url: appUrl,

			success: function(data) {
				available_tags = JSON.parse(data);
				console.log(available_tags);
				$('#myTags').tagit({
					availableTags: available_tags,
					beforeTagAdded: function(evt, ui) {

						if ($.inArray(ui.tagLabel, available_tags) < 0)
						{
							return false
						}
					},
				});
			},
		});
	});
</script>

<section class="content vin_control" style="min-height: 650px">
	<div class="row">
		<div class="col-md-3">
			<!-- form -->
			<form action="<?php echo base_url('index.php/vin/group_store'); ?>" method="post">
				<!-- box-danger -->
				<div class="box box-danger">
					<!-- box-body -->
					<div class="box-body">
						<div class="form-group hidden">
							<input type="number" class="form-control" id="id" name="ID" value="<?php echo isset($entity->ID) ? $entity->ID : 0; ?>">
						</div>

						<div class="form-group">
							<label for="name">Group Name</label>
							<input type="text" class="form-control" id="name" name="NAME" value="<?php echo isset($entity->NAME) ? $entity->NAME : ''; ?>" required>
						</div>

						<div class="form-group">
							<label for="myTags">Models</label>
							<input name="MODELS" id="myTags" class="form-control" value="<?php echo isset($entity->MODELS) ? $entity->MODELS : ''; ?>">
						</div>

						<div class="form-group">
							<input type="submit" value="Submit" class="btn btn-flat btn-danger pull-right">
						</div>
					</div>
					<!-- ./box-body-->
				</div>
				<!-- ./box-danger -->
			</form>
			<!-- ./form -->
		</div>
	</div>
</section>
