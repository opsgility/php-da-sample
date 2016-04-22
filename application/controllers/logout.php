<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Logout page
 */

class Logout extends CI_Controller {
    
    /**
	 * Logout Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/logout
	 *	- or -  
	 * 		http://example.com/index.php/logout/index
	 *	- or -
	 *
	 */
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->session->sess_destroy();
        session_start(); //to ensure you are using same session
        session_destroy(); //destroy the session
        $this->session->set_flashdata('message', 'Signed Out .');        
        
        header('cache-Control: no-store, no-cache, must-revalidate');
        header("cache-Control: post-check=0, pre-check=0", false);        
        header("Expires: Thu, 19 Nov 1981 08:52:00 GMT");        
        header("Pragma: no-cache");
        
        redirect('main/');         
    }
}

/* End of file logout.php */
/* Location: ./application/controllers/logout.php */