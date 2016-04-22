<style>.hideSignUp{display:none;}.showSignUp{display:block;}</style>

<h2 style="margin-top: 150px;">Blob storage:</h2>


<div class="container-fluid">
    <form class="form-horizontal" role="form" method="post" action="" id="mediaupload-form" enctype="multipart/form-data">
        <div class="form-group">
           <?php 
           if(isset($blob_list) && !empty($blob_list)){ 
               
               foreach($blob_list as $blob){
               ?>
               <div class="col-sm-10"> <?php echo  $blob->getName();?></div>
           <?php 
               }
           }?>
            
        </div>
        
        <div class="form-group">
            <label for="image" class="col-sm-2 control-label"> image  file:</label>
            <div class="col-sm-10"><input type="file" class="" id="image" name="image" placeholder="image" ></div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <img src="<?php echo $image_url;?>">
                <button type="submit" class="btn btn-primary">Submit</button>				
            </div>
        </div>  
        <div>

        </div>
    </form>	
</div>
			
