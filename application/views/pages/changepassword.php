<style>h1{font-size:20px}</style>
		
<!-- 2-column layout content area-->
<div class="row row-offcanvas row-offcanvas-right" id="main-content-container-fluid-first-row">
	<div class="col-xs-12 col-sm-8">
        <h2>Change Password</h2>
		<div class="container-fluid" id="changepassword-form-div">
			<div class="container-fluid">		
				<form class="form-horizontal" role="form" method="post" action="<?php echo base_url();?>users/changepassword" id="changepassword-form">
					<div class="form-group">
						<label for="password" class="col-sm-2 control-label">New Password</label>
						<div class="col-sm-10"><input type="password" class="form-control" name="password" id="change_password" placeholder="Password" value=""></div>
					</div>
					<div class="form-group">
						<label for="confirm_password" class="col-sm-2 control-label">Confim New Password</label>
						<div class="col-sm-10"><input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" value=""></div>
					</div>                    
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" class="btn btn-primary">Submit</button>							
						</div>						
					</div>
				</form>				
			</div>			
		</div>
	</div>
		
	<?php // Load sidebar panel like twitter and news ?>
	<?php echo isset($sidebar_panel) ? $sidebar_panel : ''; ?>
</div><!--/row-->