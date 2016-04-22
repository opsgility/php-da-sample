<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Community cron model
 */

/**
 * Description of communitycron_model
 *
 */
class Communitycron_model extends CI_Model {

    /**
     * This is method __construct
     *
     */		
    public function __construct(){
        
        parent::__construct();
        $this->load->database();
    }
    
    /*
     * In the DB table 
     * ID - 1 -> post table reference
     * ID - 2 -> response table reference 
     */
    public function updateLastProcessedId($processed_id, $table_ref_id) {
        
        $data = array('last_processed_id'=>$processed_id);
        $this->db->where('id', $table_ref_id);
        $result = $this->db->update('community_data_cron', $data);
        
        return $result;       
     
        
    }
    
    /*
     * In the DB table 
     * ID - 1 -> post table reference
     * ID - 2 -> response table reference 
     */
    public function getLastProcessedId($table_ref_id) {
        $this->db->select('last_processed_id');
        $this->db->from('community_data_cron');
        $this->db->where('id', $table_ref_id);
        $query = $this->db->get();

        if (!$query->num_rows() > 0) {
            return false;
        }else{
            return $query->result();
        }     
        
    }    
    
    
}
