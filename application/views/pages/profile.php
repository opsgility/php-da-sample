<?php
$sesionSocialProvider = $this->session->userdata("social_provider");

if(!isset($signed_in)){
    $signed_in = false;
}else{    
    $social_provider = false;
}

//$s = $this->session->all_userdata();echo '<pre>';print_r($profile);print_r($s);echo '</pre>';

if(isset($edit) && $edit){    
    //echo '<pre>';print_r($profile);die;
    $social_provider = (isset($edit) && $edit) ? true : $social_provider;
    $name = $profile[0]->name;
    $avatar = $profile[0]->avatar;
    $city = $profile[0]->city;
    $state = $profile[0]->state;
    $twitter_handle = $profile[0]->twitter_handle;
    $email = $profile[0]->email;    
    $notif_special = $profile[0]->notif_special;
    $notif_product = $profile[0]->notif_product;
    $notif_post = $profile[0]->notif_post;
    $notif_tweet = $profile[0]->notif_tweet;
}
?>

<div class="container-fluid" id="signup-form-div">
    <div class="row" id="main-content-container-fluid-first-row">
        <div class="col-lg-12">
            <div class="col-sm-12 background_white">
                <?php // $sn_first is when user coming from any social network for singup, then user need to profide some more information ?>
                <h1 class="edit-profile-label lbl-profile-head"><?php echo ( (isset($edit) && $edit) || (isset($sn_first) && $sn_first)) ? 'My Profile' : 'Sign Up using email'; ?></h1>
                <?php
                if(isset($status) && $status){
                    echo $this->session->flashdata('message');
                    echo isset($msg) ? $msg : ''; 
                }else{
                    $formUrl    = (isset($edit) && $edit) ? base_url().'users/updateprofile' : base_url().'users/userprofile'; 
                ?>
                <form class="form-horizontal" role="form" method="post" action="<?php echo $formUrl?>" id="signup-form" accept-charset="utf-8" enctype="multipart/form-data">
                    <input type="hidden" name="profileEdit" value="<?=(isset($edit) && $edit)? $edit : '0' ;?>" />

                    <?php if(!$signed_in){?>
                    <input type="hidden" name="social_provider" value="<?=(isset($social_provider))? $social_provider : "" ;?>" />
                    <?php }?>
                    <div class="form-group">
                        <label for="fullname" class="col-sm-2 control-label edit-profile-label">Name:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Name" value="<?=(isset($name))? $name : "" ;?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="avatar" class="col-sm-2 control-label edit-profile-label">Picture: </label>
                        <div class="col-sm-10">                            
                        <?php if( (isset($edit) && $edit) && ($sesionSocialProvider <= 1 ) ) { ?>
                            <label id="lblBrowseAvatar" for="avatar" class="col-sm-2 control-label edit-profile-label">Browse Picture</label>
                            <?php
                                if(isset($avatar)  && !empty($avatar)){
                                    $userAvatar = ($social_provider == 1) ? $this->config->item('cdn_avatar_img_url').$avatar : $avatar;
                                }else{
                                    $userAvatar = STATIC_URL.'images/default_profile_pic.jpg';
                                }
                            ?>                            
                            <img id="edit-profile-avatar" class="edit-profile-avatar one" src="<?php echo $userAvatar ;?>" alt="<?php echo (isset($name))? $name : ""; ?>" />
                            <input type="file" class="form-control" id="avatar" name="avatar" value="" style="display: none;" />                        
                        <?php }elseif( (isset($edit) && !$edit)) { ?>
                            <label id="lblBrowseAvatar" for="avatar" class="col-sm-2 control-label edit-profile-label">Browse Picture</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" value="" style="display: none;" />                            
                        <?php } else { ?>
                            <img class="edit-profile-avatar two" src="<?php echo (isset($avatar) && !empty($avatar)) ? $avatar : STATIC_URL.'images/default_profile_pic.jpg' ;?>" alt="<?php echo (isset($name))? $name : ""; ?>" />                            
                        <?php } ?>
                        </div>
                    </div>

            <div class="form-group">
                <label for="name" class="col-sm-2 control-label edit-profile-label">City: </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?=(isset($city))? $city : "" ;?>" />
                </div>
            </div>

            <div class="form-group">
                <label for="state" class="col-sm-2 control-label edit-profile-label">State: </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="state" name="state" placeholder="State" value="<?=(isset($state))? $state : "" ;?>" />
                </div>
            </div>

            <div class="form-group">                
                <!-- Use this hidden value for validaton -->
                <input type="hidden" name="isTwitterUser" id="isTwitterUser" value="<?php echo ($sesionSocialProvider == 3) ? 1: 0 ?>" />

                <label for="twitter_handle" class="col-sm-2 control-label edit-profile-label">Twitter Handle: </label>
                <div class="col-sm-10">
                    <?php
                    if( $sesionSocialProvider == 3 ) {
                        echo (isset($twitter_handle))? $twitter_handle : "" ;                        
                    }
                    elseif( (isset($edit) && $edit) || ($sesionSocialProvider != 3 ) ) {
                    ?>
                    <input type="text" class="form-control" id="twitter_handle" name="twitter_handle" placeholder="Twitter Handle" value="<?php echo (isset($twitter_handle))? $twitter_handle : "" ;?>" />
                    <?php } ?>
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="col-sm-2 control-label edit-profile-label">Email:</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?=(isset($email))? $email : "" ;?>" />
                </div>
            </div>

            <!-- If socialIdentity set then show password field -->
            <?php if ($social_provider && ($this->session->userdata("social_provider") <= 1 ) && !$edit) { ?>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label edit-profile-label">Password: </label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="password" id="signup_password" placeholder="Password" value="" />
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password" class="col-sm-2 control-label edit-profile-label">Confim Password: </label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" value="" />
                </div>
            </div>
            <?php } ?>

            <div class="form-group">
                <label for="notif" class="col-sm-2 control-label edit-profile-label">Notification Preferences </label>
                <div class="col-sm-10">
                    <div class="checkbox">
                        <label>
                            <input <?php if(isset($notif_special) && $notif_special) {echo "checked=checked";} ?> type="checkbox" name="notification">Notify me about special offers and discounts.</label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input <?php if(isset($notif_product) && $notif_product) {echo "checked=checked";} ?> type="checkbox" name="product_notification">
                            Notify me about product announcements</label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input <?php if(isset($notif_post) && $notif_post) {echo "checked=checked";} ?> type="checkbox" name="post_notification">
                            Notify me when someone responds to a comment or question I post about the product.</label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input <?php if(isset($notif_tweet) && $notif_tweet) {echo "checked=checked";} ?> type="checkbox" name="tweet_notification" id="tweet_notification">
                            Notify me when someone responds to a tweet I make about the product.</label>
                    </div>
                </div>
            </div>

            <?php if( (isset($edit) && !$edit) || (isset($sn_first) && $sn_first)){ ?>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="yes" name="terms_service" id="terms_service">
                            <span id="terms-service-label">
                                <strong>I agree to the Contoso <a target="_blank" id="tosLink" href="#">Terms of Service</a> and <a target="_blank" id="PrivacyLink" href="#">Privacy Statement</a></strong>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" id="btn-done-edit-profile"><?php echo (isset($edit) && $edit) ? 'Done Editing': 'Submit'; ?></button>
                </div>
            </div>
            </form>
                <?php } ?>
        </div>
    </div>
</div>
</div>
