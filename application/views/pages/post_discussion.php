<!-- 2-column layout content area-->
<div class="row row-offcanvas row-offcanvas-right" id="main-content-container-fluid-first-row">
	<div class="col-xs-12 col-sm-8">
		<h1>New Discussion</h1>
		<div id="new_dicussion">
            <form action="<?php echo base_url();?>community/post" method="post" accept-charset="utf-8" enctype="multipart/form-data" class="form-horizontal discussion_form" id="new-discussion-form">
                <input type="hidden" name="isAjax" value="1" />
                
                <input type="hidden" name="succes-redirect-url" id="succes-redirect-url" value="<?php echo base_url();?>community/" />
                
				<div class="form-group">
					<label for="title" class="col-sm-2 control-label">Title</label>
					<div class="col-sm-10"><input type="text" class="form-control" name="title" id="title" placeholder="Title" value="" autofocus></div>
				</div>
				<div class="form-group">
					<label for="content" class="col-sm-2 control-label">Content</label>
					<div class="col-sm-10"><textarea class="form-control" name="content" id="content" placeholder="Content"></textarea></div>
				</div>
				<!--
				<div class="form-group">
					<label for="mediaFile" class="col-sm-2 control-label">Media</label>

                    <div style="position:relative;">
                        <a class="btn btn-primary chooseMediaFile" id="chooseMediaFile" href='javascript:;'>Choose File...</a>
                        <input type="file" class="mediaFile" name="mediaFile" id="mediaFile" />
                    </div>
				</div>
                
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label"></label>
                    <div class="col-sm-10"><span class='label label-info' id="uploadFileInfo"></span></div>
                </div>
                
				<div class="form-group" id="media-description-form-group" style="display:none;">
					<label for="mediaDescription" class="col-sm-2 control-label">Description</label>
					<div class="col-sm-10"><input type="text" class="form-control" name="mediaDescription" id="mediaDescription" placeholder="Description" /></div>
				</div>
				-->
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-primary" id="btnSubmit">Post</button>
					</div>                    
				</div>
            </form>
            <div id="progress" class="progress progress-striped active" style="display:none;">
                <div class="bar" id="progress-bar" style="width: 0%;"></div>
            </div>
            <div id="status-msg"></div>
		</div>
	</div>		
    
    <div class="col-lg-4">
        <?php // Load sidebar panel like twitter and news ?>
        <?php echo isset($sidebar_panel) ? $sidebar_panel : ''; ?>
    </div>    
</div><!--/row-->