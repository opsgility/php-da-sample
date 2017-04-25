<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Install script
 * This calss will create containers in azure and will load basic db tables
 * Before running this file , 
 * Note: 1.please make sure you have mentioned azure storage details in the config file
 * 2. Mysql ino also( host, username, pass, db)
 * 3. Don't run this file morethan one time.
 * 
 */

class Startinstall extends CI_Controller {
    
    /**
     * This is method constructor
     * load the config and lib files.
     */
    public function __construct() {
        parent::__construct();
      

        
                // WAMS service
        $wamedia_config = array(
            'account_name' => $this->config->item('azure_media_services_account_name'),
            'account_key' => $this->config->item('azure_media_services_account_key')
        );
        $this->load->library('wamediaservice', $wamedia_config);
        
         $this->load->model('discussion_model');
    }
    
    public function index(){        
        // installing db        
        // install basic mysql tables for the site running.    
        //load sql parser lib
        // check is alreday exist or not.
        // check DB table are exist or not.
        
        $this->load->model('discussion_model');
        $result = $this->discussion_model->isTableExist('user_posts');
        
        if($result){
            // redirect to main - this is not first installation  already tables are exist
            redirect('main/');            
        }        
        $this->load->library('sqlparser');
        
        // first sql file.
        $dbms_schema = FCPATH.'application\sql\new_contosoweb_install.sql';
        $sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema)) or die('problem ');
        $sql_query = $this->sqlparser->removeRemarks($sql_query);
        $sql_query = $this->sqlparser->splitSqlFile($sql_query, ';');
        
        $this->_executeSqlQuery($sql_query);

        $data = array();
        $this->load->view('pages/installone', $data);
    }
    
    public function createcontainer(){
        // only for test or Admin install script.
        // load azure credentials from config     
        // BLOB service
        
        $wablob_config = array(
            'STORAGE_ACCOUNT' => $this->config->item('azure_storage_account'),
            'STORAGE_KEY' => $this->config->item('azure_storage_key')
        );

        $this->load->library('wablobstorage', $wablob_config); 
        // create container     
        $all_containers = array($this->config->item('azure_storage_container_images'), $this->config->item('cloud_user_avatar_container'), $this->config->item('azure_storage_container_logs'));
        $existing_containers = $this->wablobstorage->getAllContainersName();

        $to_create = array_diff($all_containers, $existing_containers);
        
        if(isset($to_create) && !empty($to_create)){            
            foreach($to_create as $key => $container_name){
                
               $res = $this->wablobstorage->createContainer($container_name);   
			   sleep(10);       
                if($res===false){
                    error_log(__METHOD__ .' '. __CLASS__ .'  not able to create container '.$container_name);
                }else{
                    //echo 'Container created in azure '. $container_name;
                    //echo "<br >";
                   
                }
            }
        }
        
		sleep(30);
        //$xhrResult['isInstallComplete'] = false;
		$xhrResult['isInstallComplete'] = true;
        //$xhrResult['url'] = base_url()."startinstall/uploadvideosetone";
        $xhrResult['message'] = "Application Setup Complete";
        //$xhrResult['updatemsg'] = "Uploading videos ...";
        echo json_encode($xhrResult);        
    }

    public function uploadvideosetone(){
        // just updating the existing records.
        // uploading videos. ( 3 videos)
       
        $video_list = array(
          '1361'=>'post_1.mp4', 
          '1381'=>'resp_1.3gp', 
          '1401'=>'resp_2.mp4');
        
        foreach($video_list as $media_id=> $file_na){            
            $fn = pathinfo($file_na)['filename'];
            $fileName = $fn.date("YmdHis").'.'.pathinfo($file_na, PATHINFO_EXTENSION);
            $asset_name = 'asset_' . $fileName; // shows in the content page
            $filepath = FCPATH.'content'.DIRECTORY_SEPARATOR.'video'.DIRECTORY_SEPARATOR.$file_na;
            
            $fileContent = file_get_contents($filepath);
            
            $uploadResult = $this->wamediaservice->uploadVideoFileInInstall($asset_name, $fileName, $fileContent);
        
            $update_val['asset_id'] =  $uploadResult['assetId'];
            $update_val['sas_write_url'] =  $uploadResult['sasWriteUrl'];
            $update_val['locator_id'] =  $uploadResult['locatorId'];
            $update_val['access_policy_id'] =  $uploadResult['accessPolicyId'];
            $update_val['file_name'] =  $fileName;
            $update_val['media_path'] =  $fileName;
            // call conver job
            $sas_asset_name = 'Output_' . $asset_name; // shows in the content page {Output_asset_filename}   
            $sas_jobname = 'Job_' . $fileName; // shows in the jobs page job name {Job_Output_asset_filename}

            $jobResult = $this->wamediaservice->sasCreateJob($sas_jobname, $sas_asset_name, $uploadResult['asset']);
           
            $update_val['job_id'] =  $jobResult->getId();

            // update in media
            $result = $this->discussion_model->updateMediaById($media_id, $update_val);
           // error_log('result '.print_r($result, true));
        } 
       
        $xhrResult['isInstallComplete'] = false;
        $xhrResult['url'] = base_url()."startinstall/uploadvideosettwo";
        $xhrResult['message'] = "Post and Response Media/Video Set one uploaded successfully";
        $xhrResult['updatemsg'] = "Uploading videos ...";
        echo json_encode($xhrResult);
    }
    
    public function uploadvideosettwo(){        
        // just updating the existing records.
        // uploading videos. ( 2 videos)
        
        $video_list = array(
          '1341'=>'post_2.mp4', 
          '253'=>'post_3.3gp');
        
        foreach($video_list as $media_id=> $file_na){            
            $fn = pathinfo($file_na)['filename'];
            $fileName = $fn.date("YmdHis").'.'.pathinfo($file_na, PATHINFO_EXTENSION);
            $asset_name = 'asset_' . $fileName; // shows in the content page
            $filepath = FCPATH.'content'.DIRECTORY_SEPARATOR.'video'.DIRECTORY_SEPARATOR.$file_na;
            
            $fileContent = file_get_contents($filepath);
            
            $uploadResult = $this->wamediaservice->uploadVideoFileInInstall($asset_name, $fileName, $fileContent);
        
            $update_val['asset_id'] =  $uploadResult['assetId'];
            $update_val['sas_write_url'] =  $uploadResult['sasWriteUrl'];
            $update_val['locator_id'] =  $uploadResult['locatorId'];
            $update_val['access_policy_id'] =  $uploadResult['accessPolicyId'];
            $update_val['file_name'] =  $fileName;
            $update_val['media_path'] =  $fileName;
            // call conver job
            $sas_asset_name = 'Output_' . $asset_name; // shows in the content page {Output_asset_filename}   
            $sas_jobname = 'Job_' . $fileName; // shows in the jobs page job name {Job_Output_asset_filename}

            $jobResult = $this->wamediaservice->sasCreateJob($sas_jobname, $sas_asset_name, $uploadResult['asset']);
           
            $update_val['job_id'] =  $jobResult->getId();

            // update in media
            $result = $this->discussion_model->updateMediaById($media_id, $update_val);
           // error_log('result '.print_r($result, true));
        }
        
        $xhrResult['isInstallComplete'] = false;
        $xhrResult['url'] = base_url()."startinstall/imageupload";
        $xhrResult['message'] = "Post and Response Media/Video Set two uploaded successfully";
        $xhrResult['updatemsg'] = "Uploading images ...";
        echo json_encode($xhrResult);
    } 
    
    public function imageupload(){
        // load azure credentials from config     
        // BLOB service
        $wablob_config = array(
            'STORAGE_ACCOUNT' => $this->config->item('azure_storage_account'),
            'STORAGE_KEY' => $this->config->item('azure_storage_key')
        );

        $this->load->library('wablobstorage', $wablob_config);
        
       // uploading post/response images - working fine.
        $images_list = array(
            'post_120140403142800.png',
            'post_6120140403145533.jpg',
            'post_7120140403162532.jpg',
            'post_7120140403172727.png',
            'post_7120140403172840.png',
            'post_6120140403173039.png',
            'post_6120140403173040.png',
            'post_120140410202156.jpg',
            'post_120140411170159.jpg',
            'post_120140411171433.jpg',
            'post_120140411171528.jpg',
            'post_120140411171608.jpg',
            'post_120140411171702.jpg',
            'post_120140411173505.jpg',
            'post_120140411173909.jpg',
            'post_120140411173934.jpg',
            'post_120140411174124.jpg',
            'post_9120140428114655.jpg',
            'post_1120140513071802.jpg',
            'resp_1120140513072333.jpg',
            'resp_1120140514061700.jpg'
        );
        
        $cloud_container_images = $this->config->item('azure_storage_container_images');
        foreach($images_list as $key=>$image_name){
             $imagepath = FCPATH.'content'.DIRECTORY_SEPARATOR.'contosoimages'.DIRECTORY_SEPARATOR.$image_name;
             $fileContent = file_get_contents($imagepath);             
             // upload files to WA_BLOBCLOUD_CONTAINER
             $uploadResult = $this->wablobstorage->uploadBlob($cloud_container_images, $image_name, $fileContent);                       
        }
       
        $xhrResult['isInstallComplete'] = false;
        $xhrResult['url'] = base_url()."startinstall/avatarupload";
        $xhrResult['message'] = "Post and Response Media/Images uploaded successfully";
        $xhrResult['updatemsg'] = "Uploading images ...";
        echo json_encode($xhrResult);        
    }
    
    public function avatarupload(){
        // load azure credentials from config     
        // BLOB service
        
        $wablob_config = array(
            'STORAGE_ACCOUNT' => $this->config->item('azure_storage_account'),
            'STORAGE_KEY' => $this->config->item('azure_storage_key')
        );

        $this->load->library('wablobstorage', $wablob_config);        
        // uploading avatar images. - working fine.
        $cloud_container_avatar = $this->config->item('cloud_user_avatar_container');
        $avatar_dir = FCPATH."content\avatar";
        $avatar_images = scandir($avatar_dir);
        
        // first 2 values contains directory values, we no need that.
        for($i=2; $i<=count($avatar_images); $i++){
            if(empty($avatar_images[$i])){
                error_log('avatar upload no data '.$i);
                continue;
            }
            
             $imagepath = $avatar_dir.DIRECTORY_SEPARATOR.$avatar_images[$i];
             $fileContent = file_get_contents($imagepath);             
             // upload files to WA_BLOBCLOUD_CONTAINER
             $uploadResult = $this->wablobstorage->uploadBlob($cloud_container_avatar, $avatar_images[$i], $fileContent);                       
        }        
        
        $xhrResult['isInstallComplete'] = false;
        $xhrResult['url'] = base_url()."startinstall/createminingcontainer";
        $xhrResult['message'] = "Profile Avatar uploaded successfully.";
        $xhrResult['updatemsg'] = "Creating containers for mining data ...";
        echo json_encode($xhrResult);        
    }
    
        public function createminingcontainer(){
        // only for test or Admin install script.
        // create container // BLOB service
                   
        $wablob_config = array(
            'STORAGE_ACCOUNT' => $this->config->item('t_mining_azure_storage_account'),
            'STORAGE_KEY' => $this->config->item('t_mining_azure_storage_key')
        );

        $this->load->library('wablobstorage', $wablob_config);
        
        $all_containers = array($this->config->item('t_mining_azure_container_name'));
        $existing_containers = $this->wablobstorage->getAllContainersName();

        $to_create = array_diff($all_containers, $existing_containers);
        $xhrResult['message'] = "Twitter mining container already existed. And Waiting for job Status...";
        if(isset($to_create) && !empty($to_create)){
            foreach($to_create as $key => $container_name){
                
               $res = $this->wablobstorage->createContainer($container_name, true);            
                if($res===false){
                    error_log(__METHOD__ .' '. __CLASS__ .'  not able to create container '.$container_name);
                }else{
                    //echo 'Container created in azure '. $container_name;
                    //echo "<br >";
                    $xhrResult['message'] = "Twitter mining container created successfully. And Waiting for job Status...";
                }
            }
        }
        
        $xhrResult['isInstallComplete'] = false;
        $xhrResult['url'] = base_url()."startinstall/jobstatus";
        $xhrResult['updatemsg'] = "Waiting for video conversions  ...";
        $xhrResult['message'] = "Twitter mining container created successfully.";
        
        echo json_encode($xhrResult);        
    }
    
    public function jobstatus(){
       // check job conversion completed or not.
       $media_list = array('1361', '1381', '1401', '1341', '253');
       
       $result = $this->discussion_model->getMediaByIdsForJobStatus($media_list);
       $i=0;
                 //error_log('calling '.print_r($result, true));
         if(!empty($result)){
             
             foreach ($result AS $key => $val) {
                 if(isset($val->job_id) && !empty($val->job_id) && isset($val->asset_id) && !empty($val->asset_id)){
                     if($val->job_status && $val->job_status==1){
                         $i++;
                         continue;
                     }
                     try {
                        $job = $this->wamediaservice->getJobByJobId($val->job_id);
                        if($job){
                            $jobStatus = $this->wamediaservice->isJobCompleted($job);
                            
                            if($jobStatus){
                                $filename = pathinfo($val->file_name)['filename']; // php 5.4 array feature 
                                
                                $sasMediaResult = $this->wamediaservice->getSasLocator('Output_asset_' . $val->media_path, $filename); // {sas_asset_name, filename without extension}
                                
                                if ($sasMediaResult['status']) {
                                   // error_log('url '.$sasMediaResult['media_path']);
                                    $this->discussion_model->updateMediaJobStatus($val->id, $sasMediaResult['media_path']);
                                }                     
                            }
                        }
                     } catch (ServiceException $e) {
                        $code = $e->getCode();
                        $error_message = $e->getMessage();
                        error_log(__CLASS__ ." ".__METHOD__." ". $code.": ".$error_message);
                     }
                 }
             }
         }
        
         if($i>=5){
             $xhrResult['isInstallComplete'] = true;
             $xhrResult['updatemsg'] = "Jobs are completed and installation done successfully ";
         }else{
             $xhrResult['isInstallComplete'] = false;
             $xhrResult['updatemsg'] = "Waiting for video conversions ...";
             $xhrResult['message'] = "Twitter mining container created successfully.";
         }
        
        $xhrResult['url'] = base_url()."startinstall/jobstatus/".time();
        echo json_encode($xhrResult);        
    }     
        
    
    // run the sql queries
    private function _executeSqlQuery($sql_query){
        
        if(!isset($sql_query) || empty($sql_query)){
            return false;
        }
        // load db
        $this->load->database();
        
        $i=0;
        foreach($sql_query as $sql){            
            $result = $this->db->query($sql);
            if($result){
                $i++;            
            }else{
                error_log('Error In Query '. $sql);
            }
        }
        
       // echo $i." SQL Queries Executed successfully"."<br>";            
    }    
}

/* End of file startinstall.php */
/* Location: ./application/controllers/startinstall.php */