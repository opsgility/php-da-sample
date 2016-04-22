<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * This is class News
	 *
	 */	
	class Showmedia extends CI_Controller {
        public function Showmedia(){
            parent::__construct();
        }
		
        public function index(){
           if (isset($_COOKIE["mediaPath"]) && !empty($_COOKIE["mediaPath"])){
            $header['title'] =  'Welcome :: '.WEBSITE_NAME;
            $data =   array();
            $this->load->view('templates/header', $header);
            $this->load->view('templates/nav_bar', $data);

                $twitter['twitter_widget_panel'] =  $this->load->view('templates/twitter_widget', '', true);
                $data['sidebar_panel'] =  $this->load->view('templates/sidebar_panel', $twitter, true);

                $data['mediaPath']          =   $_COOKIE["mediaPath"];            
                $data['mediaDescription']   =   $_COOKIE["mediaDescription"];
                        
            $this->load->view('pages/show_media', $data);
            $this->load->view('templates/footer', $data);
            }else{
                header('Location: '.base_url());
            }            
        }
    }

/* End of file Showmedia.php */
/* Location: ./application/controllers/Showmedia.php */