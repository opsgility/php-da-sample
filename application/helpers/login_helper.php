<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Mahesh Babu Bokkisam
 * custom login helper
 */

/**
 * Is Logged In
 *
 * Returns boolean
 * function is placed
 *
 * @access	public
 * @return	boolean
 */
if ( ! function_exists('is_logged_in')){
    function is_logged_in(){
        // Get current CodeIgniter instance
        $CI =& get_instance();
        
        //$CI->session->sess_destroy(); // destroy all sessions
        
        //echo "<pre>";print_r($CI->session->all_userdata());echo "</pre>";
        // We need to use $CI->session instead of $this->session
        $user = $CI->session->userdata('user_data');
        return TRUE;        
    }
}
?>