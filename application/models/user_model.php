<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
      
      /**
       * This is class User_model
       *
       */
      class User_model extends CI_Model {
          
          public function __construct(){
              // Call the Model constructor
              parent::__construct();
          }

          /**
           * This is method UserInsert
           *
           * @param mixed $data This is a description
           * @return mixed This is the return value description
           *
           */		
          function userInsert($data=array()){

              $username       = $this->db->escape($data['username']);
              $email          = $this->db->escape($data['email']);
              $password       = $this->db->escape($data['password']);
              $social_userid  = $this->db->escape($data['social_userid']);
              $social_provider= $this->db->escape($data['social_provider']);
              $social_token   = $this->db->escape($data['social_token']);
              $tokens         = $this->db->escape($data['tokens']);
              $name           = $this->db->escape($data['name']);	
              $avatar         = $this->db->escape($data['avatar']);
              $city           = $this->db->escape($data['city']);
              $state          =$this->db->escape($data['state']);
              $twitter_handle = $this->db->escape($data['twitter_handle']);
              $notif_special  = $this->db->escape($data['notif_special']);
              $notif_product  = $this->db->escape($data['notif_product']);
              $notif_post     = $this->db->escape($data['notif_post']);
              $notif_tweet    = $this->db->escape($data['notif_tweet']);
              
              $query="INSERT INTO users (username, email, password, social_provider, social_userid, social_token, tokens, name, avatar, city, state, twitter_handle, notif_special, notif_product, notif_post, notif_tweet, created_timestamp) VALUES ($username, $email, $password, $social_provider, $social_userid, $social_token, $tokens, $name, $avatar, $city, $state, $twitter_handle, $notif_special, $notif_product, $notif_post, $notif_tweet, now())";

              //error_log('q '.$query);
              // exit;
              
              return $this->db->query($query);
          }
          
          /**
           * This is method update
           *
           * @param mixed $data This is a description
           * @param mixed $dataCond This is a description
           * @return mixed This is the return value description
           *
           */	
          function userUpdate($data=array()){  	            
              $userid = $data['userid'];
              
              unset($data['userid']);

              if(count($data) > 0) {               
                  return $this->db->update('users', $data, array('userid' => $userid));
              }else {
                  return false;
              }
          }         
          
          /**
           * This is method get User By Email
           *
           * @param mixed $data This is a description
           * @param mixed $sn_id is social network identifier (email, fb, google, twitte, etc..) This is a description   		
           * @return mixed This is the return value description
           *
           */	
          function getUserByEmail($email, $sn_id){               
              if(empty($sn_id) || empty($email)){
                  return false;
              }
              
              $email  =	$this->db->escape($email);	// escape special charcters            
              $query =    "SELECT * FROM users WHERE email=$email AND social_provider=$sn_id";            
              $result   =   $this->db->query($query);           
              
              return $result->result_object();
          }
          
          /**
           * This is method get user Profile
           *
           * @param mixed $email This is a description
           * @return mixed This is the return value description
           * function to redirect user profile page for setting neccessary fields like
           * Twitter profile
           * UserName
           * whether interested to get emails
           *
           */		
          function getUserProfileById($userid){
              if(empty($userid)){
                  return false;
              }
              $this->db->select('*, u.userid as userid');
              $this->db->from('users as u');
              $this->db->join('user_posts as up', 'up.userid = u.userid', 'left');
              $this->db->where('u.userid', $userid);
              $query = $this->db->get();
              
              //echo '<pre>';echo $this->db->last_query();echo '<pre>';var_dump($query);die;
              if (!$query->num_rows() > 0) {
                  return false;
              }else{
                  //print_r($query->result());die;
                  return $query->result();
              }           
          }
          
          /**
           * This is method get user Profile
           *
           * @param mixed $email This is a description
           * @return mixed This is the return value description
           * function to redirect user profile page for setting neccessary fields like
           * Twitter profile
           * UserName
           * whether interested to get emails
           *
           */		
          function getUserByTwitterHandle($t_handle){
              if(empty($t_handle)){
                  return false;
              }
              $this->db->select('*');
              $this->db->from('users as u');
              $this->db->join('user_posts as up', 'up.userid = u.userid', 'left');
              $this->db->where('u.twitter_handle', $t_handle);
              $query = $this->db->get();
              
              //echo '<pre>';echo $this->db->last_query();echo '<pre>';var_dump($query);die;
              if (!$query->num_rows() > 0) {
                  return false;
              }else{
                  //print_r($query->result());die;
                  return $query->result();
              }           
          }          
          
          /**
           * This is method getUserBySN
           *
           * @param mixed $social_userid This is a description
           * @param mixed $social_provider This is a description
           * @return mixed This is the return value description
           * Get user based on the social_userid and social_provider
           *
           */		
          function getUserBySN($social_userid, $social_provider){
              if(empty($social_userid) || empty($social_provider)){
                  return false;
              }
              
              $sql = "SELECT userid, username FROM users WHERE social_userid = '".$social_userid."' AND social_provider='".$social_provider."'";	
              $result = $this->db->query($sql);
              return $result->result_object();   			
          }

          /**
           * This is method signin
           *
           * @param mixed $data This is a description
           * @return mixed This is the return value description
           *
           */						
          public function signin($data){			
              $email	=	$this->db->escape($data['email']);
              $password	=	$this->db->escape(md5($data['password']));
              
              $sql = "SELECT userid FROM users WHERE email=".$email." AND password=".$password;              
              //error_log('user sign in '.$sql);
              $result = $this->db->query($sql);              
              
              if (!$result->num_rows() > 0) {
                  return false;
              }else{
                  return $result->result();
              }			
          }
          
          /**
           * This is method getToken
           *
           * @param $userid This is a description
           * @return mixed This is the return value description
           *
           */	        
          public function getToken($userid){
              $sql = "SELECT tokens FROM users WHERE userid=".$userid;
              $result = $this->db->query($sql);
              return $result->result_object();             
          }
          
          /**
           * This is method update Password
           *
           * @param $password This is a description
           * @return mixed This is the return value description
           *
           */
          public function updatePassword($password, $userid){
              $data = array('password' => md5($password), 'tokens'=>'');
              $this->db->where('userid', $userid);
              $this->db->update('users', $data);
              error_log('Password changes successfully '.$userid); 
              return true;
          }
           
          /**
           * This is method get top users based on posts
           *
           * @param $count limit users
           * @return mixed This is the return value description
           *
           */
           public function getTopUsers($count){              
              $this->db->select('u.name as name, up.no_of_posts as noOfPosts');
              $this->db->join('user_posts as up', 'up.userid = u.userid');
              $this->db->order_by('up.no_of_posts', 'desc');
              $this->db->limit($count);
              $query = $this->db->get('users as u');
              
              //echo '<pre>';echo $this->db->last_query();echo '<pre>';var_dump($query);die;
              if (!$query->num_rows() > 0) {
                  return false;
              }else{
                  //print_r($query->result());die;
                  return $query->result();
              }
           }
      }
?>