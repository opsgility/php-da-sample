<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This is class Community
 * Summary of Community
 */

class Community extends CI_Controller {

    const MIME_IMAGE = 1;
    const MIME_VIDEO = 2;

    private $_cloud_container_images;

    /**
     * This is method constructor
     * load the config and lib files.
     */
    public function Community() {
        parent::__construct();
        parse_str($_SERVER['QUERY_STRING'], $_REQUEST);
        $this->load->model('discussion_model');
        $this->load->library("pagination");

        // load azure credentials from config
        // We are going to use this container for storing images.
        $this->_cloud_container_images = $this->config->item('azure_storage_container_images');

        // BLOB service
        $wablob_config = array(
            'STORAGE_ACCOUNT' => $this->config->item('azure_storage_account'),
            'STORAGE_KEY' => $this->config->item('azure_storage_key')
        );

        $this->load->library('wablobstorage', $wablob_config);

        // WAMS service
        $wamedia_config = array(
            'account_name' => $this->config->item('azure_media_services_account_name'),
            'account_key' => $this->config->item('azure_media_services_account_key')
        );
        $this->load->library('wamediaservice', $wamedia_config);
    }

    /**
     * This is method view discussions
     *
     */
    public function index() {
        //if(ENVIRONMENT == 'development'){$this->output->enable_profiler(TRUE);}              
        $data = array();
        $offset = (!empty($this->uri->segment(3))) ? $this->uri->segment(3) : 1;
        $filter = '';  // filter by image or video or all {image=1, video=2}
        $filterSuffix = '';

        if (!empty($this->uri->segment(5))) {
            $filterSuffix = '/filter/' . $this->uri->segment(5);
            if ($this->uri->segment(5) != 'all') {
                $filter = ($this->uri->segment(5) === "image") ? self::MIME_IMAGE : self::MIME_VIDEO;
            }
        }

        $per_page = 5;
        $offset = ($offset - 1) * $per_page;

        // Pagination config setup              
        // http://{domainname.com}/uri_segment_1/uri_segment_2/
        $config['base_url'] = base_url() . 'community/index';
        $config['total_rows'] = $this->discussion_model->countPosts($filter);
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = true;

        // I added this extra one to control the number of links to show up at each page.
        $config['num_links'] = 2;

        // uri_segment_5 === page no/offset
        $config['uri_segment'] = 3;
        $config['suffix'] = $filterSuffix;
        $config['first_url'] = $config['base_url'] . '/1/' . $config['suffix'];

        // Initialize
        $this->pagination->initialize($config);

        // http://{domain.com}/discussions/{pageno/offset} // find config in config/routes.php
        // 1/2 {uri_segments}

        $data['posts_list'] = $this->discussion_model->viewPosts($filter, $offset, $per_page);

        $data['link'] = $this->pagination->create_links();
        $data['currentpage'] = $this->pagination->cur_page;
        $data['filterquery'] = $filter;

        $data['content'] = $this->load->view('pages/ajax_list_discussion', $data, true);

        $twitter['twitter_widget_panel'] = $this->load->view('templates/twitter_widget', '', true);
        $data['sidebar_panel'] = $this->load->view('templates/sidebar_panel', $twitter, true);

        $header['title'] = 'Welcome :: ' . WEBSITE_NAME;
        $this->load->view('templates/header', $header);
        $this->load->view('templates/nav_bar', $data);

        $this->load->view('pages/list_discussion', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Summary of ajax_discussion_list
     * load discussions when user click on pagination links and filter
     */
    public function ajax_discussion_list() {
        $data = array();

        $offset = (!empty($this->uri->segment(3))) ? $this->uri->segment(3) : 1;
        $filter = '';  // filter by image or video or all {image=1, video=2}

        $filterSuffix = '';

        if (!empty($this->uri->segment(5))) {
            $filterSuffix = '/filter/' . $this->uri->segment(5);
            if ($this->uri->segment(5) != 'all') {
                $filter = ($this->uri->segment(5) === "image") ? self::MIME_IMAGE : self::MIME_VIDEO;
            }
        }

        $per_page = 5;
        $offset = ($offset - 1) * $per_page;

        // Pagination config setup              
        // http://{domainname.com}/uri_segment_1/uri_segment_2/
        $config['base_url'] = base_url() . 'community/index';
        $config['total_rows'] = $this->discussion_model->countPosts($filter);
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;

        // I added this extra one to control the number of links to show up at each page.
        $config['num_links'] = 2;

        // uri_segment_5 === page no/offset
        $config['uri_segment'] = 3;
        $config['suffix'] = $filterSuffix;
        $config['first_url'] = $config['base_url'] . '/1/' . $config['suffix'];

        // Initialize
        $this->pagination->initialize($config);

        // http://{domain.com}/discussions/{pageno/offset} // find config in config/routes.php
        // 1/2 {uri_segments}

        $data['posts_list'] = $this->discussion_model->viewPosts($filter, $offset, $per_page);

        $data['link'] = $this->pagination->create_links();
        //$data['currentpage'] =$this->pagination->cur_page;
        $data['filterquery'] = $filter;

        $this->load->view('pages/ajax_list_discussion', $data);
    }

    /**
     * Summary of view
     * This is method viewCommunityWithcomments will show particular discussion with comments
     */
    public function view() {
        //if(ENVIRONMENT == 'development'){$this->output->enable_profiler(TRUE);}

        $data = array();

        $id = $this->uri->segment(3);
        $data['id'] = $id;
        $title = $this->uri->segment(4);
        $data['title'] = $title;
        $pageOffset = $this->uri->segment(5);

        $this->load->library("pagination");

        $offset = (!empty($this->uri->segment(5))) ? $this->uri->segment(5) : 1;
        if (!$this->session->userdata('logged_in') && $offset > 1) {
            $this->session->set_flashdata('alert', 'danger');
            $this->session->set_flashdata('message', 'Please login to see remaining responses');
            redirect('community/', 'refresh');
        }

        $filter = '';  // filter by image or video or all {image==1, video == 2}
        $filterSuffix = '';

        if (!empty($this->uri->segment(6))) {
            $filterSuffix = '/filter/' . $this->uri->segment(7);
            if ($this->uri->segment(7) != 'all') {
                $filter = ($this->uri->segment(7) === "image") ? self::MIME_IMAGE : self::MIME_VIDEO;
            }
        }

        $per_page = 5;
        $offset = ($offset - 1) * $per_page;

        // Pagination config setup
        // http://{domainname.com}/uri_segment_1/uri_segment_2/uri_segment_3/uri_segment_4
        $config['base_url'] = base_url() . '/community/view/' . $id . '/' . $title . '/';

        // uri_segment_5 === page no/offset
        $config['uri_segment'] = 5;
        $config['total_rows'] = $this->discussion_model->countResponses($id, $filter);
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;

        // I added this extra one to control the number of links to show up at each page.
        $config['num_links'] = 2;
        $config['suffix'] = $filterSuffix;
        $config['first_url'] = $config['base_url'] . '/1/' . $config['suffix'];

        // Initialize
        $this->pagination->initialize($config);
        $data['link'] = $this->pagination->create_links();

        $data['discussion'] = $this->discussion_model->getDiscussion($id);
        if ($data['discussion'] !== false) {
            $data['userinfo'] = $this->discussion_model->getCountUserPostsAndResponse($data['discussion'][0]->postUserid);
            $data['response_list'] = $this->discussion_model->getResponses($id, $filter, $offset, $per_page);
            $data['currentpage'] = $this->pagination->cur_page;
            $data['filterquery'] = $filter;
            $data['content'] = $this->load->view('pages/ajax_view_discussion', $data, true);
        } else {
            redirect('community/', 'refresh');
        }

        $twitter['twitter_widget_panel'] = $this->load->view('templates/twitter_widget', '', true);
        $data['sidebar_panel'] = $this->load->view('templates/sidebar_panel', $twitter, true);

        $header['title'] = 'Welcome :: ' . WEBSITE_NAME;
        $this->load->view('templates/header', $header);
        $this->load->view('templates/nav_bar', $data);
        $this->load->view('pages/view_discussion', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * Summary of ajax_resp_list
     * load responses of discussions when user click on pagination links and filter
     */
    public function ajax_resp_list() {
        //if(ENVIRONMENT == 'development'){$this->output->enable_profiler(TRUE);}

        $data = array();

        $id = $this->uri->segment(3);
        $data['id'] = $id;
        $title = $this->uri->segment(4);
        $data['title'] = $title;
        $pageOffset = $this->uri->segment(5);

        $this->load->library("pagination");

        $offset = (!empty($this->uri->segment(5))) ? $this->uri->segment(5) : 1;
        if (!$this->session->userdata('logged_in') && $offset > 1) {
            $this->session->set_flashdata('alert', 'danger');
            $this->session->set_flashdata('message', 'Please login to see remaining responses');
            $flag = false;
            $alert = 'login';
        }

        $filter = '';  // filter by image or video or all {image==1, video == 2}
        $filterSuffix = '';

        if (!empty($this->uri->segment(6))) {
            $filterSuffix = '/filter/' . $this->uri->segment(7);
            if ($this->uri->segment(7) != 'all') {
                $filter = ($this->uri->segment(7) === "image") ? self::MIME_IMAGE : self::MIME_VIDEO;
            }
        }

        $per_page = 5;
        $offset = ($offset - 1) * $per_page;

        // Pagination config setup              
        // http://{domainname.com}/uri_segment_1/uri_segment_2/uri_segment_3/uri_segment_4
        $config['base_url'] = base_url().'/community/view/'.$id.'/'.$title.'/';

        // uri_segment_5 === page no/offset
        $config['uri_segment'] = 5;
        $config['total_rows'] = $this->discussion_model->countResponses($id, $filter);
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;

        // I added this extra one to control the number of links to show up at each page.
        $config['num_links'] = 2;
        $config['suffix'] = $filterSuffix;
        $config['first_url'] = $config['base_url'].'/1/'.$config['suffix'];

        // Initialize
        $this->pagination->initialize($config);
        $data['link'] = $this->pagination->create_links();

        $data['discussion'] = $this->discussion_model->getDiscussion($id);

        if ($data['discussion'] !== false) {
            $data['userinfo'] = $this->discussion_model->getCountUserPostsAndResponse($data['discussion'][0]->postUserid);
            $data['response_list'] = $this->discussion_model->getResponses($id, $filter, $offset, $per_page);
            $data['currentpage'] = $this->pagination->cur_page;
            $data['filterquery'] = $filter;
            $flag = true;
        } else {
            $flag = false;
            $alert = 'nodata';
        }

        if ($flag) {
            $this->load->view('pages/ajax_view_discussion', $data);
        } elseif (!$flag && $alert == 'login') {
            echo '<div style="margin-top:10px;" class="alert alert-warning bs-alert-old-docs">Please Login</div>';
        } elseif (!$flag && $alert == 'nodata') {
            echo '<div style="margin-top:10px;" class="alert alert-info bs-alert-old-docs">There are no Responses</div>';
        }
    }

    /**
     * Summary of post
     * currenlty we are not using this method.
     */
    /*
    public function post() {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('alert', 'danger');
            $this->session->set_flashdata('message', 'Please login to start new disussion');
            redirect('main/', 'refresh');
        }

        // If server request method is GET then load discussion post form
        // Else if server request method is POST then get the discussion post form details
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $header['title'] = 'Welcome :: ' . WEBSITE_NAME;
            $data = array();
            $this->load->view('templates/header', $header);
            $this->load->view('templates/nav_bar', $data);

            $twitter['twitter_widget_panel'] = $this->load->view('templates/twitter_widget', '', true);
            $data['sidebar_panel'] = $this->load->view('templates/sidebar_panel', $twitter, true);

            $this->load->view('pages/post_discussion', $data);
            $this->load->view('templates/footer', $data);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $xhrresult = array();
            $haveMedia = false;
            $uploadResult = true;
            // Get post data
            $data = $this->input->post();

            // Validate post data
            $flag = $this->_discAndResponseValidation($data);

            if (isset($_FILES['mediaFile']['name']) && trim($_FILES['mediaFile']['name']) != '') {
                $haveMedia = true;
                if ($this->_fileValidation($_FILES)) {
                    $flag = $this->_mediaDescrValiation($data);
                }
            }

            if ($flag) {
                $discussion_info = array(
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'havemedia' => 0 // Default set have media as '0', beacuse there is no media uploaded
                );

                // If have media then upload media and insert discussion post details in DB
                if ($haveMedia) {
                    $discussion_info['asset_id'] = 0;
                    $discussion_info['job_id'] = 0;

                    // Make file name using userid, current timestamp and concatinate string 'post'
                    $fileName = $this->session->userdata('userid') . date("YmdHis");
                    $fileName = 'post_' . $fileName . '.' . pathinfo($_FILES['mediaFile']['name'], PATHINFO_EXTENSION);
                    $discussion_info['media_path'] = $fileName;

                    // Get the tile file mime type eaither image or video
                    $fileType = explode('/', $_FILES['mediaFile']['type']);
                    $discussion_info['havemedia'] = ($fileType[0] == 'image') ? self::MIME_IMAGE : self::MIME_VIDEO;
                    $discussion_info['media_type'] = $_FILES['mediaFile']['type'];

                    $discussion_info['media_description'] = $data['mediaDescription'];

                    // We are going to use this container for storing images.
                    $container = $this->config->item('azure_storage_container_images');

                    // Get the file content
                    $fileContent = file_get_contents($_FILES['mediaFile']['tmp_name']);

                    set_time_limit(0);
                    if ($discussion_info['havemedia'] == self::MIME_IMAGE) {
                        // upload files to WA_BLOBCLOUD_CONTAINER
                        $uploadResult = $this->wablobstorage->uploadBlob($this->_cloud_container_images, $fileName, $fileContent);
                    } elseif ($discussion_info['havemedia'] == self::MIME_VIDEO) {
                        $asset_name = 'asset_' . $fileName; // shows in the content page
                        $uploadResult = $this->wamediaservice->uploadFileToMediaService($asset_name, $fileName, $fileContent);

                        if ($uploadResult) {
                            // Insert discussion/post details into table
                            $discussion_info['asset_id'] = $uploadResult->getId();

                            $sas_asset_name = 'Output_' . $asset_name; // shows in the content page {Output_asset_filename}
                            $sas_jobname = 'Job_' . $asset_name; // shows in the jobs page job name {Job_Output_asset_filename}
                            //error_log('-->BEFORE sasJOB uoload result '. print_r($uploadResult, true));
                            $sasjob = $this->wamediaservice->sasCreateJob($sas_jobname, $sas_asset_name, $uploadResult);
                            //error_log('-->AFTER sasJOB '. print_r($sasjob, true));                          

                            $discussion_info['job_id'] = $sasjob->getId();
                        }
                    }
                }

                if (!$uploadResult) {
                    $xhrresult = ['status' => false, 'message' => 'Something went wrong when saving the file, please try again.'];
                } else {
                    $result = $this->discAndResponseInsert($discussion_info, 'post');
                    $xhrresult = ['status' => $result['status'], 'message' => $result['message']];
                }
            } else {
                $xhrresult['status'] = false;
                $xhrresult['message'] = "Something went wrong when saving the file, please try again...";
            }
            echo json_encode($xhrresult);
        }
    }
*/
    /**
     * Summary of postMS
     * New post
     */
    public function postMS() {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('alert', 'danger');
            $this->session->set_flashdata('message', 'Please login to start new discussion');
            redirect('main/', 'refresh');
        }

        // If server request method is GET then load discussion post form
        // Else if server request method is POST then get the discussion post form details
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            
            $header['title'] = 'Welcome :: ' . WEBSITE_NAME;
            $data = array();

            $twitter['twitter_widget_panel'] = $this->load->view('templates/twitter_widget', '', true);
            $data['sidebar_panel'] = $this->load->view('templates/sidebar_panel', $twitter, true);
            
            $this->load->view('templates/header', $header);
            $this->load->view('templates/nav_bar', $data);
            $this->load->view('pages/post_discussionms', $data);
            $this->load->view('templates/footer', $data);
            
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $xhrResult = array();
            $sasWriteUrl = $assetId = $assetFilename = '';
            $uploadResult = true;

            // Get post data
            $data = $this->input->post();

            // Validate post data
            $flag = $this->_discAndResponseValidation($data);

            if ($flag && $data['haveMedia'] && $data['isImage'] && isset($_FILES['mediaFile']['name']) && trim($_FILES['mediaFile']['name']) != '') {
                if ($this->_fileValidation($_FILES)) {
                    $flag = $this->_mediaDescrValiation($data);
                }
            }

            if ($flag) {
                $discussion_info = array(
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'havemedia' => 0 // Default set have media as '0', because there is no media uploaded
                );

                // If have media then upload media and insert discussion post details in DB
                if ($data['haveMedia']) {
                    
                    $discussion_info['assetId'] = 0;
                    $discussion_info['jobId'] = 0;

                    // Make file name using userid, current timestamp and concatenate string 'post'
                    $userIdDate = $this->session->userdata('userid') . date("YmdHis");
                    
                    $fileName = 'post_' . $userIdDate . '.' . pathinfo($data['mediaFileName'], PATHINFO_EXTENSION);
                    
                    $discussion_info['mediaPath'] = $fileName;
                    $discussion_info['havemedia'] = ($data['isImage']) ? self::MIME_IMAGE : self::MIME_VIDEO;
                    $discussion_info['mediaType'] = $data['fileType'];
                    $discussion_info['mediaDescription'] = $data['mediaDescription'];
                       
                    if ($data['isImage']) {
                        // image upload to blob
                        $discussion_info['filename'] = $fileName;
                        // Get the file content                        
                        $fileContent = file_get_contents($_FILES['mediaFile']['tmp_name']);
                        // upload files to WA_BLOBCLOUD_CONTAINER
                        $uploadResult = $this->wablobstorage->uploadBlob($this->_cloud_container_images, $fileName, $fileContent);                       
                    } else {
                        // video content - prepare url for uploading through browser
                        $fileName = 'asset_'.$fileName;
                        $sasUri = $this->_getSasUri($fileName);
                        
                        if ($sasUri['status']) {
                            $discussion_info['assetId']         = $sasUri['message']['assetId'];                            
                            $discussion_info['locatorId']       = $sasUri['message']['locatorId'];
                            $discussion_info['accessPolicyId']  = $sasUri['message']['accessPolicyId'];
                            $discussion_info['sasWriteUrl']     = $sasUri['message']['sasWriteUrl'];
                            $discussion_info['filename']        = $sasUri['message']['filename'];
                            
                            $assetId        = $sasUri['message']['assetId'];
                            $assetFilename  = $sasUri['message']['filename'];
                            $sasWriteUrl    = $sasUri['message']['sasWriteUrl'];
                        } else {
                            $uploadResult = false;
                        }
                    }
                }

                if (!$uploadResult) {
                    $xhrResult = ['status' => false, 'message' => 'Something went wrong when saving the file, please try again.'];
                } else {
                    $result = $this->discAndResponseInsert($discussion_info, 'post');
                    $xhrResult = ['status' => $result['status'], 'message' => $result['message'], 'sasWriteUrl' => $sasWriteUrl, 'assetId' => $assetId, 'assetFileName' => $assetFilename];
                }
            } else {
                $xhrResult['status'] = false;
                $xhrResult['message'] = "Something went wrong when saving the file, please try again...";
            }
            echo json_encode($xhrResult);
        }
    }    
    /**
     * Summary of response
     * This function return only json_encode data, because this function purely for AJAX/XHR
     * Login user can response to any discussion
     * So user can post a response with text and upload image OR video but media is optional
     * If media uploaded then media description is required
     * 
     * IF user is not loggedin then send a message back to user like Please login to provide response
     */
    /*
    public function response() {
        $xhrresult = array();
        $haveMedia = false;
        $uploadResult = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->session->userdata('logged_in')) {
            // Get the post data
            $data = $this->input->post();

            // Validate the post data
            $flag = $this->_discAndResponseValidation($data);

            if (isset($_FILES['mediaFile']['name']) && trim($_FILES['mediaFile']['name']) != '') {
                $haveMedia = true;
                if ($this->_fileValidation($_FILES)) {
                    $flag = $this->_mediaDescrValiation($data);
                }
            }

            if ($flag) {
                $response_info = array(
                    'content' => $data['content'],
                    'postid' => $data['postId'],
                    'userid' => $this->session->userdata('userid'),
                    'havemedia' => 0 // Default set have media as '0', beacuse there is no media uploaded
                );

                // do upload send argument form file field name
                if ($haveMedia) {
                    $response_info['asset_id'] = 0;
                    $response_info['job_id'] = 0;

                    $fileName = $this->session->userdata('userid') . date("YmdHis");
                    $fileName = 'resp_'.$fileName.'.'.pathinfo($_FILES['mediaFile']['name'], PATHINFO_EXTENSION);
                    $response_info['media_path'] = $fileName;
                    $response_info['filename'] = $fileName;

                    // Get the tile file mime type eaither image or video
                    $fileType = explode('/', $_FILES['mediaFile']['type']);
                    $response_info['havemedia'] = ($fileType[0] == 'image') ? self::MIME_IMAGE : self::MIME_VIDEO;
                    $response_info['media_type'] = $_FILES['mediaFile']['type'];
                    $response_info['media_description'] = $data['mediaDescription'];

                    // Get the file content
                    $fileContent = file_get_contents($_FILES['mediaFile']['tmp_name']);

                    set_time_limit(0);
                    if ($response_info['havemedia'] == self::MIME_IMAGE) {
                        // upload files to WA_BLOB
                        $uploadResult = $this->wablobstorage->uploadBlob($this->_cloud_container_images, $fileName, $fileContent);
                    } elseif ($response_info['havemedia'] == self::MIME_VIDEO) {
                        $asset_name = 'asset_' . $fileName; // shows in the content page
                        $uploadResult = $this->wamediaservice->uploadFileToMediaService($asset_name, $fileName, $fileContent);

                        if ($uploadResult) {
                            $response_info['asset_id'] = $uploadResult->getId();

                            $sas_asset_name = 'Output_' . $asset_name; // shows in the content page
                            $sas_jobname = 'Job_' . $asset_name; // shows in the jobs page job name
                            //error_log('-->BEFORE sasJOB upload result '. print_r($uploadResult, true));
                            $sasjob = $this->wamediaservice->sasCreateJob($sas_jobname, $sas_asset_name, $uploadResult);
                            //error_log('-->AFTER sasJOB '. print_r($sasjob, true));

                            $response_info['job_id'] = $sasjob->getId();
                        }
                    }
                }

                // If uploadResult is false set status as false
                // else update DB with discussion info
                // Note: uploadResult is default true, so  we can if media is available or not 
                if (!$uploadResult) {
                    $xhrresult = ['status' => false, 'message' => 'Something went wrong when saving the file, please try again.'];
                } else {
                    // Insert responses/thred details of parent post info in table
                    $result = $this->discAndResponseInsert($response_info, 'responses');
                    $xhrresult = ['status' => $result['status'], 'message' => $result['message']];
                }
            } else {
                $xhrresult = ['status' => false, 'message' => 'There are some validation errors, So please try again.'];
            }
        } else {
            $xhrresult = ['status' => false, 'message' => 'To respond to this discussion, you must login.'];
        }// IF else finish session->userdata('logged_in') and POST

        echo json_encode($xhrresult);
    }
*/
    public function responsems() {
        $xhrresult = array();
        $sasWriteUrl = $assetId = $assetFilename = '';
        $uploadResult = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->session->userdata('logged_in')) {
            // Get the post data
            $data = $this->input->post();

            // Validate the post data
            $flag = $this->_discAndResponseValidation($data);

            if ($flag && $data['haveMedia'] && $data['isImage'] && isset($_FILES['mediaFile']['name']) && trim($_FILES['mediaFile']['name']) != '') {
                if ($this->_fileValidation($_FILES)) {
                    $flag = $this->_mediaDescrValiation($data);
                }
            }
            
            if ($flag) {
                $response_info = array(
                    'content' => $data['content'],
                    'postid' => $data['postId'],
                    'userid' => $this->session->userdata('userid'),
                    'havemedia' => 0 // Default set have media as '0', beacuse there is no media uploaded
                );

                // do upload send argument form file field name
                if ($data['haveMedia']) {
                    $response_info['assetId'] = 0;
                    $response_info['jobId'] = 0;

                    // Make file name using userid, current timestamp and concatenate string 'post'
                    $userIdDate = $this->session->userdata('userid') . date("YmdHis");                   
                    $fileName = 'resp_'.$userIdDate.'.'.pathinfo($data['mediaFile'], PATHINFO_EXTENSION);
                    
                    $response_info['mediaPath']    = $fileName;                    
                    $response_info['havemedia']     = ($data['isImage']) ? self::MIME_IMAGE : self::MIME_VIDEO;
                    $response_info['mediaType']     = $data['fileType'];
                    $response_info['mediaDescription'] = $data['mediaDescription']; 
                    if ($data['isImage']) {
                         // image upload to blob
                        $response_info['filename'] = $fileName;
                        // Get the file content
                        $fileContent = file_get_contents($_FILES['mediaFile']['tmp_name']);
                        // upload files to WA_BLOBCLOUD_CONTAINER
                        $uploadResult = $this->wablobstorage->uploadBlob($this->_cloud_container_images, $fileName, $fileContent);
                    }else{
                        // video content - prepare url for uploading through browser
                        $fileName = 'asset_'.$fileName;
                        $sasUri = $this->_getSasUri($fileName);
                        if ($sasUri['status']) {
                            $response_info['assetId']         = $sasUri['message']['assetId'];                            
                            $response_info['locatorId']       = $sasUri['message']['locatorId'];
                            $response_info['accessPolicyId']  = $sasUri['message']['accessPolicyId'];
                            $response_info['sasWriteUrl']     = $sasUri['message']['sasWriteUrl'];
                            $response_info['filename']        = $sasUri['message']['filename'];
                            
                            $assetId        = $sasUri['message']['assetId'];
                            $assetFilename  = $sasUri['message']['filename'];
                            $sasWriteUrl    = $sasUri['message']['sasWriteUrl'];
                        } else {
                            $uploadResult = false;
                        }                        
                    }

                    // Get the tile file mime type eaither image or video
                   // $fileType = explode('/', $_FILES['mediaFile']['type']);
                   // $response_info['havemedia'] = ($fileType[0] == 'image') ? self::MIME_IMAGE : self::MIME_VIDEO;
                    //$response_info['media_type'] = $_FILES['mediaFile']['type'];
                   // $response_info['media_description'] = $data['mediaDescription'];

                    // Get the file content
                   // $fileContent = file_get_contents($_FILES['mediaFile']['tmp_name']);

                  //  set_time_limit(0);
                   // if ($response_info['havemedia'] == self::MIME_IMAGE) {
                        // upload files to WA_BLOB
                       // $uploadResult = $this->wablobstorage->uploadBlob($this->_cloud_container_images, $fileName, $fileContent);
                   // } elseif ($response_info['havemedia'] == self::MIME_VIDEO) {
                      //  $asset_name = 'asset_' . $fileName; // shows in the content page
                      //  $uploadResult = $this->wamediaservice->uploadFileToMediaService($asset_name, $fileName, $fileContent);

                       // if ($uploadResult) {
                        //    $response_info['asset_id'] = $uploadResult->getId();

                       //     $sas_asset_name = 'Output_' . $asset_name; // shows in the content page
                       //     $sas_jobname = 'Job_' . $asset_name; // shows in the jobs page job name
                            //error_log('-->BEFORE sasJOB upload result '. print_r($uploadResult, true));
                     //       $sasjob = $this->wamediaservice->sasCreateJob($sas_jobname, $sas_asset_name, $uploadResult);
                            //error_log('-->AFTER sasJOB '. print_r($sasjob, true));

                     //       $response_info['job_id'] = $sasjob->getId();
                    //    }
                  //  }
                }

                // If uploadResult is false set status as false
                // else update DB with discussion info
                // Note: uploadResult is default true, so  we can if media is available or not 
                if (!$uploadResult) {
                    $xhrresult = ['status' => false, 'message' => 'Something went wrong when saving the file, please try again.'];
                } else {
                    // Insert responses/thred details of parent post info in table
                    $result = $this->discAndResponseInsert($response_info, 'responses');
                    $xhrresult = ['status' => $result['status'], 'message' => $result['message'], 'sasWriteUrl' => $sasWriteUrl, 'assetId' => $assetId, 'assetFileName' => $assetFilename];
                   // $xhrresult = ['status' => $result['status'], 'message' => $result['message']];
                }
            } else {
                $xhrresult = ['status' => false, 'message' => 'There are some validation errors, So please try again.'];
            }
        } else {
            $xhrresult = ['status' => false, 'message' => 'To respond to this discussion, you must login.'];
        }// IF else finish session->userdata('logged_in') and POST

        echo json_encode($xhrresult);
    }
    /**
     * Summary of updateWaMsJobStatus
     * Get and Update Windows Azure Media Service video convert job satus in DB
     */
    public function updateWaMsJobStatus() {
        //if(ENVIRONMENT == 'development'){$this->output->enable_profiler(TRUE);}
        error_log(' calling info ' . __CLASS__ . ' ' . __METHOD__);

        $result = $this->discussion_model->getMediaJobIds();

        //error_log('DB Result '.print_r($result, true));
        if (isset($result) && !empty($result)) {
            foreach ($result AS $key => $val) {
                //updateMediaJobStatus
                $job = $this->wamediaservice->getJobByJobId($val->job_id);
                error_log('job ' . print_r($job, true));

                $jobState = $this->wamediaservice->getJobState($job);
                error_log('jobState ' . $jobState);

                $txtmsg = $this->wamediaservice->getJobStatusMessage($job);
                error_log('msg ' . $txtmsg);

                if ($txtmsg) {
                    $filename = pathinfo($val->file_name)['filename']; // php 5.4 array feature
                    $sasMediaResult = $this->wamediaservice->getSasLocator('Output_asset_' . $val->media_path, $filename); // {sas_asset_name, filename without extension}
                    if ($sasMediaResult['status']) {
                        $this->discussion_model->updateMediaJobStatus($val->id, $sasMediaResult['media_path']);
                    }
                }
            }
        }
    }

    /**
     * Summary of discAndResponseValidation
     * Discussion and Responsive post data validation
     * @param $data form submission data
     * @return boolean
     */
    private function _discAndResponseValidation($data) {
        $flag = (isset($data['content']) && trim($data['content']) != '') ? true : false;
        if (isset($data['postid']) && empty($data['postid'])) {
            $flag = false;
        }

        if (isset($data['title'])) {
            $flag = (empty($data['title'])) ? false : true;
            $flag = ($flag && strlen($data['title'] <= 60)) ? true : false;
        }
        return $flag;
    }

    /**
     * Summary of fileValidation
     * post file validation
     * @param $_FILES form submission data
     * @return boolean
     */
    private function _fileValidation($file) {
        $flag = true;
        $allowed_media_types = ['asf, avi, m2ts, m2v, mp4, mpeg, mpg, mts, ts, wmv, 3gp, 3g2, 3gp2, mod, dv, vob, ismv, m4a, png, gif, JPEG, jpg'];
        $current_media_type = pathinfo($file['mediaFile']['name'], PATHINFO_EXTENSION);

        // Find is media file is allowable type or not
        if (!in_array($current_media_type, $allowed_media_types)) {
            $flag = false;
        }

        if ($file['mediaFile']['error'] > 0) {
            $flag = false;
        }
        return $flag;
    }

    /**
     * Summary of _mediaDescrValiation
     * @param mixed $data 
     * @return mixed
     */
    private function _mediaDescrValiation($data) {
        $flag = false;
        if (isset($data['mediaDescription']) && trim($data['mediaDescription']) != '') {
            $flag = (strlen($data['mediaDescription'] <= 140)) ? true : false;
        }
        return $flag;
    }

    /**
     * Summary of discAndResponseInsert
     * @param mixed $data 
     * @param mixed $postType 
     * @return mixed
     */
    private function discAndResponseInsert($data, $postType) {
        if ($postType == 'post') {
            // Insert discussion post info in table
            $result = $this->discussion_model->insertPost($data);
        } elseif ($postType == 'responses') {
            // Insert discussion post info in table
            error_log('data '.print_r($data, true));
            $result = $this->discussion_model->insertResponse($data);
        }
        return ($result) ? array('status' => true, 'message' => 'Details uploaded sucessfully') : array('status' => false, 'message' => 'Something went wrong, please try again.');
    }

    /**
     * Summary of showMedia
     * @param mixed $filetype file mime type like mp4 3gpp
     * @param mixed $disc_type eaither post or resp
     */
//    public function getSasUri($filetype, $disc_type) {
//        $xhrresult['status'] = true;
//        if (!$this->session->userdata('logged_in')) {
//            $xhrresult['status'] = false;
//            $xhrresult['message'] = "Please login to start new disussion";
//        } else {
//            $userid_date = $this->session->userdata('userid') . date("YmdHis");
//            $filename = 'asset_' . $disc_type . '_' . $userid_date;
//            $xhrresult['message'] = $this->wamediaservice->getAssetSasUrl($filename);
//        }
//        echo json_encode($xhrresult);
//    }
    /**
     * Summary of _getSasUri
     * @param mixed $disc_type either post or resp
     * @return json string
     */
    private function _getSasUri($fileName) {
        $xhrResult['status'] = true;
        if (!$this->session->userdata('logged_in')) {
            $xhrResult['status'] = false;
            $xhrResult['message'] = "Please login to start new discussion";
        } else {
            $xhrResult['message'] = $this->wamediaservice->getAssetSasUrl($fileName);
            $xhrResult['message']['filename'] = $fileName;
        }
        //echo json_encode($xhrResult);
        return $xhrResult;
    }
    

    /**
     * Summary of Create Job using assetid and asset file name
     *
     * @param string $assetId
     * @param string $assetFileName
     */
    public function createJob($assetId, $assetFileName) {
       // error_log('test here 3'); //exit;
        $xhrResult = array();
        
       $xhrResult['status'] = false;

        // get the asset.
        $assetResult = $this->wamediaservice->getAsset($assetId);
        // set the file info in media services.
        $this->wamediaservice->createFileInfos($assetResult);
        // get the uploaded access policy and locator
        $result = $this->discussion_model->getLocatorIdAndAccessPolicyIdByAssetId($assetId);

        if ($result) {
            $locator = $this->wamediaservice->getLocator($result[0]->locator_id);
            $access_policy = $this->wamediaservice->getAccessPolicy($result[0]->access_policy_id);
            // clean access policy and locator
            $res1 = $this->wamediaservice->deleteLocator($locator);
            $res2 = $this->wamediaservice->deleteAccessPolicy($access_policy);
        }

        $sas_asset_name = 'Output_' . $assetFileName; // shows in the content page {Output_asset_filename}            
        $sas_jobname = 'Job_' . $assetFileName; // shows in the jobs page job name {Job_Output_asset_filename}

        $sasjob = $this->wamediaservice->sasCreateJob($sas_jobname, $sas_asset_name, $assetResult);

        $xhrResult['status'] = $this->discussion_model->insertJobId($assetId, $sasjob->getId());

      //  error_log('sasjob' . print_r($sasjob, true));
       //echo $xhrResult;
       echo json_encode($xhrResult);
    }    

    /**
     * Summary of post
     */
    /*
    public function postms() {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('alert', 'danger');
            $this->session->set_flashdata('message', 'Please login to start new disussion');
            redirect('main/', 'refresh');
        }

        // If server request method is GET then load discussion post form
        // Else if server request method is POST then get the discussion post form details
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $header['title'] = 'Welcome :: ' . WEBSITE_NAME;
            $data = array();
            $this->load->view('templates/header', $header);
            $this->load->view('templates/nav_bar', $data);
            $this->load->view('pages/post_discussionms', $data);
            $this->load->view('templates/footer', $data);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $xhrresult = array();
            $haveMedia = false;
            $uploadResult = true;
            // Get post data
            $data = $this->input->post();

            // Validate post data
            $flag = $this->_discAndResponseValidation($data);

            if (isset($_FILES['mediaFile']['name']) && trim($_FILES['mediaFile']['name']) != '') {
                $haveMedia = true;
                if ($this->_fileValidation($_FILES)) {
                    $flag = $this->_mediaDescrValiation($data);
                }
            }

            if ($flag) {
                $discussion_info = array(
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'havemedia' => 0 // Default set have media as '0', beacuse there is no media uploaded
                );

                // If have media then upload media and insert discussion post details in DB
                if ($haveMedia) {
                    $discussion_info['asset_id'] = 0;
                    $discussion_info['job_id'] = 0;

                    // Make file name using userid, current timestamp and concatinate string 'post'
                    $fileName = $this->session->userdata('userid') . date("YmdHis");
                    $fileName = 'post_' . $fileName . '.' . pathinfo($_FILES['mediaFile']['name'], PATHINFO_EXTENSION);
                    $discussion_info['media_path'] = $fileName;

                    // Get the tile file mime type eaither image or video
                    $fileType = explode('/', $_FILES['mediaFile']['type']);
                    $discussion_info['havemedia'] = ($fileType[0] == 'image') ? self::MIME_IMAGE : self::MIME_VIDEO;
                    $discussion_info['media_type'] = $_FILES['mediaFile']['type'];

                    $discussion_info['media_description'] = $data['mediaDescription'];

                    // We are going to use this container for storing images.
                    $container = $this->config->item('azure_storage_container_images');

                    // Get the file content
                    $fileContent = file_get_contents($_FILES['mediaFile']['tmp_name']);

                    set_time_limit(0);
                    if ($discussion_info['havemedia'] == self::MIME_IMAGE) {
                        // upload files to WA_BLOBCLOUD_CONTAINER
                        $uploadResult = $this->wablobstorage->uploadBlob($this->_cloud_container_images, $fileName, $fileContent);
                    } elseif ($discussion_info['havemedia'] == self::MIME_VIDEO) {
                        $asset_name = 'asset_' . $fileName; // shows in the content page
                        $uploadResult = $this->wamediaservice->uploadFileToMediaService($asset_name, $fileName, $fileContent);

                        if ($uploadResult) {
                            // Insert discussion/post details into table
                            $discussion_info['asset_id'] = $uploadResult->getId();

                            $sas_asset_name = 'Output_' . $asset_name; // shows in the content page {Output_asset_filename}
                            $sas_jobname = 'Job_' . $asset_name; // shows in the jobs page job name {Job_Output_asset_filename}

                            error_log('-->BEFORE sasJOB uoload result ' . print_r($uploadResult, true));
                            $sasjob = $this->wamediaservice->sasCreateJob($sas_jobname, $sas_asset_name, $uploadResult);
                            error_log('-->AFTER sasJOB ' . print_r($sasjob, true));

                            $discussion_info['job_id'] = $sasjob->getId();
                        }
                    }
                }

                if (!$uploadResult) {
                    $xhrresult = ['status' => false, 'message' => 'Something went wrong when saving the file, please try again.'];
                } else {
                    $result = $this->discAndResponseInsert($discussion_info, 'post');
                    $xhrresult = ['status' => $result['status'], 'message' => $result['message']];
                }
            } else {
                $xhrresult['status'] = false;
                $xhrresult['message'] = "Something went wrong when saving the file, please try again...";
            }
            echo json_encode($xhrresult);
        }
    }
    */

}

/* End of file disussion.php */
      /* Location: ./application/controllers/disussion.php */