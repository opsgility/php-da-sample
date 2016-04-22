<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    /*
     * users Page for this controller.
     */

class Users extends CI_Controller {
    // Social Providers
    const OWN_EMAIL = 1;

    const FACEBOOK  = 2;
    const TWITTER   = 3;
    const MICROSOFT = 4;
    const GOOGLE    = 5;

    const VERIFIED  = 'verified';
    const LOGGED_IN = 'logged_in';
    
    const UPLOADS_DIR = 'upload_user_profile';
    private $_cloud_user_avatar_container;
    
    public static $site_title;

    public function __construct() {
        parent::__construct();
        parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
        
        $this->load->model('user_model'); // Load User Model
        
        $this->load->library('session');
        // config
        $this->config->load('sn_config', TRUE);
        $this->sn_config = $this->config->item('sn_config');
        
        self::$site_title = 'Welcome :: '.WEBSITE_NAME;
        
        // load azure credentials from config
              
        // We are going to use this container for storing images.
        $this->_cloud_user_avatar_container   = $this->config->item('cloud_user_avatar_container');
              
        // BLOB service
        $wablob_config = array(
                            'STORAGE_ACCOUNT' => $this->config->item('azure_storage_account'), 
                            'STORAGE_KEY'=> $this->config->item('azure_storage_key')
                            );
              
        $this->load->library('wablobstorage', $wablob_config);        
    }

   /**
     * Method index
     * @return mixed This is the return value description
     *
     */
    public function index() {
        $data['title'] =  self::$site_title;
        
        $uriSegment = $this->uri->segment_array();            
        $loginSignupSegment = (isset($uriSegment[2])) ? $uriSegment[2] : '';
              
        if(!$this->session->userdata(self::LOGGED_IN)){
            $data['show_login'] =  true;
            
            if(isset($loginSignupSegment) && $loginSignupSegment === 'signup'){
                // Pass url segment 2nd value to signup_login page, so based on that we can enable				
                $profile['uriSegment'] = $loginSignupSegment;
                $profile['social_provider'] = self::OWN_EMAIL;	// Set default social provider is one means custom email login/signup
                $profile['edit']   =   false;
                $profile['sn_first'] = true;
                $data['loginSignupForm'] =  $this->load->view('pages/profile', $profile, true);

                $this->load->view('templates/header', $data);
			    $this->load->view('templates/nav_bar', $data);
			    $this->load->view('pages/signup_login', $data);
			    $this->load->view('templates/footer', $data);                
            }else{
                redirect('main/'); 
            }
        }else{            
            // if user is logged in redirect to main page
            redirect('main/');
        }
    }

    /**
     * Method facebookLogin
     *
     * @return mixed This is the return value description
     *
     */		
    public function facebookLogin(){        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        //setting api key from config
        $fb_config = array('appId'=>$this->sn_config['facebook_appId'], 'secret'=>$this->sn_config['facebook_secret']);        
        $this->load->library('Facebook', $fb_config);

        // Try to get the user's id on Facebook - If user is not yet authenticated, the id will be zero
        $fb_userid = $this->facebook->getUser();
        
        if(isset($fb_userid) && ($fb_userid) > 0){
            try {
                $fb_userdata = NULL;
                $fb_userdata = $this->facebook->api('/me');  

                if(empty($fb_userdata)){
                    $this->session->set_flashdata('message', 'Facebook error');
                    error_log(__CLASS__ .' '.__METHOD__ .' Facebook Error');
                    exit;
                }
                
                $this->session->set_userdata('fb_userdata', $fb_userdata);
                $this->session->set_userdata('social_provider', self::FACEBOOK);
                $this->session->set_userdata('status', self::VERIFIED);               
                
                $a = $this->session->all_userdata();
                
                $user_obj = $this->isUserExist($fb_userdata['id'], self::FACEBOOK);

                if($user_obj && !empty($user_obj)){ 
                    // redirect to index 
                    $this->session->set_userdata(self::LOGGED_IN, true);
                    $this->session->set_userdata('userid', $user_obj->userid);
                    $this->session->set_userdata('username', $user_obj->username);
                    
                    // redirect to main page.
                    $this->session->set_flashdata('alert', 'success');
                    $this->session->set_flashdata('message', 'Welcome back '.$user_obj->username);
                    redirect('main/', 'refresh');                  
                }else{
                    // redirect to profile page for collecting the values
                    redirect('users/userProfile', 'refresh');
                }  
            }
            catch (FacebookApiException $e) {
                $fb_userid = null;
            }
        }else{
            //redirect to login page again
            $this->facebook->destroySession();
            $url = $this->facebook->getLoginUrl(array('scope'=>'email'));            
            header("Location: $url");
        }		
    }

