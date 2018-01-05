<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">

		<!-- Sidebar user panel (optional) -->
		<div class="user-panel">
			<div class="pull-left image">
				<img src="<?php echo base_url('resources/images/default.png');?>" class="img-circle" alt="User Image">
			</div>
			<div class="pull-left info">
				<p>	<?php echo $this->session->userdata('fullname'); ?></p>
			</div>
		</div>

		<!-- Seach form -->
		<form action="<?php echo base_url('index.php/vin_engine/search_field'); ?>" method="post" class="sidebar-form">
			<div class="input-group">
				<input type="text" name="search_string" class="form-control" placeholder="Search...">
				<span class="input-group-btn">
					<button type="submit" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
					</button>
				</span>
			</div>
		</form>

		<!-- Sidebar Menu -->
		<ul class="sidebar-menu">
			<li class="header">MAIN NAVIGATION</li>
			
			<?php
				$menu = $this->uri->uri_string();

				$group_menu = array(
					'color/list_',
					'classification/list_',
					'serial/list_',
					'security/list_',
					'portcode/list_',
					'cp/list_'
				);
			?>

			<?php if (in_array($this->session->userdata('user_access'), array('Administrator'))): ?>
				<li class="treeview <?php echo in_array($menu, $group_menu) ? 'active' : '' ?>">
					<a href="#">
						<i class="fa fa-newspaper-o"></i> <span>Data Entry</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>

					<!-- group menu items -->
					<ul class="treeview-menu">
						<li class="<?php echo $menu == 'color/list_' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/color/list_') ?>"><i class="fa fa-circle-o"></i><span>Color</span></a></li>

						<li class="<?php echo $menu == 'classification/list_' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/classification/list_') ?>"><i class="fa fa-circle-o"></i><span>Classification</span></a></li>

						<li class="<?php echo $menu == 'serial/list_' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/serial/list_') ?>"><i class="fa fa-circle-o"></i><span>Serial</span></a></li>

						<li class="<?php echo $menu == 'security/list_' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/security/list_') ?>"><i class="fa fa-circle-o"></i><span>Security</span></a></li>

						<li class="<?php echo $menu == 'portcode/list_' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/portcode/list_') ?>"><i class="fa fa-circle-o"></i><span>Port Code</span></a></li>

						<li class="<?php echo $menu == 'cp/list_' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/cp/list_') ?>"><i class="fa fa-circle-o"></i><span>CP Details</span></a></li>
					</ul>
				</li>
			<?php endif ?>

			<?php if (in_array($this->session->userdata('user_access'), array('Administrator'))): ?>
				<li class="<?php echo $menu == 'vin/list_' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/vin/list_') ?>"><i class="fa fa-table"></i><span>Vin Model</span></a></li>
			<?php endif ?>

			<?php if (in_array($this->session->userdata('user_access'), array('Administrator'))): ?>
				<li class="<?php echo $menu == 'vin/group_list' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/vin/group_list') ?>"><i class="fa fa-object-group"></i><span>Model Group</span></a></li>
			<?php endif ?>

			<?php if (in_array($this->session->userdata('user_access'), array('Administrator'))): ?>
				<li class="<?php echo $menu == 'vin_control/list_' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/vin_control/list_') ?>"><i class="fa fa-gamepad"></i><span>Vin Control</span></a></li>
			<?php endif ?>

			<?php if (in_array($this->session->userdata('user_access'), array('Administrator'))): ?>
				<li class="<?php echo $menu == 'vin_engine/ckd' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/vin_engine/ckd') ?>"><i class="fa fa-tasks"></i><span>CKD</span></a></li>
			<?php endif ?>

			<?php if (in_array($this->session->userdata('user_access'), array('Administrator'))): ?>
				<li class="<?php echo $menu == 'vin_engine/cbu' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/vin_engine/cbu') ?>"><i class="fa fa-tasks"></i><span>CBU</span></a></li>
			<?php endif ?>

			<?php if (in_array($this->session->userdata('user_access'), array('Administrator', 'ReadOnly'))): ?>
				<li class="<?php echo $menu == 'vin_engine/invoice' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/vin_engine/invoice') ?>"><i class="fa fa-bar-chart"></i><span>Invoice Report</span></a></li>
			<?php endif ?>

			<?php if (in_array($this->session->userdata('user_access'), array('Administrator', 'Regular'))): ?>
				<li class="<?php echo $menu == 'vin_engine/model_view' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/vin_engine/model_view') ?>"><i class="fa fa-pie-chart"></i><span>Model View</span></a></li>
			<?php endif ?>

			<?php if (in_array($this->session->userdata('user_access'), array('Administrator', 'ReadOnly', 'CPOnly'))): ?>
				<li class="<?php echo $menu == 'cop/list_' ? 'active' : ''; ?>"><a href="<?php echo base_url('index.php/cop/list_') ?>"><i class="fa fa-database"></i><span>CP Entry</span></a></li>
			<?php endif ?>

		</ul><!-- /.sidebar-menu -->

	</section>
	<!-- /.sidebar -->
</aside>

