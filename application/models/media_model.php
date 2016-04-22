<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of media_model
 *
 * @author Administrator
 */
class media_model extends CI_Model {
    //put your code here
    public function __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * This is method getMedia details
     *
     * Fetch media details based on the $postid
     * @return mixed This is the return value description
     *
     */        
    public function getMediaByPostId($postid){
        
        if(!isset($postid) || empty($postid)){
            return false;
        }
        
        $this->db->select('*');
        $this->db->from('media');
        $this->db->where('postid', $postid);        
        $query = $this->db->get();
        
        if (!$query->num_rows() > 0) {
            return false;
        }else{
            return $query->result();
        }
        
    }    
}

/* End of file media_model.php */
/* Location: ./application/models/media_model.php */