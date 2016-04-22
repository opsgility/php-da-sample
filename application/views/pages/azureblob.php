<!-- 2-column layout content area-->
<div class="row row-offcanvas row-offcanvas-right">
    <div class="col-xs-12 col-sm-8">
        <h1>All Discussion</h1>
        <?php echo (isset($error) && !empty($error)) ? $error :''; ?>
        <div>
            <form action="<?php echo base_url(); ?>wablob" id="blobtest" name="blobtest" method="post" enctype="multipart/form-data"  accept-charset="utf-8">
                <input type="file" name="fileBlob" id="fileBlob" />
                <button type="submit" name="btnUploadBlob" id="btnUploadBlob">Upload</button>
            </form>
        </div>
        <hr />        
        <div>
            <?php
            if(isset($blob_list) && !empty($blob_list)) {                
                echo '<ul>';
                foreach($blob_list AS $key=>$blob){
                    //echo '<li><div><strong>'.$blob->getName().'</strong><br /><img src="'.$blob->getUrl().'" alt="" style="height:140px;" /></div></li>';
                    echo '<li><div><strong>'.$blob->getName().'</strong><br /><img src="'.$this->config->item('cdn_img_url').$blob->getName().'" alt="" style="height:140px;" /></div></li>';
                    //if(is_object($blob)){echo '<pre>';print_r($blob);echo '</pre>';}
                }
                echo '</ul>';
                //echo '<pre>';print_r($blob_list);echo '</pre>';
            }
            ?>
        </div>
    </div>
</div>
<!--/row-->