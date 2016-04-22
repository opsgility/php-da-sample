<script>
    /**
     * These variable names useful when user select filter or click on pagination links
     */
    var controllerName = '<?php echo $this->router->fetch_class(); ?>';
    var methodName = '<?php echo $this->router->fetch_method(); ?>';
</script>
<div class="container-fluid">
    <div class="row" id="main-content-container-fluid-first-row">
        <div class="col-lg-8">
            <div class="col-sm-12 no_padding">               
                <div class="row community_title_panel">
                    <div class="community-icon"></div>
                    <div class="community_title_caption">Community</div>
                    <div class="span5 pull-right community_title_panel_filter">
                        <label for="filter" style="font-weight: normal;">Filter</label>
                        <?php $url =  base_url().'community/index/1/filter/'; ?>
                        <select name="filter" id="filter">
                            <option value="<?php echo $url; ?>all" <?php echo ($filterquery == 0) ? "selected='selected'": ''; ?>>All</option>
                            <option value="<?php echo $url; ?>image" <?php echo ($filterquery == 1) ? "selected='selected'": ''; ?>>Image</option>
                            <option value="<?php echo $url; ?>video" <?php echo ($filterquery == 2) ? "selected='selected'": ''; ?>>Video</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- End col-sm-12 -->
            <?php if(isset($posts_list) && count($posts_list) > 0 && !empty($posts_list)){ ?>
                <div id="disc-content" class="col-sm-12 background_white dis_list_box">
                    <?php echo $content; ?>
                </div>
            <?php
                }else{ 
                    if($this->session->userdata('logged_in')){
            ?>
                <div class="span3 pull-left"><button class="btn btn-primary" id="login-to-post-redirect" style="margin-left:15px;" onclick="location.href='<?php echo base_url(); ?>community/postms'">Start New Discussion</button></div>
                <?php } else { ?>
                <div class="span3 pull-left"><button class="btn btn-primary" id="login-to-post" style="margin-left:15px;">Start New Discussion</button></div>
                <?php } ?>       
            <?php }// End IF isset $posts_list ?>
        </div>

        <div class="col-lg-4">
            <?php // Load sidebar panel like twitter and news ?>
            <?php echo isset($sidebar_panel) ? $sidebar_panel : ''; ?>
        </div>
    </div>
</div>
<!-- /.container-fluid -->