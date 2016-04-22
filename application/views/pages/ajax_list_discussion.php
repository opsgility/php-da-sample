<?php
if(isset($posts_list) && count($posts_list) > 0 && !empty($posts_list)){
    foreach($posts_list as $post) {
        $title  =   str_replace(' ', '-', $post->postTitle);       // Replaces all spaces with hyphens.
        $title  =   preg_replace('/[^A-Za-z0-9\-]/', '', $title);  // Remove special characters...
        $title  =   preg_replace('/-+/', '-', strtolower($title)); // Replaces multiple hyphens with single one.

        $url = base_url().'community/view/'.$post->postId.'/'.$title; // make url using with discussion id and title
    ?>
    <div class="row dis_list_box_row">
        <div class="col-sm-3 user_icon_box">
            <span class="dis_user_name" title="<?php echo $post->userName; ?>"><?php echo $post->userName; ?></span>
            <?php
                if(isset($post->userAvatar)  && !empty($post->userAvatar)){
                    $userAvatar = ($post->userSocialProvider == 1) ? $this->config->item('cdn_avatar_img_url').$post->userAvatar : $post->userAvatar;
                }else{
                    $userAvatar = STATIC_URL.'images/default_profile_pic.jpg';
                }
            ?>            
            <img class="media-object" alt="<?php echo $post->userName; ?>" src="<?php echo $userAvatar; ?>" style="width:64px;" />
            <span class="postRespAndDate"><?php echo (isset($post->userNoOfPosts) || !empty($post->userNoOfPosts)) ? "Post's: ".$post->userNoOfPosts : '' ; ?></span>
        </div>

        <div class="col-sm-9 all-posts" id="post-<?php echo $post->postId; ?>">
            <h5><a href="<?php echo $url; ?>" class="dis_list_title" title=""><?php echo $post->postTitle; ?></a></h5>
            <p><?php echo substr($post->postContent, 0, 150).'...'; ?></p>
            <p class="postRespAndDate">
                <?php if($post->noOfResponses > 0 ) {echo 'Replies: '.$post->noOfResponses.'&nbsp;&nbsp;|&nbsp;&nbsp;';} ?>
                Date: <?php echo date('j<\s\up>S</\s\up> F Y', strtotime($post->postDate)); ?>
            </p>
        </div>
    </div>
    <?php } // End foreach ?>
<?php }// End IF isset $posts_list ?>

<div class="row paginationAndPostButton">
    <?php if($this->session->userdata('logged_in')){ ?>
    <div class="span3 pull-left"><button class="btn btn-primary" id="login-to-post-redirect" style="margin-left:15px;" onclick="location.href='<?php echo base_url(); ?>community/postms'">Start New Discussion</button></div>
    <?php } else { ?>
    <div class="span3 pull-left"><button class="btn btn-primary" id="login-to-post" style="margin-left:15px;">Start New Discussion</button></div>
    <?php } ?>
    <?php if(!empty($link)) { ?>
    <div class="span6 pull-right" id="disc-list-view"><?php echo $link; ?></div>
    <?php } ?>
</div>