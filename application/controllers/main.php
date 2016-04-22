<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
            
            // check DB table are exist or not. 
            $this->load->model('discussion_model');
            $result = $this->discussion_model->isTableExist('user_posts');
            if(!$result){
                // redirect to install - first time installation
                redirect('startinstall/');
            }
           
            // Load News model
            $this->load->model('news_model');			
            $channel['items'] = $this->news_model->getNews(5); // get latest 5 news
            $data['news_panel'] =  $this->load->view('templates/news_panel', $channel, true);
            
            
            //$post_pannel['posts'] = $this->discussion_model->viewPosts('', 0,5); // get latest 5 posts
            $post_pannel['posts'] = $this->discussion_model->getLatestPosts(5); // get latest 5 posts
            $data['posts_panel'] =  $this->load->view('templates/posts_panel', $post_pannel, true); 
            
            $data['title'] =  'Welcome '.WEBSITE_NAME;
            
            $data['twitter_widget_panel'] =  $this->load->view('templates/twitter_widget', '', true);
            
            $data['home_page'] = true;  // To show innerpage_banner image in templates/nav_bar.php page for other than home page
            $this->load->view('templates/header', $data);
            $this->load->view('templates/nav_bar', $data);
            $this->load->view('pages/home', $data);
            $this->load->view('templates/footer', $data);
		//$this->load->view('pages/main_page');
	}
        
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */