<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

      /*
       * Windows Azure Blob storage testing/sample for this controller.
       * Testing purpose
       * 
       */

      class Wablob extends CI_Controller {
          
          public function __construct() {
              
              parent::__construct();
              
              // load azure credentials from config
              $wablob_config = array(
                  'STORAGE_ACCOUNT' => $this->config->item('azure_storage_account'), 
                  'STORAGE_KEY'=> $this->config->item('azure_storage_key')
                      );
              
              $this->load->library('wablobstorage', $wablob_config);
              $this->load->helper(array('form', 'url'));
              $this->load->library('session');
          }
          
          public function index(){

              $container = $this->config->item('azure_storage_container_images'); // we are going to use this container for storing images.
              // should be unique
              $image_name = 'test1'; // image name or blob name; 
              
              if($_FILES){
                  error_log(' file uploaded');
                  if ($_FILES["fileBlob"]["error"] > 0) {
                      echo "Error: " . $_FILES["fileBlob"]["error"] . "<br>";                      
                  }else{
                      //                echo "Upload: " . $_FILES["image"]["name"] . "<br>";
                      //                echo "Type: " . $_FILES["image"]["type"] . "<br>";
                      //                echo "Size: " . ($_FILES["image"]["size"] / 1024) . " kB<br>";
                      //                echo "Stored in: " . $_FILES["image"]["tmp_name"];

                      error_log(' file uploaded inside no error');    
                      
                      // upload
                      $image_content = file_get_contents($_FILES['fileBlob']['tmp_name']);
                      
                      $image_name = date("YmdHis").$_FILES["fileBlob"]["name"]; // we have to add unique id in front of the image (may be id)
                      // upload images
                      $result = $this->wablobstorage->uploadBlob($container, $image_name,  $image_content);
                      error_log(' result '.print_r($result, true));                      
                      //print_r($result);
                      
                      // $result = $this->mediaservice->uploadFileToMediaService($filename, $video, $sas_jobname, $asset_name, $sas_asset_name);
                  }
                  //error_log('data '.print_r($_FILES, true));
              }else{                
                  $data['error'] = 'There are no files';
              }
              
              //get blob names from container
              $data['blob_list'] = $this->wablobstorage->listBlobs($container);
              
              $data['title'] =  'Welcome '.WEBSITE_NAME;
              $data['content_body'] =  'Content Goes here';
              $data['twitter_widget'] = true;
              
              $this->load->view('templates/header', $data);
              $this->load->view('templates/nav_bar', $data);              
              //$this->load->view('pages/image_upload', $data);
              $this->load->view('pages/azureblob', $data);
              $this->load->view('templates/footer', $data);
          }
          
          public function service(){
              // only for test or Admin
              // create container              
              //$res = $this->wablobstorage->createContainer();
          }
          
          /**
           * Summary of testAzure
           */
          public function testAzure(){              
              $header['title'] =  'Welcome '.WEBSITE_NAME;
              
              //get blob names from container
              $container = $this->config->item('azure_storage_container_images'); // we are going to use this container for storing images.
              $data['blob_list'] = $this->wablobstorage->listBlobs($container);
              
              $this->load->view('templates/header', $header);
              $this->load->view('templates/nav_bar');
              $this->load->view('pages/azureblob', $data);
              $this->load->view('templates/footer');
          }   
      }

      /* End of file Wablob.php */
      /* Location: ./application/controllers/Wablob.php */
