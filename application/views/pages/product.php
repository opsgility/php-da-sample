<div class="container-fluid">
    <div class="row" id="main-content-container-fluid-first-row">
        <div class="col-lg-8">
            <div class="col-sm-12 background_white prdcontainer">
                <div id="prd_componentss">
                    <h1><?php echo $prname; ?></h1>
                       
                    <?php
                    // Check is productdetails are set not
                    // If set then show the details using foreach loop
                    // else skip the result
                    if(isset($productdetails) && !empty($productdetails)) {
                        foreach($productdetails AS $key=>$val){
                            /*echo '<pre>'; print_r($val); echo '</pre>';*/
                    ?>
                    <div class="prdcomponent <?php echo $val->{'component_type'}; ?>">
                        <h2><?php echo $val->{'component_type'}; ?></h2>
                        <?php
                            if (isset($val->{'image_name'})){
                                echo "<ul class='thumb_image_list'>";
                                $images = explode(",", $val->{'image_name'});
                                foreach ($images as $img){
                                    $baseurl = base_url();
                                    list($width, $height, $type, $attr) = getimagesize($baseurl.'assets/images/products/large/'.$img);
                                    $mediaPath = $baseurl.'assets/images/products/large/'.$img;
                                    echo "<li><a href=\"javascript:;\" class=\"media_popup_parent\" data-media-width=\"$width\" data-media-height=\"$height\" data-media-path=\"$mediaPath\" data-media-type=\"image\" data-media-description=\"\"><img class=\"thumb_image media-object\" alt=\"\" src=\"/assets/images/products/thumbnail/$img\" /></a></li>";
                                }
                                echo "</ul>";
                            }
                        ?>
                        <div class="text-content short-text"><?php echo $val->{'component_details'}; ?></div>
                        <?php if(strlen($val->{'component_details'}) > 190) { ?> <div class="show-more"><a href="#">Show more</a></div> <?php } ?>
                    </div>
                    <?php
                        } // End Foreach
                    } // End IF
                    ?>
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