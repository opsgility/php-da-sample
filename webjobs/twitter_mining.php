<?php 

/*
 * Web jobs
 * Twitter mining.
 * data mining from the twitter
 * currently scheudle every 30 min.
 */

// twitter search API config
define('TWEETS_COUNT', 100); // The number of tweets to return per page, up to a maximum of 100. - for more https://dev.twitter.com/docs/api/1.1/get/search/tweets
define('TWEETS_LANG', 'en');

// set include path for pear packages - Windows Azure php SDK - this path is development site pear package path

$pathToRoot = getenv('HOME_EXPANDED') . '\\site\\wwwroot';
$pathToPear = getenv('HOME_EXPANDED') . '\\site\\wwwroot\\pear';
ini_set('include_path', PATH_SEPARATOR . $pathToPear);
set_include_path(get_include_path() . PATH_SEPARATOR . $pathToRoot);

require("cronhelper.php");
// require files
    require("inc/Twitteroauth.php");
    require("inc/Wablobstorage.php");

    
// load require config info. - load codeigniter  files to access config, lib, model, etc..
    
     $CI =& get_instance();
 
    // config
    define('STORAGE_ACCOUNT', $CI->config->item('t_mining_azure_storage_account')); 
    define('STORAGE_KEY', $CI->config->item('t_mining_azure_storage_key'));
    define('CONTAINER_NAME', $CI->config->item('t_mining_azure_container_name'));
    
    $CI->config->load('sn_config', TRUE);
    $sn_config = $CI->config->item('sn_config');

   
    define('TWITTER_CONSUMER_KEY', $sn_config['twitter_consumer_key']);
    define('TWITTER_CONSUMER_SECRET', $sn_config['twitter_consumer_secret']);
    define('TWITTER_ACCESS_TOKEN', $sn_config['twitter_access_token']);
    define('TWITTER_ACCESS_SECRET', $sn_config['twitter_access_secret']);     

    //list of hash tags or key words
    $hashtag_list = $CI->config->item('t_mining_hash_tag');  
    
    
// functions 

/*
 * Upload the tweets to windows azure
 */
    function processTweets($tweet_data){

        if(!is_array($tweet_data)){
            return false;
        } 
        

        $prefix  = 'digitalMarketing/data/twitter/twitter_';

        // Windows azure storage
        $wablob_config = array('STORAGE_ACCOUNT' => STORAGE_ACCOUNT, 'STORAGE_KEY'=>STORAGE_KEY);
        
        $wablobstorage = new Wablobstorage($wablob_config); 
        
        if(!isset($wablobstorage) || empty($wablobstorage)){
            error_log('wablobstorage failed -  ');
        }
        
        $container = CONTAINER_NAME ; // we are going to use this container for twitter data.

        $blob_name_prefix = $prefix.''.date('mdY',time()).'_'.date('His',time());

        foreach ($tweet_data as $key =>$jsondata){
            $blob_name = $blob_name_prefix.'_'.$key.'.txt';   

            // upload file
            $result = $wablobstorage->uploadBlob($container, $blob_name,  $jsondata);
            
            if(!$result){
                error_log('Upload failed - from twitter to blob storage '. $key);
            }
        }        

     }


/*
 * mine the twitter
 * twitter authentication
 * twitter search api
 */
     

    // create connection
    $twitteroauth = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_ACCESS_TOKEN, TWITTER_ACCESS_SECRET);
    
    if(!isset($twitteroauth) || empty($twitteroauth)){
        error_log('TwitterOAuth failed -  ');
        exit;
    }
    
    $tweet_data = array();    
          
    // load user model        
    $CI->load->model('user_model');
    $CI->load->library('userlib'); 
    // run foreach for all the tags|key words        
    foreach($hashtag_list as $key => $hashtag){

        $url = 'https://api.twitter.com/1.1/search/tweets.json?q="'.$hashtag.'"&count='.TWEETS_COUNT.'&lang="'.TWEETS_LANG.'"';

        $tweets_obj = $twitteroauth->get($url);
         // we are taking only statuses not search_metadata
        if($tweets_obj && !empty($tweets_obj) && isset($tweets_obj->statuses) && !empty($tweets_obj->statuses)){
            
            // we need every single object seperatly
            $tweets_json_obj = array();          
            
            foreach($tweets_obj->statuses as $tweets){  
                $tweets->microsite_user = '';
                // check if user exist or not in db.
                if(isset($tweets->user->screen_name) && !empty($tweets->user->screen_name)){
                    $user_obj = userlib::getUserByTwitterHandle($tweets->user->screen_name);                    
                    $tweets->microsite_user = $user_obj;
                }                
                $tweets_json_obj[] = json_encode($tweets);
               
                //echo '<pre>';
               // print_r($tweets);
               // echo $tweets->user->screen_name; 
                // exit;

            }
            
            if(!empty($tweets_json_obj)){
                
                $tweet_data[$hashtag] = join("\n", $tweets_json_obj);
                
            }

        }
    }

    // process or store in azure blob storage
    if(is_array($tweet_data) && !empty($tweet_data)){

        processTweets($tweet_data);
    }
   
 
     
?>