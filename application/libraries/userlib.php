<?php

/*
 * User class , we can access user info through this class based on the user id
 * Fetch user through getUser function then we will access this class to get properties
 */

/**
 * Description of users
 */
class userlib {
    
    public $created_at; 
    public $id;
    public $username;    
    public $full_name;
    public $email;
    public $social_provider;
    public $social_userid;    
    public $profile_image_url;    
    public $city;    
    public $state;    
    public $twitter_handle;
    public $notify_special_offers;
    public $notify_product_announcements;
    public $notify_if_responds_for_my_post;    
    public $notify_if_responds_tweet;    
    public $no_of_posts;
    public $no_of_responses;
    
    
    public function __construct() {
       
        
    }
    
    public static function getUser($user_id){
        
        $user_data = self::getUserFromDB($user_id);
        
        $user_obj = new userlib();
        $user_obj->set($user_data);
        
        return $user_obj;
    }
    
    public static function getUserByTwitterHandle($t_handle){
        
        $user_data = self::getUserFromDbByTwitterHandle($t_handle);
        
        $user_obj = new userlib();
        $user_obj->set($user_data);
        
        return $user_obj;
    }    
    private static function getUserFromDB($user_id){
        $CI =& get_instance(); 
        // load user model        
        $CI->load->model('user_model'); 
        $users_obj = $CI->user_model->getUserProfileById($user_id);
        
        if(!isset($users_obj) || empty($users_obj)){
            return FALSE;
        }
        $user_obj = $users_obj[0];
        
        $data= array();
        $data['created_at']        = self::getDateFormatAsPerTwitter($user_obj->created_timestamp);
        $data['id']                = $user_obj->userid;
        $data['username']          = $user_obj->username;
        $data['full_name']         = $user_obj->name;
        $data['email']             = $user_obj->email;
        $data['social_provider']   = $user_obj->social_provider;
        $data['social_userid']     = $user_obj->social_userid;
        $data['profile_image_url'] = self::getProfileUrl($user_obj->avatar, $user_obj->social_provider);
        $data['city']              = $user_obj->city;
        $data['state']             = $user_obj->state;
        $data['twitter_handle']    = $user_obj->twitter_handle;        
        $data['notify_special_offers']          = $user_obj->notif_special;
        $data['notify_product_announcements']   = $user_obj->notif_product;
        $data['notify_if_responds_for_my_post'] = $user_obj->notif_post;
        $data['notify_if_responds_tweet']       = $user_obj->notif_tweet;
        $data['no_of_posts']       = $user_obj->no_of_posts; 
        $data['no_of_responses']   = $user_obj->no_of_responses;

        return $data;   
    }
    
    private static function getUserFromDbByTwitterHandle($t_handle){
        $CI =& get_instance(); 
        // load user model        
        $CI->load->model('user_model'); 
        $users_obj = $CI->user_model->getUserByTwitterHandle($t_handle);
        
        if(!isset($users_obj) || empty($users_obj)){
            return FALSE;
        }
        $user_obj = $users_obj[0];
        
        $data= array();
        $data['created_at']        = self::getDateFormatAsPerTwitter($user_obj->created_timestamp);
        $data['id']                = $user_obj->userid;
        $data['username']          = $user_obj->username;
        $data['full_name']         = $user_obj->name;
        $data['email']             = $user_obj->email;
        $data['social_provider']   = $user_obj->social_provider;
        $data['social_userid']     = $user_obj->social_userid;
        $data['profile_image_url'] = self::getProfileUrl($user_obj->avatar, $user_obj->social_provider);
        $data['city']              = $user_obj->city;
        $data['state']             = $user_obj->state;
        $data['twitter_handle']    = $user_obj->twitter_handle;        
        $data['notify_special_offers']          = $user_obj->notif_special;
        $data['notify_product_announcements']   = $user_obj->notif_product;
        $data['notify_if_responds_for_my_post'] = $user_obj->notif_post;
        $data['notify_if_responds_tweet']       = $user_obj->notif_tweet;
        $data['no_of_posts']       = $user_obj->no_of_posts; 
        $data['no_of_responses']   = $user_obj->no_of_responses;

        return $data;   
    } 
    
    // this function will prepare url for only email signup
    public static function getProfileUrl($avatar, $social_provider=1){
       
        if($social_provider==1){
            
            return STATIC_URL.$avatar;
        }
        return $avatar;
        
    }
    
    public static function getDateFormatAsPerTwitter($timestring){
        return date('D M d H:i:s O Y', strtotime($timestring));
    }
    
    
    public static function getRFCTimeFormat($timestring){
        // » RFC 2822 formatted date
        return date('r', strtotime($timestring));
    }
    
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
