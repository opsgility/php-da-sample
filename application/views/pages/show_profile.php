<?php
//$s = $this->session->all_userdata();
//echo '<pre>';print_r($s);echo '</pre>';
?>
<div class="container-fluid" id="show-profile-div">
    <div class="row" id="main-content-container-fluid-first-row">
        <div class="col-lg-12">
            <div class="col-sm-12 background_white">
                <h1 class="show-profile-label lbl-profile-head">My Profile</h1>
                <br />
                <div class="row">
                    <div class="col-sm-3">
                        <?php
                            if(isset($profile[0]->avatar)  && !empty($profile[0]->avatar)){
                                $userAvatar = ($profile[0]->social_provider == 1) ? $this->config->item('cdn_avatar_img_url').$profile[0]->avatar : $profile[0]->avatar;
                            }else{
                                $userAvatar = STATIC_URL.'images/default_profile_pic.jpg';
                            }
                        ?>                    
                        <img class="media-object" src="<?php echo $userAvatar;?>" width="125" alt="">
                        <br />
                        <span>Posts: <?php echo ($profile[0]->no_of_posts) ? $profile[0]->no_of_posts : 0; ?></span>
                        <br />
                        <span>Replies: <?php echo ($profile[0]->no_of_responses) ? $profile[0]->no_of_responses : 0; ?></span>
                    </div>

                    <div class="col-sm-9">
                        <h4 class="show-profile-label lbl-name"><?php echo $profile[0]->name; ?></h4>
                        <span><?php echo $profile[0]->email; ?></span>
                        <br />
                        <h5 class="show-profile-label lbl-addr">Address:</h5>
                        <span><?php echo $profile[0]->city.'<br />'.$profile[0]->state; ?></span>
                        <br />
                        <h5 class="show-profile-label lbl-noti">Notification Preferences:</h5>
                        <ul>
                            <li><?php echo (!$profile[0]->notif_special) ? '<i class="fa fa-square-o"></i>' : '<i class="fa fa-check-square-o"></i>'; ?> Notify me about special offers and discounts.</li>
                            <li><?php echo (!$profile[0]->notif_product) ? '<i class="fa fa-square-o"></i>' : '<i class="fa fa-check-square-o"></i>'; ?> Notify me about product announcements</li>
                            <li><?php echo (!$profile[0]->notif_post) ? '<i class="fa fa-square-o"></i>' : '<i class="fa fa-check-square-o"></i>'; ?> Notify me when someone responds to a comment or question I post about the product.</li>
                            <li><?php echo (!$profile[0]->notif_tweet) ? '<i class="fa fa-square-o"></i>' : '<i class="fa fa-check-square-o"></i>'; ?> Notify me when someone responds to a tweet I make about the product.</li>
                        </ul>                        
                    </div>                     
                </div>
                <!--/ End row -->

                <div class="row btnRow">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9"><button id="btn-edit-profile" onclick="location.href='<?php echo base_url(); ?>users/profile/edit'">Edit Profile</button></div>
                </div>
                <!--/ End row -->

            </div>
            <!--/End col-sm-12 -->
        </div>
        <!--/End col-lg-12 -->
    </div>
    <!--/End row -->
</div>
<!--/End container-fluid -->