    /**
     * Method microsoftLogin
     *
     * @return mixed This is the return value description
     *
     */
    public function microsoftLogin(){
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $cnt_live = $this->msLoadConfig();
        $loginUrl = $cnt_live->GetLoginUrl();
        header('Location: ' . $loginUrl);
    }
    
    /**
     * MS login
     */
    public function afterLoginFromMs(){
        $getData = $this->input->get();
        $code = (isset($getData['code'])) ? $getData['code'] : NULL;
        $cnt_live = $this->msLoadConfig();
        //get user info
        $user_info = $cnt_live->getUser($code);
        //get and set access token from microsoft
        if(!$user_info && isset($_GET["code"])){
            
            $access_token 	= $cnt_live->getAccessToken($_GET["code"]);
            $cnt_live->setAccessToken($access_token);                
            header('Location: '.$cnt_live->GetRedirectUrl());
        }

        //if user wants to log out, we simply distroy the current session
        if(isset($_GET["logout"])){
            $cnt_live->distroySession();
            header('Location: '.$cnt_live->GetRedirectUrl());
        }

        if($user_info){ //we have the user info, let's do something with it.
            $this->session->set_userdata('user_info',$user_info);
            $this->session->set_userdata('social_provider', self::MICROSOFT);
            $this->session->set_userdata('status', self::VERIFIED);
            
            // check is user already available or not
            $user_obj = $this->isUserExist($user_info->id, self::MICROSOFT);
            
            if($user_obj && !empty($user_obj)){
                // redirect to index
                $this->session->set_userdata(self::LOGGED_IN, true);
                $this->session->set_userdata('userid', $user_obj->userid);
                $this->session->set_userdata('username', $user_obj->username);  
                
                // redirect to main page.
                $this->session->set_flashdata('alert', 'success');
                $this->session->set_flashdata('message', 'Welcome back '.$user_obj->username);
                redirect('main/', 'refresh');                
            }else{
                // redirect to profile page for collecting the values
                redirect('users/userProfile', 'refresh');
            }  
        }else{
            $this->session->set_flashdata('alert', 'success');
            $this->session->set_flashdata('message', 'Looks you are not authenticated ');
            redirect('main/', 'refresh'); 
            
            
        }            
    }

    /**
     * Load MSN config
     */    
    private function msLoadConfig(){
        $ms_config  = array(
            'client_id'=>$this->sn_config['microsoft_appid'],
            'client_secret'=>$this->sn_config['microsoft_app_secret'],
            'client_scope'=>$this->sn_config['microsoft_scope'],
            'redirect_url'=>$this->sn_config['microsoft_redirect_url']
                );
        
        //$this->load->library('microsoft', $ms_config);
        require_once("microsoft.php");
        return new MocrosoftLiveCnt($ms_config);            
    }

    /**
     * This is method googleLogin
     *
     * @return mixed This is the return value description
     */
    public function googleLogin(){ 
        $google_config = array(
            'client_id'     =>$this->sn_config['google_appid'],
            'client_secret' =>$this->sn_config['google_app_secret'],
            'redirect_url'  =>$this->sn_config['google_redirect_url'],
            'google_aplication_name'=>$this->sn_config['google_aplication_name']
                );        
        
        require_once('Google/src/Google_Client.php');
        require_once('Google/src/contrib/Google_Oauth2Service.php');
        
        if (session_status() == PHP_SESSION_NONE) {
        session_start();
        }
        
        $apiConfig['oauth2_redirect_uri']=$this->sn_config['google_redirect_url'];
        
        $client = new Google_Client();
        $client->setApplicationName($google_config['google_aplication_name']);
        $client->setClientId($google_config['client_id']);
        $client->setClientSecret($google_config['client_secret']);
        $client->setRedirectUri($google_config['redirect_url']);
        $client->setDeveloperKey('');
        $oauth2 = new Google_Oauth2Service($client);
        
        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $_SESSION['token'] = $client->getAccessToken();
            $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
            return;
        }
        if (isset($_SESSION['token'])) {
            $client->setAccessToken($_SESSION['token']);
        }

        if (isset($_REQUEST['logout'])) {
            unset($_SESSION['token']);
            $client->revokeToken();
        }

