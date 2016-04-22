<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

      /**
       * This is class Discussion_model
       *
       */	
      class Discussion_model extends CI_Model {

          /**
           * This is method __construct
           *
           * @return mixed This is the return value description
           *
           */		
          public function __construct(){
              parent::__construct();
              $this->load->database();
              if(MC){
                  
                  $this->load->library('Memcachesasl');
                  $this->memcachesasl->addServer($this->config->item('mc_host'), $this->config->item('mc_port'));
                  $this->memcachesasl->setSaslAuthData($this->config->item('mc_username'), $this->config->item('mc_password'));
              }
              
              
          }

          /**
           * This is method getProductDetails
           *
           * @param mixed $data This is a description
           * @return mixed This is the return value description
           *
           */
          public function insertPost($data) {
              $title	=	$this->db->escape($data['title']);	// escape special charcters from title
              $content	=	$this->db->escape($data['content']);	// escape special charcters from content
              $userid	=	$this->db->escape($this->session->userdata('userid'));	// escape special charcters from userid
              $havemedia =  $this->db->escape($data['havemedia']);	// escape special charcters

              $query1	=	"INSERT INTO posts (title, content, userid, havemedia) VALUES ($title, $content, $userid, $havemedia)";
              
              // Find is current user data available in user_posts table
              $query2 = "SELECT id from user_posts WHERE userid=$userid";
              $result = $this->db->query($query2);
              
              // If not, generate INSERT query
              // else generate update query with self increment for no_of_posts field
              if ($result->num_rows() > 0){                  
                  $query3 = "UPDATE user_posts SET no_of_posts=no_of_posts+1 WHERE userid=$userid";
              } else {                  
                  $query3 = "INSERT INTO user_posts(userid, no_of_posts, no_of_responses) VALUES($userid, 1, 0)";
              }             
              
              $this->db->trans_start();

              $this->db->query($query1);

              $postid = $this->db->insert_id(); // get the post id
              
              $this->db->query($query3);

              $this->db->trans_complete();
              
              $flag = true;
              
              if ($this->db->trans_status() === FALSE){                  
                  $flag = false;
              }elseif($havemedia){
                  $data['postid'] = $postid;
                  
                  // call insert media method to insert media values
                  if(!$this->insertMedia($data)){                      
                      $flag = false;
                  }
              }
              
              if($flag){
                  $this->db->trans_commit();
                  return true;                  
              }else{
                  $this->db->trans_rollback();
                  return false;                  
              }              
          }
          
          /**
           * Summary of insertResponse
           * @param mixed $data 
           * @return mixed
           */
          public function insertResponse($data) {              
              $content	=	$this->db->escape($data['content']);	// escape special charcters from content
              $userid	=	$this->db->escape($this->session->userdata('userid'));	// escape special charcters from userid
              $postid	=	$this->db->escape($data['postid']);	// escape special charcters from postid
              $havemedia=  $this->db->escape($data['havemedia']);	// escape special charcters
              
              $query1	=	"INSERT INTO responses (content, postid, userid, havemedia) VALUES ($content, $postid, $userid, $havemedia)";
              
              // Find is current user data available in user_posts table
              $query2 = "SELECT id from user_posts WHERE userid=$userid";
              $result = $this->db->query($query2);
              
              // If not, generate INSERT query
              // else generate update query with self increment for no_of_responses field
              if ($result->num_rows() > 0) {
                  $query3 = "UPDATE user_posts SET no_of_responses=no_of_responses+1 WHERE userid=$userid";
              }else{                  
                  $query3 = "INSERT INTO user_posts(userid, no_of_responses, no_of_posts) VALUES($userid, 1, 0)";
              }
              
              // Find is current posts_id is available in post_responses table
              $query4 = "SELECT * from post_responses WHERE postid=$postid";
              $result = $this->db->query($query4);
              
              // If not, generate INSERT query
              // else generate update query with self increment for no_of_post_responses field
              if ($result->num_rows() > 0){
                  $query5 = "UPDATE post_responses SET no_of_post_responses=no_of_post_responses+1 WHERE postid=$postid";
              } else {
                  $query5 = "INSERT INTO post_responses(postid, no_of_post_responses) VALUES($postid, 1)";                  
              }              
              
              // Start Transaction 
              $this->db->trans_start();
              $this->db->query($query1);
              $responseid = $this->db->insert_id(); // get the response id
              $this->db->query($query3);
              $this->db->query($query5);
              // Complete Trnsaction
              $this->db->trans_complete();
              
              $flag = true;
              
              if ($this->db->trans_status() === FALSE){                  
                  $flag = false;
              }elseif($havemedia){                 
                  $data['responseid'] = $responseid;
                  unset($data['postid']); // unset postid to add response media details in media table
                  // call insert media method to insert media values
                  if(!$this->insertMedia($data)){
                      $flag = false;
                  }
              }

              if($flag){
                  // Commit Trnsaction
                  $this->db->trans_commit();
                  return true;                  
              }else{
                  // Roolback Trnsaction
                  $this->db->trans_rollback();
                  return false;                  
              }
          }      
          
          /**
           * Summary of insertMedia
           * @param string $data
           * @return mixed
           */
          private function insertMedia($data){
              
              $insertData = array(
                    'media_path'        =>  $data['mediaPath'],
                    'media_type'        =>  $data['mediaType'],
                    'media_description' =>  $data['mediaDescription'],
                    'file_name'         =>  $data['filename']
              );              
              if($data['havemedia'] == 2){
                  $insertData['asset_id']   = $data['assetId'];
                  $insertData['job_id']     = $data['jobId'];
                  $insertData['job_status'] = 0;                 
                  $insertData['locator_id'] = $data['locatorId'];
                  $insertData['sas_write_url']      = $data['sasWriteUrl'];
                  $insertData['access_policy_id']   = $data['accessPolicyId'];
              }


              if(isset($data['postid'])){
                  $insertData['postid']= $data['postid'];
                  $insertData['responseid']= 0;
              }elseif(isset($data['responseid'])){
                  $insertData['postid']= 0;
                  $insertData['responseid']= $data['responseid'];
              }
              
              $this->db->insert('media', $insertData);
              //error_log('Media Query'.$this->db->last_query());error_log('Last insert media id '.$this->db->insert_id());
              return $this->db->insert_id();
          }
          
          /**
           * Summary of insert job id in media table based on asset id
           * @param string $assetId
           * @param string $sasJobId
           * @return boolean based on affected rows
           */
          public function insertJobId($assetId, $sasJobId){
             $data = array('job_id' => $sasJobId);
             $this->db->where('asset_id', $assetId);
             $this->db->update('media', $data);
             return ($this->db->affected_rows() == 0) ? false : true;
          }
          
          /**
           * Summaray of get Locator id and Access Policy id using Asset Id
           * @param string $assetId
           * @return array of locator and access policy id
           */
          public function getLocatorIdAndAccessPolicyIdByAssetId($assetId){
            $this->db->select('locator_id, access_policy_id');
            $this->db->where('asset_id', $assetId);
            $this->db->from('media');
            $query = $this->db->get();
             
            //echo '<pre>';echo $this->db->last_query();echo '<pre>';die;
            
            return (!$query->num_rows() > 0) ? false :$query->result();
          }          
          
          /**
           * This is method View Posts
           * @param string $filter
           * @param int $offset
           * @param int $limit
           * @return boolean This is the return value description
           * only cache community main page
           */
          public function viewPosts($filter, $offset=0, $limit=10){
              
              if(empty($filter) && empty($offset)){
                  $vposts = '';
                if(MC){
                    // get from mc
                    $vposts = $this->memcachesasl->get('default_vposts');
                }
                
                if(empty($vposts)){
                    $this->db->select('u.userid as userid, u.name as userName, u.avatar as userAvatar, u.social_provider as userSocialProvider, p.id as postId, p.title as postTitle, p.content as postContent, p.post_timestamp as postDate, up.no_of_posts as userNoOfPosts, ps.no_of_post_responses as noOfResponses');
                    $this->db->from('posts as p');
                    $this->db->join('users as u', 'p.userid = u.userid');
                    $this->db->join('user_posts as up', 'up.userid = u.userid');
                    $this->db->join('post_responses as ps', 'ps.postid = p.id', 'left');
                    $this->db->order_by('p.post_timestamp', 'DESC');
                    $this->db->limit($limit, $offset); // (offset, limit)
                    $query = $this->db->get();

                    //echo '<pre>';echo $this->db->last_query();echo '<pre>';var_dump($query);die;
                    if (!$query->num_rows() > 0) {
                        return false;
                    }else{
                        $vposts = $query->result();
                        if(MC){
                            $this->memcachesasl->set('default_vposts', $vposts, 120); // 2 min cache
                        }                        
                     
                    }
                }
                
                return $vposts;
              }

              
              $this->db->select('u.userid as userid, u.name as userName, u.avatar as userAvatar, u.social_provider as userSocialProvider, p.id as postId, p.title as postTitle, p.content as postContent, p.post_timestamp as postDate, up.no_of_posts as userNoOfPosts, ps.no_of_post_responses as noOfResponses');
              $this->db->from('posts as p');
              $this->db->join('users as u', 'p.userid = u.userid');
              $this->db->join('user_posts as up', 'up.userid = u.userid');
              $this->db->join('post_responses as ps', 'ps.postid = p.id', 'left');
              if(!empty($filter)){
                  $this->db->where('p.havemedia', $filter);
              }
              $this->db->order_by('p.post_timestamp', 'DESC');
              $this->db->limit($limit, $offset); // (offset, limit)
              $query = $this->db->get();
             
              //echo '<pre>';echo $this->db->last_query();echo '<pre>';var_dump($query);die;
              if (!$query->num_rows() > 0) {
                  return false;
              }else{
                  //print_r($query->result());die;
                  return $query->result();
              }
              //echo $this->db->last_query();exit;              
          }

          /**
           * This is method countPosts details
           * @param string $filter
           * @return will be count
           */
          public function countPosts($filter){
              $this->db->from('posts as p');
              $this->db->join('users as u', 'p.userid = u.userid');
              $this->db->join('user_posts as up', 'up.userid = u.userid');
              $this->db->join('post_responses as ps', 'ps.postid = p.id', 'left');
              if(!empty($filter)){
                  $this->db->where('p.havemedia', $filter);
              }
              return $this->db->count_all_results();
          }

          
          /**
           * This is method getResponses details
           * @param int $id
           * @param string $filter
           * @param int $offset
           * @param int $limit
           * @return mixed This is the return value description
           */
          public function getResponses($id=0, $filter, $offset=0, $limit=10){             
              $this->db->select('u.userid as userid, u.name as userName, u.avatar as userAvatar, u.social_provider as userSocialProvider, r.id as rid, r.content as rContent, r.response_timestamp as responseDate, r.havemedia as rHaveMedia, m.media_type as mediaType, m.media_path as mediaPath, m.job_status as mediaJobStatus, m.media_description as mediaDescription');

              $this->db->from('responses as r');
              $this->db->join('users as u', 'r.userid = u.userid');
              $this->db->join('media as m', 'm.responseid = r.id', 'left');              
              $this->db->where('r.postid', $id);
              if(!empty($filter)){
                  $this->db->where('r.havemedia', $filter);
              }              
              $this->db->order_by('r.response_timestamp', 'ASC');
              $this->db->limit($limit, $offset); // (offset, limit)
              $query = $this->db->get();
              
              //echo '<pre>';echo $this->db->last_query();echo '<pre>';var_dump($query);die;
              if (!$query->num_rows() > 0) {
                  return false;
              }else{
                  //print_r($query->result());die;
                  return $query->result();
              }
              //echo $this->db->last_query();exit;
          }          
          
          
          /**
           * This is method countResponses will count all the responses to the to that discussion
           * @param int $id
           * @param string $filter
           * @return will be count
           */
          public function countResponses($id=0, $filter){              
              $this->db->from('responses as r');
              $this->db->join('users as u ', 'r.userid = u.userid');
              $this->db->where('r.postid', $id);
              if(!empty($filter)){
                  $this->db->where('r.havemedia', $filter);
              }              
              return $this->db->count_all_results();
          }

          /**
           * This is method getDiscussion details
           *
           * Fetch user details who started this discussion
           * @param int $id
           * @return mixed This is the return value description
           *
           */        
          public function getDiscussion($id){              
              $this->db->select('p.id as postId, p.title as postTitle, p.content as postContent, p.userid as postUserid, p.havemedia as postHavemedia, p.post_timestamp as postTimestamp, m.id as postMediaId, m.media_path as postMediaPath, m.job_status as postMediaJobStatus, m.media_type as postMediaType, m.media_description as postMediaDescription, m.postid as mediaPostId, m.responseid as postResponseId, m.media_timestamp as postMediaTimestamp, pr.no_of_post_responses as noOfPostResponses');
              $this->db->join('post_responses as pr', 'pr.postid=p.id', 'left');
              $this->db->join('media as m', 'm.postid = p.id', 'left');              
              $this->db->where('p.id', $id);              
              $query = $this->db->get('posts as p');
              
              //echo '<pre>';echo $this->db->last_query();echo '<pre>';var_dump($query);die;
              if (!$query->num_rows() > 0) {
                  return false;
              }else{
                  return $query->result();
              }
          }
          
          /**
           * Summary of getCountUserPostsAndResponse
           * Get count of posts and responses of selected user
           * @param mixed $userid 
           * @return mixed
           */
          public function getCountUserPostsAndResponse($userid){
              $this->db->select('u.name as userName, u.avatar as userAvatar, u.social_provider as userSocialProvider, up.no_of_posts as noOfPosts, up.no_of_responses as noOfResponses');
              $this->db->join('user_posts as up', 'up.userid = u.userid');
              $this->db->where('u.userid', $userid);
              $query = $this->db->get('users as u');
              
              //echo '<pre>';echo $this->db->last_query();echo '<pre>';var_dump($query);die;
              if (!$query->num_rows() > 0) {
                  return false;
              }else{
                  return $query->result();
              }              
          }
      
          /**
           * Get Latest post
           * this function won't support pagination
           * Default will return latest 5 posts
           * @param int $limit default 5
           * @return mixed This is the return value description
           *
           */
          public function getLatestPosts($limit=5){
              
              $latestPosts = '';
             
              if( MC ){
                  $latestPosts = $this->memcachesasl->get('latest_posts');
              }
              if(empty($latestPosts)){
                $this->db->select('p.id as postId, p.title as postTitle, p.content as postContent, p.post_timestamp as postDate, pr.no_of_post_responses as noOfPostResponses');
                $this->db->join('post_responses as pr', 'pr.postid=p.id', 'left');
                $this->db->order_by('p.post_timestamp', 'DESC');              
                $this->db->limit($limit, 0); // (offset, limit)
                $query = $this->db->get('posts as p');
                //echo '<pre>';echo $this->db->last_query();echo '<pre>';var_dump($query->result());die;              

                if (!$query->num_rows() > 0) {
                    $latestPosts = false;
                }else{
                    $latestPosts = $query->result();
                    if(MC){
                       $this->memcachesasl->set('latest_posts', $latestPosts, 120);  // expire time 2 min
                    }
                   
                }                 
                  
              }

              return $latestPosts;
                 
          }

          /**
           * @param $post_id
           * @return bool
           */
          public function getPost($post_id){              
              if(empty($post_id)){
                  return false;
              }             
          }
          
          /**
           * Summary of getMediaJobIds
           * @return mixed
           */
          public function getMediaJobIds(){
              $this->db->select('id, media_path, asset_id, job_id, file_name');
              $this->db->from('media');
              $this->db->where('job_status = 0');
              $this->db->where("job_id NOT LIKE '0%'");
              $query = $this->db->get();
              
              if (!$query->num_rows() > 0) {
                  return false;
              }else{                 
                  return $query->result();
              }              
          }
          
          public function getIncompletedJobIds(){
              return $this->getMediaJobIds(); 
          }          
          
          /**
           * Summary of updateMediaJobStatus
           * Update media job status and media url path based on jobid and media table row id
           * @param int $id
           * @param mixed $mediapath {Video url to play in browser}
           * @return boolean
           */
          public function updateMediaJobStatus($id, $mediapath){
              $data = array('media_path' => $mediapath, 'job_status' => 1);
              $this->db->where('id', $id);              
              $this->db->update('media', $data);
              return ($this->db->affected_rows() > 0);
          } 

                    
          /**
           * This is method postUpdate
           *
           * @param array $data This is a description
           * @return mixed This is the return value description
           *
           */	
          public function postUpdate($data=array()){
              $postid = $data['id'];
              
              unset($data['id']);

              if(count($data) > 0) {               
                  return $this->db->update('posts', $data, array('id' => $postid));
              }else {
                  return false;
              }
          }  

          /**
           * This is method responseUpdate
           *
           * @param array $data This is a description
           * @return mixed This is the return value description
           *
           */	
          public function responsesUpdate($data=array()){
              $responsesid = $data['id'];
              
              unset($data['id']);

              if(count($data) > 0) {               
                  return $this->db->update('responses', $data, array('id' => $responsesid));
              }else {
                  return false;
              }
          }  

          /**
           * This is method getPosts
           * @param int $condition_id,
           * @param int $limit
           * get records greater than $condition_id
           * @return mixed This is the return value description
           *
           */
          public function getPostsForDataProcessing($condition_id, $limit=10){
              $this->db->select('p.id as postId, p.title as postTitle, p.content as postContent, p.userid as postUserid, p.havemedia as postHavemedia, p.post_timestamp as postTimestamp, m.id as postMediaId, m.media_path as postMediaPath, m.media_type as postMediaType, m.media_description as postMediaDescription, m.postid as mediaPostId, m.responseid as postResponseId, m.media_timestamp as postMediaTimestamp, pr.no_of_post_responses as noOfPostResponses');
              $this->db->join('post_responses as pr', 'pr.postid=p.id', 'left');
              $this->db->join('media as m', 'm.postid = p.id', 'left');
              $this->db->order_by('p.id', 'ASC');
              $this->db->where('p.id > ', $condition_id);
              $this->db->limit($limit, 0); // (offset, limit)
              $query = $this->db->get('posts as p');

              if (!$query->num_rows() > 0) {
                  return false;
              }else{
                  return $query->result();
              }              

          }
          
          /**
           * This is method getResponses
           * @param int $condition_id,
           * @param int $limit
           * get records greater than $condition_id
           * @return mixed This is the return value description
           *
           */
          public function getResponsesForDataProcessing($condition_id, $limit=10){
              
              $this->db->select('r.id as rid, r.content as rContent,r.postid as rpostId,r.userid as rUserid, r.havemedia as rHaveMedia, r.response_timestamp as responseDate, m.media_type as mediaType, m.media_path as mediaPath, m.media_description as mediaDescription');
              $this->db->join('media as m', 'm.responseid = r.id', 'left');
              $this->db->order_by('r.id', 'ASC');
              $this->db->where('r.id > ', $condition_id);
              $this->db->limit($limit, 0); // (offset, limit)
              $query = $this->db->get('responses as r');
              //echo '<pre>';echo $this->db->last_query();echo '<pre>';var_dump($query);die;
              if (!$query->num_rows() > 0) {
                  return false;
              }else{
                  return $query->result();
              }               
               
          } 
          
          public function isTableExist($tablename){
              if(empty($tablename)){
                 return false; 
              }
              $sql = "SHOW TABLES LIKE '".$tablename."'";
              $result = $this->db->query($sql);
              
              if($result->num_rows() > 0){
                  return true;
              }else{
                  return false;
              }
              
          }
          /**
           * Summary of updateMedia 
           * Update media row
           * @param int $id
           * @param mixed $mediapath {Video url to play in browser}
           * @return boolean
           */
          public function updateMediaById($id, $data){
             if(empty($data)){
                 return false;
             }
              $this->db->where('id', $id);              
              $this->db->update('media', $data);
              return ($this->db->affected_rows() > 0);
          }
          
          public function getMediaByIdsForJobStatus($mediaIds){
              
              if(empty($mediaIds) || !is_array($mediaIds)){
                  return false;
              }
              $mediaIdStr = implode(',', $mediaIds);
              
              $this->db->select('id, media_path, asset_id, job_id, job_status, file_name');
              $this->db->from('media');             
              $this->db->where("id IN (".$mediaIdStr.") ");
              $query = $this->db->get();              
              //echo '<pre>';echo $this->db->last_query();echo '<pre>';
              
              if (!$query->num_rows() > 0) {
                  return false;
              }else{                 
                  return $query->result();
              }              
          }            
          
        }
      

      /* End of file discussion_model.php */
      /* Location: ./application/models/discussion_model.php */