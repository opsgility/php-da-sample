<div class="container-fluid">
    <div class="row" id="main-content-container-fluid-first-row">
        <div class="col-lg-8">
            <div class="col-sm-12 background_white news-page">
                <img src="<?php echo $mediaPath; ?>"  style="max-width:100%;padding:0px 15px 0px 0px;" alt="<?php echo $mediaDescription; ?>" title="<?php echo $mediaDescription; ?>" />
                <br /><br />
                <span><?php echo $mediaDescription; ?></span>
            </div>
            <div class="clear_both"></div>
        </div>

        <div class="col-lg-4">
            <?php // Load sidebar panel like twitter and news ?>
            <?php echo isset($sidebar_panel) ? $sidebar_panel : ''; ?>
        </div>
    </div>
</div>
<!-- /.container -->