        if ($client->getAccessToken()) {
            $user = $oauth2->userinfo->get();

            // The access token may have been updated lazily.
            $_SESSION['token'] = $client->getAccessToken();
        }else {
                $authUrl = $client->createAuthUrl();
        }

        if($user){ 
            $this->session->set_userdata('social_provider', self::GOOGLE);
            $this->session->set_userdata('status', self::VERIFIED);
            $this->session->set_userdata('g_user_data',$user);
            // check is user already available or not
            $user_obj = $this->isUserExist($user['id'], self::GOOGLE);
            
            if($user_obj && !empty($user_obj)){
                // redirect to index
                $this->session->set_userdata(self::LOGGED_IN, true);
                $this->session->set_userdata('userid', $user_obj->userid);
                $this->session->set_userdata('username', $user_obj->username);
                
                // redirect to main page.
                $this->session->set_flashdata('alert', 'success');
                $this->session->set_flashdata('message', 'Welcome back '.$user_obj->username);
                redirect('main/', 'refresh');
            }else{
                // redirect to profile page for collecting the values
                redirect('users/userProfile', 'refresh');
            }  
        }else{
            if(isset($authUrl)) {
                header('Location: ' . $authUrl);
            }
        }
    }

    /**
     * Method twitterLogin
     *
     * @return mixed This is the return value description
     */         
    public function twitterLogin(){
        $this->load->library('twitteroauth');
        $connection = $this->twitteroauth->create($this->sn_config['twitter_consumer_key'], $this->sn_config['twitter_consumer_secret']);

        /* Get temporary credentials. */
        $request_token = $connection->getRequestToken($this->sn_config['twitter_oauth_callback']);
        $token = $request_token['oauth_token'];
        $this->session->set_userdata('oauth_token', $request_token['oauth_token']);
        $this->session->set_userdata('oauth_token_secret', $request_token['oauth_token_secret']);

        /* If last connection failed don't display authorization link. */
        switch ($connection->http_code) {
            case 200:
                /* Build authorize URL and redirect user to Twitter. */
                $url = $connection->getAuthorizeURL($token);
                header('Location: ' . $url); 
                break;
            default:
                /* Show notification if something went wrong. */
                error_log(__CLASS__ .' '.__METHOD__. 'Could not connect to Twitter. please fix this.');
        }
    }

    /**
     * Twitter callback for auth
     */        
    public function callback(){
        
        $this->load->library('twitteroauth'); 

        /* If the oauth_token is old redirect to the twitterLogin page. */
        if (isset($_REQUEST['oauth_token']) && $this->session->userdata('oauth_token') !== $_REQUEST['oauth_token']) {
            $this->session->set_userdata('oauth_status', 'oldtoken');
            $this->session->unset_userdata('access_token');
            $this->session->unset_userdata('oauth_token_secret');

            redirect('/users/twitterLogin/', 'refresh');
        }

        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = $this->twitteroauth->create($this->sn_config['twitter_consumer_key'], $this->sn_config['twitter_consumer_secret'], $this->session->userdata('oauth_token'), $this->session->userdata('oauth_token_secret'));
        
        if(!isset($_REQUEST['oauth_verifier']) || empty($_REQUEST['oauth_verifier'])){
            $this->session->set_flashdata('alert', 'success');
            $this->session->set_flashdata('message', 'Looks you are not authenticated ');
            redirect('main/', 'refresh');
        }
        /* Request access tokens from twitter */
        $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

        /* Save the access tokens. Normally these would be saved in a database for future use. */
        $this->session->set_userdata('access_token', $access_token);
        /* Remove no longer needed request tokens */
        $this->session->unset_userdata('oauth_token');
        $this->session->unset_userdata('oauth_token_secret');

        /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if (200 == $connection->http_code) {
            $this->session->set_userdata('status', 'verified');

            // redirect('login/userProfile', 'refresh');
            $content = $connection->get('account/verify_credentials');
            $sn_userid = $content->id;
            //print_r($content);
            $this->session->set_userdata('social_provider', self::TWITTER);
            $this->session->set_userdata('status', self::VERIFIED);
            // check is user already available or not
            $user_obj = $this->isUserExist($sn_userid, self::TWITTER);

            if($user_obj && !empty($user_obj)){
                $this->session->set_userdata(self::LOGGED_IN, true);
                $this->session->set_userdata('userid', $user_obj->userid);
                $this->session->set_userdata('username', $user_obj->username);
                
                // redirect to main page.
                $this->session->set_flashdata('alert', 'success');
                $this->session->set_flashdata('message', 'Welcome back '.$user_obj->username);
                redirect('main/', 'refresh');
                
            }else{
                $t_user_data = array(
                    'id'=>$content->id,
                    'name' => $content->name,
                    'avatar' => $content->profile_image_url_https,
                    'screen_name' => $content->screen_name                 
                    );
                $this->session->set_userdata('t_user_data', $t_user_data);
                redirect('users/userProfile', 'refresh');
            }				
        }else{
            redirect('users/', 'refresh');
        }
    }

    /**
     * Summary of mehtod Get data for user model from Facebook Data
     * @param array $fb_userdata
     * @return mix eaither boolean or array
     */
    public function getDataForUserModelFromFBData($fb_userdata){
        if(empty($fb_userdata)){
            error_log('getUserDataForModel empty value' );
            return false;
        }

        $arr = array();
        $arr['id'] = $fb_userdata['id'];
        $arr['username'] = $fb_userdata['username'];
        $arr['email'] = $fb_userdata['email'];
        $arr['social_userid'] = $fb_userdata['id'];
        $arr['social_provider'] = self::FACEBOOK;
        
        return $arr;
    }

    /**
     * Check user already exist or not.
     * Based on the social network logins.
     */
    public function isUserExist($social_userid, $social_provider){

        if(empty($social_userid) || empty($social_provider)){
                return false;            
        }
        
        $result= $this->user_model->getUserBySN($social_userid, $social_provider);

        if( count($result) >=1  ){
                return $result[0];
        }

        return false;
    }

    /**
     * Summary of showProfile
     * @param string $uriData eaither show or edit
     */
    public function profile($uriData){
        
        if(!$this->session->userdata('logged_in')){                
            $this->session->set_flashdata('alert', 'warning');
            $this->session->set_flashdata('message', 'Please login to start new disussion');
            redirect('users', 'refresh');
        }else{         

            $userid   =  $this->session->userdata('userid');
            
            $data =   array();
            $data['profile'] = $this->user_model->getUserProfileById($userid);
            
            unset($data['profile'][0]->password); // unset password
            $header['title'] =  'Welcome :: '.WEBSITE_NAME.' Profile';
            
            $this->load->view('templates/header', $header);
            $this->load->view('templates/nav_bar', $data);

            if($uriData === 'show'){
                $this->load->view('pages/show_profile', $data);
            }elseif($uriData === 'edit'){                    
                $data['signed_in']  = true;
                $data['edit']   =   true;
                $this->load->view('pages/profile', $data);
            }
            
            $this->load->view('templates/footer', $data);
        }
    }

    /**
     * This is method userProfile
     *
     * @return mixed This is the return value description
     * User profile data
     * When user submits his/her profile in the sign up this method will take care
     *
     */
    public function userProfile(){
        $this->load->library('session');
        $this->load->helper(array('form', 'url'));
        $data =  $this->input->post();
        
        // Check is profile data not empty AND is social_provider is set for custom email signup
        if(!empty($data) && isset($data['social_provider'])){
            // update social_id with social_provider
            $this->session->set_userdata('social_provider', $data['social_provider']);
        }
        $data['signed_in'] = true;

        if($this->session->userdata(self::LOGGED_IN)){ 
            redirect('main/', 'refresh');
            //$data['signed_in'] = false;				
        }

        // Look for social_provider from session 
        // If session not available
        if(!$this->session->userdata('social_provider')){	 		
            redirect('/main/', 'refresh');
        }

        // Load the specific data from session        
        switch($this->session->userdata('social_provider')){

            case self::FACEBOOK: 
                $fb_userdata = $this->session->userdata('fb_userdata');                 
                break;
                
            case self::MICROSOFT:
                $user_info=$this->session->userdata('user_info');
                break;
                
            case self::GOOGLE:
                $g_user_data=$this->session->userdata('g_user_data');
                break;
            case self::TWITTER:					
                $t_user_data=$this->session->userdata('t_user_data');
                
                break;
        }

        
        if(empty($data) || !isset($data['fullname']) || empty($data['fullname'])){
            switch($this->session->userdata('social_provider')){
                case self::FACEBOOK:
                    $data['sn_first'] = true;
                    $data['name'] = $fb_userdata['name'];
                    $data['email'] = $fb_userdata['email'];
                    $data['avatar'] = 'http://graph.facebook.com/'.$fb_userdata['id'].'/picture';
                    
                    if(isset($fb_userdata['hometown']) && !empty($fb_userdata['hometown'])){
                        list($city) = explode(',', $fb_userdata['hometown']['name']); // city, country
                        $data['city'] = $city;
                    }                    
                    break;
                    
                case self::MICROSOFT:
                    $data['sn_first'] = true;
                    $data['name'] = $user_info->name;
                    $data['email'] = $user_info->emails->preferred;
                    $data['avatar'] = 'https://apis.live.net/v5.0/'.$user_info->id.'/picture';
                    break;
                    
                case self::GOOGLE:
                    $data['sn_first'] = true;
                    $data['name'] = $g_user_data['name'];
                    $data['email'] = $g_user_data['email'];  
                    $data['avatar'] =$g_user_data['picture']; 
                    break;
                    
                case self::TWITTER:
                    $data['sn_first'] = true;
                    $data['name'] = $t_user_data['name']; 
                    $data['avatar'] = $t_user_data['avatar']; 
                    $data['twitter_handle'] = $t_user_data['screen_name']; 
                    break;
            }
            $this->session->set_flashdata('alert', 'warning');
            $this->session->set_flashdata('message', 'Please enter your details .');

        }else{
            
            $userdata = array();

            // profile data
            $userdata['name']= (isset($data['fullname'])) ? $data['fullname'] : '';
            $userdata['password'] = '';
            $userdata['avatar']= (isset($data['avatar'])) ? $data['avatar'] : '';
            $userdata['city']= (isset($data['city'])) ? $data['city'] : '';
            $userdata['state']= (isset($data['state'])) ? $data['state'] : '';
            $userdata['tokens']= (isset($data['tokens'])) ? $data['tokens'] : '';
            $userdata['twitter_handle']= (isset($data['twitter_handle'])) ? $data['twitter_handle'] : '';
            $userdata['notif_special']= (isset($data['notification']) && $data['notification']='on') ? 1 : 0;				
            $userdata['notif_product']= (isset($data['product_notification'])&&$data['product_notification']='on') ? 1 : 0;
            $userdata['notif_post']= (isset($data['post_notification'])&&$data['post_notification']='on') ? 1 : 0;				
            $userdata['notif_tweet']= (isset($data['tweet_notification'])&&$data['tweet_notification']='on') ? 1 : 0;            
            $userdata['social_provider']= (isset($data['social_provider'])) ? $data['social_provider'] : self::OWN_EMAIL;	// Using Fedility or email
            $userdata['email']= (isset($data['email'])) ? $data['email'] : '';
            $userdata['social_token']=(isset($data['social_token'])) ? $data['social_token'] : '';

            // user data

            switch($this->session->userdata('social_provider')){
                case self::FACEBOOK:
                    $userdata['email'] = $fb_userdata['email'];
                    $userdata['username'] = $fb_userdata['name'];
                    $userdata['social_userid'] = $fb_userdata['id'];
                    $userdata['social_provider'] = self::FACEBOOK;
                    $userdata['avatar'] = 'http://graph.facebook.com/'.$fb_userdata['id'].'/picture';
                    break;

                case self::MICROSOFT:					
                    $userdata['email'] = $user_info->emails->preferred; 
                    $userdata['username'] = $user_info->name;
                    $userdata['social_userid'] = $user_info->id;
                    $userdata['social_provider'] = self::MICROSOFT; 
                    $userdata['avatar'] = 'https://apis.live.net/v5.0/'.$user_info->id.'/picture';
                    break;

                case self::GOOGLE:						
                    $userdata['email'] = $g_user_data['email']; 
                    $userdata['username'] = (!empty($g_user_data['given_name'])) ? $g_user_data['given_name'] : $g_user_data['email'];
                    $userdata['social_userid'] = $g_user_data['id'];
                    $userdata['social_provider'] = self::GOOGLE;
                    $userdata['avatar']= $g_user_data['picture'];
                    break;

                case self::TWITTER:
                    $userdata['email'] = $data['email']; // We won't get email info from twitter so, we need to collect from user
                    $userdata['username'] = $t_user_data['screen_name']; //$content->screen_name;                  
                    $userdata['social_userid'] = $t_user_data['id']; 
                    $userdata['social_provider'] = self::TWITTER;
                    $userdata['twitter_handle']= $t_user_data['screen_name'];
                    $userdata['avatar']= $t_user_data['avatar'];
                    break; 

                case self::OWN_EMAIL:
                    $userdata['email'] = $data['email'];
                    $userdata['username'] = $data['email'];                        
                    $userdata['social_userid'] = '';
                    $userdata['social_provider'] = self::OWN_EMAIL;					
                    $userdata['password'] = (isset($data['password'])) ? md5($data['password']) : md5('Aa123456');						                        
                    break;		
            }
            
            $result = true;
            
            if ($userdata['social_provider'] == self::OWN_EMAIL) {
                $res =  $this->user_model->getUserByEmail($userdata['email'], self::OWN_EMAIL);
                if (count($res) > 0) {
                    $result	= false;
                }
            }
            
            if(!$result){
                error_log('User creation is failed'); 
                $this->session->set_flashdata('alert', 'warning');
                $this->session->set_flashdata('message', 'User with this email already exist');
                redirect('users/signup', 'refresh');	
            }

            // Upload user picture
            if( $userdata['social_provider'] == self::OWN_EMAIL && isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0 ){

                // Get the file content
                $fileContent = file_get_contents($_FILES['avatar']['tmp_name']);                
                $fileName = 'avatar_' . date('Ymdhis') . '.' . pathinfo($_FILES['avatar']['tmp_name'], PATHINFO_EXTENSION);
                    
                // upload files to WA_BLOB
                $uploadResult = $this->wablobstorage->uploadBlob($this->_cloud_user_avatar_container, $fileName,  $fileContent);

                if($uploadResult){
                    $userdata['avatar'] =  $fileName;
                }else{
                    // File upload if else condition  
                    $result = false;
                }
            }
                
            $result = ($result) ? $this->user_model->userInsert($userdata) : $result;
                                
            if($result){                    
                $userProfile =   $this->user_model->getUserByEmail($userdata['email'], $userdata['social_provider']);
                    
                // Keep userid in session
                $this->session->set_userdata('userid', $userProfile[0]->userid);
                $this->session->set_userdata('username', $userProfile[0]->username);
                unset($userProfile); // unset profile data
                    
                // Keep is user logged_in or not in session
                $this->session->set_userdata(self::LOGGED_IN, true);						
                $this->session->set_flashdata('alert', 'success');
                $this->session->set_flashdata('message', 'Your profile is created successfully .');
                redirect('main/', 'refresh'); // Redirect to Home page
                    
            }else{
                error_log('User creation is failed');
                $this->session->set_flashdata('alert', 'warning');
                $this->session->set_flashdata('message', 'User creation is failed');
            }                
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/nav_bar', $data);
        $this->load->view('pages/profile', $data);
        $this->load->view('templates/footer', $data); 
    }

    /**
     * Summary of updateProfile
     * Update user profile
     */
    public function updateProfile(){
        
        if(!$this->session->userdata('logged_in')){
            $this->session->set_flashdata('alert', 'warning');
            $this->session->set_flashdata('message', 'Please login to start new disussion');
            redirect('main/', 'refresh');
            
        }else{
            
            $data =  $this->input->post();
            
            $userdata = array();                
            // profile data
            $userdata['name']= (isset($data['fullname'])) ? $data['fullname'] : '';
            $userdata['city']= (isset($data['city'])) ? $data['city'] : '';
            $userdata['state']= (isset($data['state'])) ? $data['state'] : '';
            
            // Don't update twitter hadle if user is login using twitter
            if($this->session->userdata("social_provider") != self::TWITTER ) {
                $userdata['twitter_handle']= (isset($data['twitter_handle'])) ? $data['twitter_handle'] : '';
            }
            $userdata['email']= (isset($data['email'])) ? $data['email'] : '';
            $userdata['notif_special']= (isset($data['notification']) && $data['notification']='on') ? 1 : 0;
            $userdata['notif_product']= (isset($data['product_notification'])&&$data['product_notification']='on') ? 1 : 0;
            $userdata['notif_post']= (isset($data['post_notification'])&&$data['post_notification']='on') ? 1 : 0;
            $userdata['notif_tweet']= (isset($data['tweet_notification'])&&$data['tweet_notification']='on') ? 1 : 0;            
            $userdata['userid']= $this->session->userdata('userid');

            if( ($this->session->userdata('social_provider') == self::OWN_EMAIL) && empty($userdata['email']) ){
                redirect('users/profile/show', 'refresh');
            }else{
                
                $flag   =   true;
                $msg    =   '';
                
                // Check is file avatar is set AND not file error AND session user is not social login user
                if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0 && $this->session->userdata("social_provider") == self::OWN_EMAIL){
                    
                    // Get the file content
                    $fileContent = file_get_contents($_FILES['avatar']['tmp_name']);
                    $fileName = 'avatar_'.date('Ymdhis').$_FILES['avatar']['name'];
                    // upload files to WA_BLOB                   
                    
                    $uploadResult = $this->wablobstorage->uploadBlob($this->_cloud_user_avatar_container, $fileName,  $fileContent);
                    
                    if($uploadResult){
                        $userdata['avatar'] = $fileName;
                        // Insert discussion post info in table
                        $result = $this->user_model->userUpdate($userdata);
                        
                        // result is valid then send success message back to view page 
                        // else send error message back to view page
                        if($result){
                            $msg = 'profile updated sucessfully';
                        }
                        else{                            
                            $flag = false;
                            $msg = 'profile update fail, Something went wrong when saving data, please try again.';
                        } // DB result if else condition 
                    }else{
                        $flag = false;
                        $msg = 'profile update fail, Something went wrong when saving data, please try again..';
                    }// File upload if else condition 
                }else{
                    $result = $this->user_model->userUpdate($userdata);
                    if($result){
                        $msg = 'profile updated sucessfully';
                    }
                    else{
                        $flag = false;
                        $msg = 'profile update fail, Something went wrong when saving data, please try again...';
                    }
                }// IF file avatar check if else condition

                error_log('profile update' . ($flag) ? ' success ':' fail ' . $userdata['email']); 
                $this->session->set_flashdata('alert', ($flag)?'success':'warning');
                $this->session->set_flashdata('message', $msg);
                redirect('users/profile/show', 'refresh');
            }
        }
    }
    
    /**
     * Summary of signin method
     *
     * @return mixed This is the return value description
     *
     */		
    public function signin(){        
        $data =  $this->input->post();        
        $result = $this->user_model->signin($data);
                
        $xhrresult = array();
        $xhrresult['status'] = true;
        
        // If user data available in result then load profile details and set name and userid in session
        if (!$result){            
            error_log('Wrong Login data '.$data['email']);
            $xhrresult['status'] = false;
            $xhrresult['message'] = "The email or password you entered is incorrect";            
        }else{
            $profile = $this->user_model->getUserProfileById($result[0]->userid);
            unset($profile[0]->password);   // unset password
            
            $this->session->set_userdata('user_full_name', $profile[0]->name);
            $this->session->set_userdata('userid', $profile[0]->userid);
            $this->session->set_userdata('social_provider', self::OWN_EMAIL);
            $this->session->set_userdata('status', self::VERIFIED);
            $this->session->set_userdata(self::LOGGED_IN, true);
        }
        echo json_encode($xhrresult);
    }
    
    /**
     * Summary of method forgotPassword
     *
     * @return mixed This is the return value description
     *
     */				
    public function forgotPassword(){

        // If request method is POST then allow else redirect to user signup page
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $this->load->helper(array('form', 'url'));
            $data =  $this->input->post();
            $flag = true;

            // If data of email is not set and is empty then set flag as false;
            if(!isset($data['forgotEmail']) || trim($data['forgotEmail']) == '') {
                $flag = false;
            }            

            if($flag){                
                $profile = $this->user_model->getUserByEmail($data['forgotEmail'], self::OWN_EMAIL);

                // If count of profile is lessthan 0 set flag as false
                if(count($profile) <= 0){
                    $flag = false;
                }
            }
            
            if($flag){                
                // Generate token expire date after 24 hours
                $expDateTime   =   strtotime(date("Y-m-d H:i:s", strtotime("+24 Hour")));

                // Generate token with md5 and mcrypt hash and concatinate with expaireDateTime
                $token = md5(mcrypt_create_iv(14, MCRYPT_DEV_URANDOM)).$expDateTime;

                // Prepare user data to update users table                       
                $userData = array();
                
                /*foreach($profile[0] AS $key=>$val){ $userData[$key] = $val;}*/
                $userData['tokens']=$token;
                $userData['userid']=$profile[0]->userid;

                // Update users table tokens column with pre hash, which need to check when user come with a reset password link
                $this->user_model->userUpdate($userData);

                $passwordResetUrl = base_url().'users/resetpassword/usid/'.$userData['userid'].'/token/'.$token;

                $emailConfigArr = $this->config->item('email');
                $this->load->library('email', $emailConfigArr);
                
                $this->email->set_newline("\r\n");
                $this->email->from($emailConfigArr['from_email'], $emailConfigArr['from_name']);
                $this->email->to($data['forgotEmail']);
                $this->email->subject($emailConfigArr['from_name']. ' Forgot Password');
                
                $body   = "
                            <table>
                                <tr><td><h2>ContosoWeb</h2></td></tr>
                                <tr><td> <p>Hi,</p></td></tr>
                                <tr><td><p>Changing your password is simple. Please use the link below within 24 hours.</p></td></tr>
                                <tr><td><p><a style='color:#006699' href='$passwordResetUrl' target='_blank'>$passwordResetUrl<a/></p></td></tr>
                                <tr><td><p>Thank you,</p></td></tr>
                                <tr><td><p>The ContosoWeb Team</p></td></tr>
                            </table>
                        ";
                
                $this->email->message($body); 
                $this->email->send();

                //echo $this->email->print_debugger();
                error_log('Email sent sucessfully '.$data['forgotEmail']); 
                $this->session->set_flashdata('alert', 'success');
                $this->session->set_flashdata('message', 'Please check your email for reset password link');
                redirect('main/', 'refresh');
            
            }else{
                error_log('Forgotpassword Wrong data '.$data['forgotEmail']); 
                $this->session->set_flashdata('alert', 'warning');
                $this->session->set_flashdata('message', 'You are not registered with us, please signup');
                redirect('users/signup', 'refresh');                  
            }
        }else{
            redirect('main/', 'refresh'); 
        }            
    }

    /**
     * Summary of method resetPassword
     *
     */        
    public function resetPassword(){
        $this->load->helper(array('form', 'url'));
        $segmemnts = $this->uri->uri_to_assoc(3);

        $flag = true;

        // If userid data is set and not empty then check other condition else redirect to main page
        if(isset($segmemnts['usid']) && $segmemnts['usid'] != ''){

            // If token is not set and empty then make false and redirect to main page
            // else check other condition else redirect to main page
            if(!isset($segmemnts['token']) && $segmemnts['token'] == ''){
                $flag = false;
            }else{
                // Split the token for last 10 digits, which is expiry token
                $tokenDateTime      =  substr($segmemnts['token'], -10);
                $currentDateTime    =  strtotime(date('Y-m-d H:i:s'));

                // condition to test is expiry token date time is less than current time
                // then load db token from user table and verify is boath are same or not
                // if not same then set flag as false;
                // else set the segment user data in session and redirect to changepassword page
                if($currentDateTime <= $tokenDateTime){                    
                    $result = $this->user_model->getToken($segmemnts['usid']);

                    if($segmemnts['token'] === $result[0]->tokens){				            
                        $this->session->set_userdata('changepasswordhash', $segmemnts);                            
                        redirect('users/changepassword/', 'refresh');
                    }else{
                        $flag = false;
                    }
                }else{
                    error_log('Forgotpassword token expired Wrong data '.$segmemnts['usid']); 
                    $this->session->set_flashdata('alert', 'warning');
                    $this->session->set_flashdata('message', 'Your token was expired');
                    redirect('main/', 'refresh');                        
                }
            }                               
        }

        if(!$flag){
            redirect('main/', 'refresh'); 
        }
    }

    /**
     * Summary of method changepassword
     *
     */
    public function changePassword(){            
        if($_SERVER['REQUEST_METHOD'] === 'GET'){   
            $this->load->view('templates/header');
            $this->load->view('templates/nav_bar');
            $this->load->view('pages/changepassword');
            $this->load->view('templates/footer');
        }elseif($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data =  $this->input->post();
            
            $session_data = $this->session->userdata('changepasswordhash');
            
            if(empty($session_data)){
                $this->session->set_flashdata('alert', 'warning');
                $this->session->set_flashdata('message', 'Please try again');            
                redirect('main/', 'refresh');
            }
            
            if($data['password'] === $data['confirm_password']){                
                $result = $this->user_model->updatePassword($data['password'], $session_data['usid']);                
                $this->session->unset_userdata('changepasswordhash');
                $this->session->set_flashdata('alert', 'success');
                $this->session->set_flashdata('message', 'Your password changed successfully');
                redirect('main/', 'refresh');        
            }else{                
                redirect('users/changepassword/', 'refresh');
            }			    
        }
    }
    
    /**
     * Load top 5 users
     */
    public function topFiveUsers($count=5){
        $data['topUsers'] = $this->user_model->getTopUsers($count);        
        $this->load->view('templates/top_users_panel', $data);
    }
}

/* End of file users.php */
/* Location: ./application/controllers/users.php */