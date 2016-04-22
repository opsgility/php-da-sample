<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Windows Azure media service testing/sample for this controller.
 * Testing purpose
 * 
 */

class Wamedia extends CI_Controller {
    
//    const MEDIA_SERVICES_ACCOUNT_NAME   = 'digitalagency';
//    const MEDIA_SERVICES_ACCOUNT_KEY    = 'amMueRDeWYhJ2zT6zQ/p/KiIURqOM3Tk3AJjFZt8GHE=';     
    
    const ACCESS_POLICY_NAME    = 'AccessPolicy';
    const SAS_ACCESS_POLICY_NAME    = 'SasAccessPolicy';
    
    public function __construct() {
        
        parent::__construct();
        
       $wamedia_config = array(
           'account_name' => $this->config->item('azure_media_services_account_name'), 
           'account_key' => $this->config->item('azure_media_services_account_key')
               );
       $this->load->library('wamediaservice', $wamedia_config);
       $this->load->helper(array('form', 'url'));
       $this->load->library('session');
       
    }
    
    public function index(){
        
        error_log('calling index');

        // unset session data
        //$this->session->sess_destroy();       

        $result = array('showState'=>false);
        
        if($_FILES){
            if ($_FILES["media"]["error"] > 0) {
                echo "Error: " . $_FILES["media"]["error"] . "<br>";                
            }else{
                echo "Upload: " . $_FILES["media"]["name"] . "<br>";
                echo "Type: " . $_FILES["media"]["type"] . "<br>";
                echo "Size: " . ($_FILES["media"]["size"] / 1024) . " kB<br>";
                echo "Stored in: " . $_FILES["media"]["tmp_name"];
                
                // upload
                $video = file_get_contents($_FILES['media']['tmp_name']);
                $filename = $_FILES["media"]["name"];
                $fileInfo = pathinfo($_FILES["media"]["name"]);
                $asset_name = 'a1-'.$fileInfo['filename']; // shows in the content page
                $sas_asset_name = $asset_name.'Output'; // shows in the content page
                $sas_jobname = $asset_name.'-Job'; // shows in the jobs page job name
              
                //error_log(' file info '.print_r($fileInfo, true));
                //error_log('asset name '. $asset_name .' sas asset name '. $sas_asset_name.' sas_jobname '. $sas_jobname);

                $this->session->set_userdata('sas_jobname', $sas_jobname);
                $this->session->set_userdata('filename', $filename);
                $this->session->set_userdata('sas_asset_name', $sas_asset_name);

                //$result = $this->wamediaservice->uploadFileToMediaService($filename, $video, $sas_jobname, $asset_name, $sas_asset_name);
                $result = $this->wamediaservice->uploadFileToMediaService($filename, $video, $asset_name);
                error_log('result index '. print_r($result, true));
           }
        }
        
        $data['result'] = $result;

        $data['title'] =  'Welcome '.WEBSITE_NAME;
        $data['content_body'] =  'Content Goes here';
        $data['twitter_widget'] = true;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/nav_bar', $data);
        $this->load->view('pages/wamediaupload', $data);
        $this->load->view('templates/footer', $data);
    }
}
/* End of file wamedia.php */
/* Location: ./application/controllers/wamedia.php */