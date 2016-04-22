<!-- product image carousel -->
<div id="product-carousel" class="carousel slide" data-ride="carousel">
<!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-target="#product-carousel" data-slide-to="0" class="active"></li>
        <li data-target="#product-carousel" data-slide-to="1"></li>
        <li data-target="#product-carousel" data-slide-to="2"></li>
    </ol>

<!-- Wrapper for slides -->
    <div class="carousel-inner">
        <div class="item" onclick="location.href='<?php echo base_url();?>product'">
            <img data-src="<?php echo base_url("assets/images/sb_1.jpg");?>" alt="900x500" src="<?php echo base_url("assets/images/banner-1.jpg");?>">
            <div class="carousel-caption">
                <h3> Varial Kick Flip </h3>
                <p> Do a Kick Flip or some serious Technical Skating on Motion Board 9000</p>
            </div>
        </div>
        <div class="item" onclick="location.href='<?php echo base_url();?>product'">
            <img data-src="<?php echo base_url("assets/images/sb_2.jpg");?>" alt="900x500" src="<?php echo base_url("assets/images/banner-2.jpg");?>">
            <div class="carousel-caption">
                <h3> Carve </h3>
                <p> Carve your new style on Motion 9000.</p>
            </div>
        </div>

        <div class="item active" onclick="location.href='<?php echo base_url();?>product'">
            <img data-src="<?php echo base_url("assets/images/sb_4.jpg");?>" alt="900x500" src="<?php echo base_url("assets/images/banner-3.jpg");?>">
            <div class="carousel-caption">
                <h3>Pro Skate Board Motion 9000</h3>
                <p> Do all your cool stuff on Contoso board Motion 9000.</p>
            </div>
        </div>
    </div>
<!-- Controls -->
    <a class="left carousel-control" href="#product-carousel" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left"></span>
    </a>
    <a class="right carousel-control" href="#product-carousel" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right"></span>
    </a>
</div>
<!-- panels -->


<div class="section">
    <div class="container-fluid">
        <div class="row"  id="main-content-container-fluid-first-row">
            <div class="col-lg-4 col-md-4">
                <div class="news-panel-title">
                    <div class="news-panel-title-icon"></div>       
                    <div class="news-panel-title-caption"> News </div>
                </div>
                <div class="clear_both panel-content"> 
                    <?php echo (isset($news_panel)) ? $news_panel : ''; ?>
                </div>               
            </div>
            
            <div class="col-lg-4 col-md-4">
                <div class="discussion-panel-title" onclick="location.href='<?php echo base_url(); ?>community';">
                    <div class="discussion-panel-title-icon"></div>
                    <div class="discussion-panel-title-caption"> Community </div>
                </div>
                <div class="clear_both panel-content"> 
                    <?php echo (isset($posts_panel)) ? $posts_panel : ''; ?>
                </div>
            </div>       

            <div class="col-lg-4 col-md-4">
                <?php echo (isset($twitter_widget_panel)) ? $twitter_widget_panel : ''; ?>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</div>