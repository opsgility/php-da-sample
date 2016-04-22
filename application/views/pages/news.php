<div class="container-fluid">
    <div class="row" id="main-content-container-fluid-first-row">
        <div class="col-lg-8">
            <div class="col-sm-12 background_white news-page">
                <h1>News</h1>
                <div id="news">
                    <strong class="news-title"><?php echo $feed->title; ?></strong>
                    <p class="news-desc"><?php echo $feed->description; ?></p>
                    <span class="news-date"><?php echo date_format(new DateTime($feed->pubDate), 'j<\s\up>S</\s\up> F Y H:i:s'); ?></span>                        
                </div>
                <div class="clear_both"></div>
           </div>
        </div>

        <div class="col-lg-4">
            <?php // Load sidebar panel like twitter and news ?>
            <?php echo isset($sidebar_panel) ? $sidebar_panel : ''; ?>
        </div>
    </div>

</div>
    <!-- /.container -->