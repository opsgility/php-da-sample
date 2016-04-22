<?php
/*
 * Web Job - To update webjobs
 * Windows Azure Media Service - Job related
 * Get and Update Windows Azure Media Service video convert job satus in DB
 * 
 */ 

// set the path - production
$pathToRoot = getenv('HOME_EXPANDED') . '\\site\\wwwroot';
$pathToPear = getenv('HOME_EXPANDED') . '\\site\\wwwroot\\pear';
set_include_path(get_include_path() . PATH_SEPARATOR . $pathToRoot);
set_include_path(get_include_path() . PATH_SEPARATOR . $pathToPear);

require("cronhelper.php");


class Wamsjobstatus {
    
    public function index() {
        
        $CI =& get_instance(); 
        // load config, lib, model        
        // WAMS service
        $wamedia_config = array(
            'account_name' => $CI->config->item('azure_media_services_account_name'),
            'account_key' => $CI->config->item('azure_media_services_account_key')
        );
        
        $CI->load->library('wamediaservice', $wamedia_config);
        $CI->load->model('discussion_model');
        
         //$result = $CI->discussion_model->getMediaJobIds();
         $result = $CI->discussion_model->getIncompletedJobIds();
         
         if(!empty($result)){
             
             foreach ($result AS $key => $val) {
                 if(isset($val->job_id) && !empty($val->job_id) && isset($val->asset_id) && !empty($val->asset_id)){
                     try {
                        $job = $CI->wamediaservice->getJobByJobId($val->job_id);
                        if($job){
                            $jobStatus = $CI->wamediaservice->isJobCompleted($job);
                            if($jobStatus){
                                $filename = pathinfo($val->file_name)['filename']; // php 5.4 array feature 
                                $sasMediaResult = $CI->wamediaservice->getSasLocator('Output_asset_' . $val->media_path, $filename); // {sas_asset_name, filename without extension}
                                if ($sasMediaResult['status']) {
                                    $CI->discussion_model->updateMediaJobStatus($val->id, $sasMediaResult['media_path']);
                                }                     
                            }
                        }
                     } catch (ServiceException  $e) {
                        $code = $e->getCode();
                        $error_message = $e->getMessage();
                        error_log(__CLASS__ ." ".__METHOD__." ". $code.": ".$error_message);
                     }
                 }

             }
         }
 
    }
}


$obj = new Wamsjobstatus();
$obj->index();
exit;
?>