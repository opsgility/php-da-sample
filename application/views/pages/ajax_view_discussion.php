<?php if( isset($discussion) && !empty($discussion) && $currentpage <= 1) { ?>
<div class="row dis_list_box_row">
    <div class="col-sm-2 user_icon_box">
        <span class="dis_user_name" title="<?php echo !empty($userinfo[0]->userName)?$userinfo[0]->userName:''; ?>"><?php  echo !empty($userinfo[0]->userName) ? $userinfo[0]->userName:''; ?></span>        
        <?php
            if(isset($userinfo[0]->userAvatar)  && !empty($userinfo[0]->userAvatar)){
                $userAvatar = ($userinfo[0]->userSocialProvider == 1) ? $this->config->item('cdn_avatar_img_url').$userinfo[0]->userAvatar : $userinfo[0]->userAvatar;
            }else{
                $userAvatar = STATIC_URL.'images/default_profile_pic.jpg';
            }
        ?>
        <img class="media-object" alt="<?php echo !empty($userinfo[0]->userName)?$userinfo[0]->userName:''; ?>" src="<?php echo $userAvatar;?>" style="width:64px;" />
        <span class="postRespAndDate"><?php echo (isset($userinfo[0]->noOfPosts) || !empty($userinfo[0]->noOfPosts)) ? "Post's: ".$userinfo[0]->noOfPosts : '' ; ?></span>
    </div>

    <div class="col-sm-10 discussion_section">
        <div id="discussion">
            <h5 class="header"><a class="dis_list_title" href="<?php echo str_replace('index.php/', '', current_url()); ?>" title="<?php echo $discussion[0]->postTitle; ?>"><?php echo $discussion[0]->postTitle; ?></a></h5>
            <div class="content"><?php echo $discussion[0]->postContent; ?></div>
            <p class="postRespAndDate">
                <?php if($discussion[0]->noOfPostResponses > 0 ) {echo 'Replies: '.$discussion[0]->noOfPostResponses.'&nbsp;&nbsp;|&nbsp;&nbsp;';} ?>
                Date: <?php echo date_format(new DateTime($discussion[0]->postTimestamp), 'j<\s\up>S</\s\up> F Y'); ?>
            </p>
            
            <?php
            list($width, $height, $type, $attr, $media_src, $media_type, $image, $flag) = array('', '', '', '', '', '', '', true);
            
            // Find is discussion have any media eaither image or video
             if(isset($discussion[0]->postHavemedia)){
                // IF discussion have image
                if($discussion[0]->postHavemedia == 1){
                    $media_type =   'image';
                    $media_src = $this->config->item('cdn_img_url').$discussion[0]->postMediaPath;
                    list($width, $height, $type, $attr) = @getimagesize($media_src);
                    $image = '<img class="media-object" alt="" src="'.$media_src.'" style="width: 125px;" />';
                }
                
                // IF discussion have video
                elseif($discussion[0]->postHavemedia == 2){
                    $media_type =   'video';
                    // If WAMS (Windows Azure Media Service) job is completed then it will return one public URL to watch video
                    if($discussion[0]->postMediaJobStatus){
                        $media_src = $discussion[0]->postMediaPath;
                        $image = '<img class="media-object" alt="" src="'.base_url().'assets/images/video-play-button-300x225.jpg" style="width: 125px;" />';
                    }else{
                        // if media is video and not yet ready to display
                        $flag = false;
                        echo '<br /><strong><i>Please wait...</i></strong>'; 
                    }
                }
                
                if($flag){
                    echo '<div align="center"><a href="javascript:;" class="media_popup_parent" data-media-width="'.$width.'" data-media-height="'.$height.'" data-media-path="'.$media_src.'" data-media-type="'.$media_type.'" data-media-description="'.$discussion[0]->postMediaDescription.'">'.$image.'</a></div>';
                } 
            }
            ?>
        </div>
    </div>
</div>
<?php } ?>

<div class="row dis_list_box_row">
    <?php 
    if(isset($response_list)&&!empty($response_list)){
        echo '<div class="col-sm-12" id="discussion-responses">';
        foreach($response_list as $response) {
    ?>
    <div id="responses-<?php echo $response->rid; ?>" class="content responses">
        <div class="col-sm-3">
            <span class="dis_user_name"><?php echo $response->userName; ?></span>
            <?php
                if(isset($response->userAvatar)  && !empty($response->userAvatar)){
                    $userAvatar = ($response->userSocialProvider == 1) ? $this->config->item('cdn_avatar_img_url').$response->userAvatar : $response->userAvatar;
                }else{
                    $userAvatar = STATIC_URL.'images/default_profile_pic.jpg';
                }
            ?>
            <img class="media-object" alt="<?php echo $response->userName; ?>" src="<?php echo $userAvatar; ?>" style="width:64px;" />
        </div>        
        <div class="col-sm-9">
            <div class="content"><?php echo $response->rContent; ?></div>
            <p class="postRespAndDate">Posted: <?php echo date_format(new DateTime($response->responseDate), 'j<\s\up>S</\s\up> F Y'); ?></p>            
            <?php
            // Find is response have any media eaither image or video
            $flag = true;
            if($response->rHaveMedia){
                // IF response have image
                if($response->rHaveMedia == 1){
                    $media_type =   'image';
                    $media_src = $this->config->item('cdn_img_url').$response->mediaPath;
                    list($width, $height, $type, $attr) = @getimagesize($media_src);
                    $image = '<img class="media-object" alt="" src="'.$media_src.'" style="width: 125px;" />';
                }elseif($response->rHaveMedia == 2){
                    $width = $height='';
                    $media_type =   'video';
                    // If WAMS (Windows Azure Media Service) job is completed then it will return one public URL to watch video
                    if($response->mediaJobStatus){
                        $media_src = $response->mediaPath;
                        $image = '<img class="media-object" alt="" src="'.base_url().'assets/images/video-play-button-300x225.jpg" style="width: 125px;" />';
                    }else{
                        // if media is video and not yet ready to display
                        $flag = false;
                        echo '<br /><strong><i>Please wait...</i></strong>';
                    }
                }
                
                if($flag){
                    echo '<div align="center"><a href="javascript:;" class="media_popup_parent" data-media-width="'.$width.'" data-media-height="'.$height.'" data-media-path="'.$media_src.'" data-media-type="'.$media_type.'" data-media-description="'.$response->mediaDescription.'">'.$image.'</a></div>';
                } 
            }
            ?>
        </div>
    </div>
    <?php
        }
        echo '</div>';
    }
    ?>
</div>

<div class="row">
<?php if(!$this->session->userdata('logged_in')){ ?>
    <div class="span4 pull-left"><button id="login-to-post-response" class="btn btn-primary">Login to Post response</button></div>    
<?php } ?>
<?php if(!empty($link) && !$this->session->userdata('logged_in')){ ?>
    <div class="span6 pull-right" id="resp-list-view" style="padding-top:15px;"><a href="javascript:;" title="Login to see more responses" id="login-to-responses">Login to see more responses</a></div>
<?php } else{ ?>
    <div id="resp-list-view" class="span6 pull-right"><?php echo $link; ?></div>
<?php } ?>
</div>