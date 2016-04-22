<?php
/*
 * Community mining Page for HDInsight.
 * get the data from mysql DB, then convert to JSON format and store in the Azure blob 
 */ 

// set the path - production
$pathToRoot = getenv('HOME_EXPANDED') . '\\site\\wwwroot';
$pathToPear = getenv('HOME_EXPANDED') . '\\site\\wwwroot\\pear';
set_include_path(get_include_path() . PATH_SEPARATOR . $pathToRoot);
set_include_path(get_include_path() . PATH_SEPARATOR . $pathToPear);

require("cronhelper.php");
  

class CommunityMining {
    
    const NUMBER_OF_RECORDS_PER_TIME = 50;
    // Upto what number(id) is processed - it is based on the database table community_data_cron 
    const POST_TABLE_REF        = 1; // please don't change this value
    const RESPONSE_TABLE_REF    = 2; // please don't change this value 
    
    public $storage_account;
    public $storage_key;
    public $container_name;
            
            
    
    public function index() {
        
        $CI =& get_instance(); 
        // load user model        
        $CI->load->model('discussion_model');
        $CI->load->model('communitycron_model');
        
        $CI->load->library('medialib');
        $CI->load->library('userlib'); 
        
        // load azure storage info
        $this->storage_account  = $CI->config->item('t_mining_azure_storage_account');
        $this->storage_key      = $CI->config->item('t_mining_azure_storage_key');
        $this->container_name   = $CI->config->item('t_mining_azure_container_name');
                
        $last_processed_records = $CI->communitycron_model->getLastProcessedId(self::POST_TABLE_REF);
        
        if(empty($last_processed_records)){
           error_log('last_processed_records is failed') ;
           exit;
        }
        
      
        $last_processed_id = $last_processed_records[0]->last_processed_id;
        
        $discussions = $CI->discussion_model->getPostsForDataProcessing($last_processed_id, self::NUMBER_OF_RECORDS_PER_TIME);
         
        if(!empty($discussions)){
            
            foreach($discussions as $discussion){
                 // field list array
                $data= array();
                $data['created_at'] = self::getDateFormatAsPerTwitter($discussion->postMediaTimestamp);
                $data['id'] = $discussion->postId;
                $data['title'] = $discussion->postTitle;
                $data['content'] = $discussion->postContent;
                
                $media_obj = medialib::getMedia($discussion->postId, $discussion->postHavemedia);
                $data['media'] = $media_obj;
                $data['parent_id'] = 0;
                $data['response_count'] = $discussion->noOfPostResponses;
                
                $user_obj = userlib::getUser($discussion->postUserid);
                $data['user'] = $user_obj;
                $comm_obj = new communityjson();
                $comm_obj->set($data);
                
                $json_obj_array[] = json_encode($comm_obj);
            }
           
            // store the last processed record .
            $last_processed_records = $CI->communitycron_model->updateLastProcessedId($discussion->postId, self::POST_TABLE_REF);

            $this->processData($json_obj_array, 'post');
            
            // store in db the last record id.
            
        }else{
             error_log('No New posts') ;
        }  
       
        // Community responses
        // get the last processed id.
        $last_processed_records_r = $CI->communitycron_model->getLastProcessedId(self::RESPONSE_TABLE_REF);
        if(empty($last_processed_records_r)){
           error_log('last_processed_records is failed') ;
           exit;
        }
        
        $last_processed_id_r = $last_processed_records_r[0]->last_processed_id;
        
        $responses = $CI->discussion_model->getResponsesForDataProcessing($last_processed_id_r, self::NUMBER_OF_RECORDS_PER_TIME);
        if(!empty($responses)){
            $json_obj_array = array();
            foreach($responses as $response){
                 // field list array
                $data= array();
                $data['created_at'] = self::getDateFormatAsPerTwitter($response->responseDate);
                $data['id'] = $response->rid;
                $data['title'] = ''; // no title in the response
                $data['content'] = $response->rContent;
                
                $media_obj = medialib::getMedia($response->rid, $response->rHaveMedia);
                $data['media'] = $media_obj;
                $data['parent_id'] = $response->rpostId;
                $data['response_count'] = '';
                
                $user_obj = userlib::getUser($response->rUserid);
                $data['user'] = $user_obj;
                $comm_obj = new communityjson();
                $comm_obj->set($data);
                
                $json_obj_array[] = json_encode($comm_obj);
            }
            
            // store the last processed record .
           // echo $discussion->postId ."\n";
            $processed_record = $CI->communitycron_model->updateLastProcessedId($response->rid, self::RESPONSE_TABLE_REF);
           // print_r($processed_record);
           // exit;
            $this->processData($json_obj_array, 'response');
            
            // store in db the last record id.
            
        }
        
        
        
    }
    
    public function processData($community_data, $datafrom){
        
        if(!is_array($community_data) || empty($datafrom)){
            return false;
        }
        
         $jsondata = join("\n", $community_data );
        
// file format        /digitalMarketing/data/microsite/microsite__2014-04-15_18:41:18_skate.txt

        $prefix  = 'digitalMarketing/data/microsite/microsite_';

        // Windows azure storage
        $wablob_config = array('STORAGE_ACCOUNT' => $this->storage_account, 'STORAGE_KEY'=> $this->storage_key);
        $CI =& get_instance(); 
        $CI->load->library('wablobstorage', $wablob_config);
        
        if(!isset($CI->wablobstorage) || empty($CI->wablobstorage)){
            error_log('wablobstorage failed -  ');
        }
        
        $container = $this->container_name ; // we are going to use this container for microsite data.

        $blob_name = $prefix.''.date('mdY',time()).'_'.date('His',time()).'_'.$datafrom.'.txt'; 

        // upload file
        $result = $CI->wablobstorage->uploadBlob($container, $blob_name,  $jsondata);
       
        if(!$result){
            error_log('Upload failed - from microsite to blob storage '. $datafrom);
        }
        
    }
    
    public static function getRFCTimeFormat($timestring){
        // » RFC 2822 formatted date
        return date('r', strtotime($timestring));
    }
    public static function getDateFormatAsPerTwitter($timestring){
        return date('D M d H:i:s O Y', strtotime($timestring));
    }    
}



/*
 * Preparing the class for JSON format objects
 * discussion informations
 * user informations
 * media informations
 * this class hold all the above information
 * just works as a wrapper class
 */
class communityjson{
    
    public $created_at;
    public $id;
    public $title;
    public $content;
    public $media;
    public $parent_id;
    public $response_count;
    public $user;  
    
    public function get($prop_name){
        
        if(property_exists($this, $prop_name)){
            return $this->$prop_name;
        }
        
        return false;        
    }
    
    public function set($data_array){
       if(!isset($data_array) || empty($data_array)){
           return FALSE;
       }
       
       foreach($data_array as $prop_name => $prop_value){
           if(property_exists($this, $prop_name)){
               $this->$prop_name = $prop_value;
           }
       }
       
       return true;
    }
    
}




$obj = new CommunityMining();
$obj->index();
exit;

?>