<?php

/**
 * Wablobstorage library
 * using windowsazure php sdk for accessing Windowsazure blob storage
 * @package     Digital agency app
 * @subpackage  Libraries
 * @category    Windows Azure Blob storage
 * @version     1.0
 * @required    Windowsazure php sdk
 * 
 */


require_once 'WindowsAzure\WindowsAzure.php';

use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Blob\Models\ListContainersOptions;
use WindowsAzure\Blob\Models\CreateBlobPagesOptions;
use WindowsAzure\Blob\Models\PageRange;
use WindowsAzure\Blob\Models\PublicAccessType;
use WindowsAzure\Blob\Models\CreateContainerOptions;
use WindowsAzure\Common\ServiceException;
   

class Wablobstorage {
    
        // Active service builder insatnce
    private $_serviceBuilder;

        // Active media sevices REST proxy instance
    private $_blobRestProxy;
    
    
    public function __construct($wablob_config) {
        $this->initBlobRestProxy($wablob_config);
    }
    
    /*
     * Upload file to container
     * @container => name of the container , where we have to upload the blob
     * @blob_name => uploading file name or blob name
     * @blob_content => filecontent or blob content
     * 
     */
    
    public function uploadBlob($container, $blob_name, $content){
        
        if(empty($container) || empty($blob_name) || empty($content)){
            return false;
        }       

        try {
            //upload blob
            $result = $this->_blobRestProxy->createBlockBlob($container, $blob_name, $content);
            return $result;
            
        } catch (ServiceException  $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            error_log(__CLASS__ ." ".__METHOD__." ". $code.": ".$error_message);
            return false;    
        }            
        
    }
    
    /*
     * get blob
     */
    public function getBlob($container, $blob_name){
        if(empty($container) || empty($blob_name)){
            return false;
        } 
        
        try {
            // get blob            
            $blob = $this->_blobRestProxy->getBlob($container, $blob_name);          
            return $blob;
            
        } catch (ServiceException  $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            error_log(__CLASS__ ." ".__METHOD__." ". $code.": ".$error_message);
            return false;    
        }         
    }
    

    /*
     * Download  blob
     */
    public function downloadBlob($container, $blob_name){
        if(empty($container) || empty($blob_name)){
            return false;
        } 
        
        try {
            // get blob            
            $blob = $this->_blobRestProxy->getBlob($container, $blob_name);          
            return fpassthru($blob->getContentStream());
            
        } catch (ServiceException  $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            error_log(__CLASS__ ." ".__METHOD__." ". $code.": ".$error_message);
            return false;    
        } 
        
    }
    
    /*
     * Lists all of the blobs in the given container.
     * More information can be found at windows azure php sdk on blob rest proxy
     */
    public function listBlobs($container){
                
        if(empty($container)){
            return false;
        }       

        try {
            // List blobs.
            $blob_list = $this->_blobRestProxy->listBlobs($container);
            $blobs = $blob_list->getBlobs();
            return $blobs;
            
        } catch (ServiceException  $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            error_log(__CLASS__ ." ".__METHOD__." ". $code.": ".$error_message);
            return false;    
        }            
        
    }    
    
    /*
     * Deletes a blob or blob snapshot.
     * More information can be found at windows azure php sdk on blob rest proxy
     */
    public function deleteBlob($container, $blob_name){
                
        if(empty($container) || empty($blob_name)){
            return false;
        }       

        try {
            //delete blob
            $result = $this->_blobRestProxy->deleteBlob($container, $blob_name);
            return $result;
            
        } catch (ServiceException  $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            error_log(__CLASS__ ." ".__METHOD__." ". $code.": ".$error_message);
            return false;    
        }            
        
    }

    
   /**
     * Initializes Blob Storage REST proxy
     *
     * @param string $connectionString Media Services account name
     * @param string $accessKey   Primary or secondary access key
     *
     * @return null
     */
    private function initBlobRestProxy($wablob_config) {
        if(empty($wablob_config) || !isset($wablob_config['STORAGE_ACCOUNT']) || !isset($wablob_config['STORAGE_KEY'])){
             error_log(__CLASS__ ." ".__METHOD__." STORAGE_ACCOUNT or STORAGE_KEY is missing : ");
            return false;
            
        }
        // creating connection string
        $connectionString = 'DefaultEndpointsProtocol=http;AccountName='.$wablob_config['STORAGE_ACCOUNT'].';AccountKey='.$wablob_config['STORAGE_KEY'];
        //error_log('calling blob rest proxy init  ');
        $this->_serviceBuilder  = ServicesBuilder::getInstance();
        $this->_blobRestProxy   = $this->_serviceBuilder->createBlobService($connectionString);
        
    }   

    
    /*
     * get URL for the blob
     */
    
    public function getBlobURL($container, $blob_name){
        if(empty($container) || empty($blob_name)){
            return false;
        } 
        
        try {
            // get blob            
            $blob = $this->_blobRestProxy->getBlob($container, $blob_name); 
            if(!empty($blob)){
                 return $blob->getUrl();
            }else{
                return false;
            }
           
            
        } catch (ServiceException  $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            error_log(__CLASS__ ." ".__METHOD__." ". $code.": ".$error_message);
            return false;    
        } 
    }
    /*
     * Prepare the URL for image display
     */
    
//    public static function getUrlForBlob($storage_account, $container, $blob_name){
//        // as per azure document
//        // http://<storage account>.blob.core.windows.net/<container>/<blob>
//        
//        return 'http://'.$storage_account.'.blob.core.windows.net/'.$container.'/'.$blob_name;
//        
//    }

    /*
     * Returns all properties and metadata on the blob.
     * More information can be found at windows azure php sdk on blob rest proxy
     */
    
    public function getBlobProperties($container, $blob, $options = null){
        
        return  $this->_blobRestProxy->getBlobProperties($container, $blob, $options = null);
    }

    /*
     * Create a container in a storage
     */
    
    public function createContainer($container_name){
        // don't use this function, only for creation ( kind of developing / admin purpose)    
        // public read access
        if(empty($container_name)){
            return false;
            
        }
       
        // OPTIONAL: Set public access policy and metadata.
        // Create container options object.    
        // PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
        // CONTAINER_AND_BLOBS:     
        // Specifies full public read access for container and blob data.
        // proxys can enumerate blobs within the container via anonymous 
        // request, but cannot enumerate containers within the storage account.
        
        $createContainerOptions = new CreateContainerOptions(); 
        $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
        
        // Set container metadata
        $createContainerOptions->addMetaData("key1", "value1");
        $createContainerOptions->addMetaData("key2", "value2");
        
        try {
            // Create container.
            $result = $this->_blobRestProxy->createContainer($container_name, $createContainerOptions);
            
            return $result;
            
        }catch(ServiceException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here: 
            // http://msdn.microsoft.com/en-us/library/windowsazure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            error_log(__CLASS__ ." ".__METHOD__." ". $code.": ".$error_message);
            return false;
            
        }
        
    }    
  
}

/* End of file Wablobstorage.php */