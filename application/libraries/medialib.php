<?php

/*
 * Media class, we can access the media info
 */

/**
 * Description of medialib
 */
class medialib {
    
    public $exist;
    public $picture;
    public $video;
    public $name;
    public $description;
    public $url;
    
    /*
     * get media based on the post id
     */
    public static function getMedia($media_post_id, $media_present){
        
        $media_obj = new medialib();
        
        if(!isset($media_present) || empty($media_present)){
            $media_obj->set(array('exist'=>false));
            
        }else{
            $media_data = self::getMediaFromDB($media_post_id);     
            if(!$media_data){
                $media_obj->set(array('exist'=>false));
            }else{
                $media_data['exist'] = true;
                $media_obj->set($media_data);
            }
        }
        
        return $media_obj;
    } 
    
    private static function getMediaFromDB($media_post_id){
        $CI =& get_instance(); 
        // load user model        
        $CI->load->model('media_model'); 
        $media_data_objects = $CI->media_model->getMediaByPostId($media_post_id);
        
        if(!isset($media_data_objects) || empty($media_data_objects)){
            
            return FALSE;
        }
        $media_data_obj = $media_data_objects[0];
        
        $data= array();
        $data['picture']    = self::isPicture($media_data_obj->media_type);// self::getRFCTimeFormat($user_obj->created_timestamp);
        $data['video']      = self::isVideo($media_data_obj->media_type);
        $data['description']= $media_data_obj->media_description;
        $data['url']        = $media_data_obj->media_path;

        return $data;   
    }
    
    
    private static function isPicture($str){
        if(empty($str)){
            return false;
        }
        
        $arr = explode('/', $str);
        if($arr[0]==='image'){
            return true;
        }
        
        return false;
    }
    
    private static function isVideo($str){
        if(empty($str)){
            return false;
        }
        $arr = explode('/', $str);
        if($arr[0]==='video'){
            return true;
        }
        
        return false;        
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
