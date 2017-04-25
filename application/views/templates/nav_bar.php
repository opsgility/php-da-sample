<div class="navbar navbar-fixed-top navbar-inverse" role="navigation" id="navbar-fixed-top-1">
    <div class="container">
		<div class="navbar-collapse collapse" id="b-menu-1">
            <ul class="nav navbar-nav navbar-right">
                <?php if ($this->session->userdata('logged_in')) { ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?php echo base_url()?>/assets/images/profile-icon.png" /><b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo base_url()?>users/profile/show">My Account</a></li>
                        <li><a href="<?php echo base_url().'index.php/logout';?>">Log Out</a></li>
                    </ul>
                </li>
                <?php }else{ ?>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" title="Login" id="login-link">Login</a>
                    <ul class="dropdown-menu sign_in">
                        <li id="loginHint" class="alert alert-success" style="display:none;margin: 0px 0px 10px 0px;padding: 5px 5px 5px 5px;width: 95%;">Please login to start new discussion</li>
                        <!--
						<li>
                            <button onclick="location.href='<?php echo base_url()?>users/googleLogin';" class="btn btn-google-plus" title="Login using Google"><i class="fa fa-google-plus"></i></button>
                            <button onclick="location.href='<?php echo base_url()?>users/facebookLogin';" class="btn btn-facebook" title="Login using Facebook"><i class="fa fa-facebook"></i></button>
                            <button onclick="location.href='<?php echo base_url()?>users/twitterLogin';" class="btn btn-twitter" title="Login using Twitter"><i class="fa fa-twitter"></i></button>
                            <button onclick="location.href='<?php echo base_url()?>users/microsoftLogin';" class="btn btn-stackexchange" title="Login using Mircosoft Live"><i class="fa fa-windows"></i></button>
                        </li>
						-->
                        <li role="presentation" class="divider"></li>
                        <li>
                            <div class="container-fluid" id="login-form-div">
                                <form class="form-horizontal" role="form" method="post" action="<?php echo base_url(); ?>users/signin" id="login-form" novalidate="novalidate">
                                    <div class="form-group">
                                        <div class="col-sm-10"><input type="email" class="form-control" name="email" id="email" placeholder="Email" value=""></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-10"><input type="password" class="form-control" name="password" id="password" placeholder="Password" value=""></div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-10">
                                            <button type="submit" class="btn btn-primary">Login</button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-10">
                                            <span id="forgotpassword-link">Forgot Password</span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="container-fluid" id="forgotpassword-div">		
	                            <form class="form-horizontal" role="form" method="post" action="<?php echo base_url(); ?>users/forgotpassword" id="forgotpassword-form" novalidate="novalidate">
		                            <span>Forgotten your account's password? Enter your email address and we'll send you a recovery link.</span>
		                            <div class="form-group">			                            
			                            <div class="col-sm-10"><input type="email" class="form-control" name="forgotEmail" id="forgotEmail" placeholder="Email" value=""></div>
		                            </div>
		                            <div class="form-group">
			                            <div class="col-sm-10">
				                            <button type="submit" class="btn btn-primary">Send Recovery Email</button>
			                            </div>						
		                            </div>
	                            </form>				
                            </div>
                        </li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" title="Sign Up">Sign Up</a>
                        <ul class="dropdown-menu" id="signup-dropdown-menu">
                            <li>
							<!--
                                <button onclick="location.href='<?php echo base_url()?>users/googleLogin';" class="btn btn-google-plus" title="Sign up using Google"><i class="fa fa-google-plus"></i></button>
                                <button onclick="location.href='<?php echo base_url()?>users/facebookLogin';" class="btn btn-facebook" title="Sign up using Facebook"><i class="fa fa-facebook"></i></button>
                                <button onclick="location.href='<?php echo base_url()?>users/twitterLogin';" class="btn btn-twitter" title="Sign up using Twitter"><i class="fa fa-twitter"></i></button>
                                <button onclick="location.href='<?php echo base_url()?>users/microsoftLogin';" class="btn btn-stackexchange" title="Sign up using Mircosoft Live"><i class="fa fa-windows"></i></button>
							-->
                                <button onclick="location.href='<?php echo base_url()?>users/signup';" class="btn btn-stackexchange" title="Sign up using Email"><i class="fa fa-stackexchange">@</i></button>
                            </li>
                        </ul>
                </li>                
                <?php } ?>
            </ul>
		</div>
    </div>
</div>

<!-- fixed navigation bar -->
<div class="navbar navbar-default navbar-fixed-top" id="navbar-fixed-top-2">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#b-menu-2">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a id="main-logo" class="navbar-brand logo" href="<?php echo base_url(); ?>" title="Demo - Digital Agency Web Site Sample" tabindex="1" data-link="1"></a>
        </div>        
        <div class="collapse navbar-collapse" id="b-menu-2"> 
            <ul class="nav navbar-nav main_menu">
                <li><a title="Home" href="<?php echo base_url(); ?>">Home</a></li>
                <li><a class="<?php echo ($this->router->class === 'community')?'active':'' ?>" title="Community" href="<?php echo base_url(); ?>community">Community</a></li>
                <li><a class="<?php echo ($this->router->class === 'product')?'active':'' ?>" title="Product" href="<?php echo base_url(); ?>product">Product</a></li>
            </ul>
        </div>
        <!-- /.nav-collapse -->
    </div>
    <!-- /.container -->
</div>
<!-- /.navbar -->

<!-- main container -->
<div class="container" id="article-container">
<?php
    $alert = $this->session->flashdata('alert');
    $message = $this->session->flashdata('message');
    if (isset($message) && $message !=''){
?>
    <div class="row" id="flashdata">
        <div class="col-lg-12">
            <div class='alert alert-<?php echo (isset($alert) && $alert !== '') ? $alert : "info" ?>'><?php echo $message; ?></div>
        </div>
    </div>            
<?php } ?>
    
<?php if(!isset($home_page)) { ?>
<div class="container-fluid" id="innerpage-banner">
    <img class="img-responsive" alt="" src="<?php echo base_url(); ?>assets/images/innerpage-banner.jpg" />
</div>
<?php } ?>