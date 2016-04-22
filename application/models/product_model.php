<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This is class Product_model
 *
 */	
class Product_model extends CI_Model {
    
    const PRODUCT_CACHE_TIME = 600;  // 10*60 => 10 min

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
     * @param mixed $name This is a description
     * @return mixed This is the return value description
     * caching only for default
     *
     */ 				
    public function getProductDetails($name){
        
        if($name != FALSE) {
            $name	=	$this->db->escape($name);	// escape special charcters from name
            $sql = "SELECT id, name FROM product WHERE url LIKE $name";  
            $result = $this->db->query($sql);
            $product = $result->row_object();
        }else{
            if(MC){
                $product = $this->memcachesasl->get('default_product');
            }
            
            if(empty($product)){
                $sql = "SELECT id, name FROM product LIMIT 1";
                $result = $this->db->query($sql);
                $product = $result->row_object();
                
                if(MC){
                    $this->memcachesasl->set('default_product', $product, self::PRODUCT_CACHE_TIME);
                }
                
            }
        }
        
        
        // cache the component based on the product id
        if (isset($product->id)) {
            if(MC){
                $productCom = $this->memcachesasl->get('p_component_'.$product->id);
            }
            
            if(empty($productCom)){
                $this->db->select('prcom.*, prme.*');
                $this->db->join('product_media as prme', 'prme.product_component_id=prcom.id', 'left');
                $this->db->where('prcom.product_id', (int)$product->id);
                $this->db->where('prme.product_id', (int)$product->id);
                $query = $this->db->get('product_components as prcom');
                
                if (!$query->num_rows() > 0) {
                    return false;
                }else{ 
                    $productCom = $query->result();
                    if(MC){
                         $this->memcachesasl->set('p_component_'.$product->id, $productCom, self::PRODUCT_CACHE_TIME);
                    }                    
                }
            }
            return [$productCom, $product->name];
        }else{
            return FALSE;
        }
    }
}

/* End of file product_model.php */
/* Location: ./application/models/product_model.php */