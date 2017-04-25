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
                    <div class="community_title_caption">Responses</div>
                    <div class="span5 pull-right community_title_panel_filter">
                        <label for="filter" style="font-weight: normal;">Filter</label>
                        <?php $uri = 'community/view/'.$id.'/'.$title.'/1/'; ?>
                        <select name="filter" id="filter">
                            <option value="<?php echo base_url().$uri; ?>filter/all" <?php echo ($filterquery == 0) ? "selected='selected'": ''; ?>>All</option>
                            <option value="<?php echo base_url().$uri; ?>filter/image" <?php echo ($filterquery == 1) ? "selected='selected'": ''; ?>>Image</option>
                            <option value="<?php echo base_url().$uri; ?>filter/video" <?php echo ($filterquery == 2) ? "selected='selected'": ''; ?>>Video</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- End col-sm-12 -->

            <div id="resp-content" class="col-sm-12 background_white dis_list_box">
                <?php echo $content; /* find view/ajax_view_discussion.php */ ?>
            </div>
            
            <?php if($this->session->userdata('logged_in')){ ?>
            <style>#resp-content {border-bottom:0px;}</style>
            <div id="resp-reply" class="col-sm-12 background_white dis_list_box">
                <div class="row dis_list_box_row">                    
                    <div id="dicussion-reply">
                        <form action="<?php echo base_url();?>community/responsems" method="post" accept-charset="utf-8" enctype="multipart/form-data" class="form-horizontal discussion_form" id="discussion-response-form-upload">
                            <input type="hidden" id="postId" name="postId" value="<?php echo $discussion[0]->postId; ?>" />
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-10">
                                    <h5>Your Responses</h5>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="content" class="col-sm-2 control-label">Message:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="content" id="content" placeholder="Content"></textarea>
                                </div>
                            </div>
							<!--
                            <div class="form-group">
                                <label for="mediaFile" class="col-sm-2 control-label">Attachment:</label>
                                <div style="position: relative;">
                                    <a class="btn btn-primary chooseMediaFile" id="chooseMediaFile" href='javascript:;'>Choose File...</a>
                                    <input type="file" class="mediaFile" id="mediaFile" name="mediaFile" id="mediaFile" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label"></label>
                                <div class="col-sm-10"><span class='label label-info' id="uploadFileInfo"></span></div>
                            </div>
                            <div class="form-group" id="media-description-form-group" style="display: none;">
                                <label for="mediaDescription" class="col-sm-2 control-label">Description:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="mediaDescription" id="mediaDescription" placeholder="Description" value="" />
                                </div>
                            </div>
							-->
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div id="progress" class="progress progress-striped active" style="display: none;">
                                    <div class="bar" id="progress-bar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div id="status-msg" class="alert"></div>
                            </div>
                        </div>                        
                    </div>                    
                </div>
            </div>
            <?php } ?>
            
        </div>
        <div class="col-lg-4">
            <?php // Load sidebar panel like twitter and news ?>
            <?php echo isset($sidebar_panel) ? $sidebar_panel : ''; ?>
        </div>
    </div>
</div>
<!-- /.container -->
