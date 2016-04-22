<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * This is class Product controller
	 *
	 */
	class Product extends CI_Controller {
    
        public function Product(){
            parent::__construct();
            parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
            $this->load->model('product_model');
        }

		/**
		 * This is method index
		 *
		 * @param mixed $product This is a description
		 * @return mixed This is the return value description
		 * Maps to the following URL
		 * http://example.com/product
		 * Load  specific product page
		 *
		 */
		public function index($product = NULL){

			

            if($product != NULL){
			    $prdetails = $this->product_model->getProductDetails($product);
            }else{
                $prdetails = $this->product_model->getProductDetails(FALSE);
            }

			// If product details return false
			if($prdetails == false){
				$data['prname'] = '<span style=\'color:red;\'>Please check the product name</span>';
			}else{
				//echo '<pre>';print_r($prdetails);echo '</pre>';die();
				$data['productdetails'] = $prdetails[0];
				$data['prname'] = $prdetails[1];
			}

			$this->load->library('twitterwidget');

			$data['title'] =  'Welcome :: '.WEBSITE_NAME.' :: '.$product;

            $twitter['twitter_widget_panel'] =  $this->load->view('templates/twitter_widget', '', true);
            $data['sidebar_panel'] =  $this->load->view('templates/sidebar_panel', $twitter, true);            
            
			$this->load->view('templates/header', $data);
			$this->load->view('templates/nav_bar');
            $this->load->view('pages/product', $data);
			$this->load->view('templates/footer');
		}
	}

/* End of file product.php */
/* Location: ./application/controllers/product.php */