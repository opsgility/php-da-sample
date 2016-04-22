<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * This is class News
	 *
	 */	
	class News extends CI_Controller {

		public function __construct() {
			parent::__construct();
		}
		
		public function show(){
			$this->load->helper(array('form', 'url')); 		
			$link =	current_url();
			
			$this->load->model('news_model');
			$feed = $this->news_model->getFeed($link);
			
			$data['title'] =  'Welcome :: '.WEBSITE_NAME.' :: '.$feed->title;
            
			$this->load->view('templates/header', $data);
			$this->load->view('templates/nav_bar');			

            $twitter['twitter_widget_panel'] =  $this->load->view('templates/twitter_widget', '', true);
            $data['sidebar_panel'] =  $this->load->view('templates/sidebar_panel', $twitter, true);
            
			$data['feed'] = $feed;
			$this->load->view('pages/news', $data);			
			$this->load->view('templates/footer');
		}
	}

/* End of file news.php */
/* Location: ./application/controllers/news.php */