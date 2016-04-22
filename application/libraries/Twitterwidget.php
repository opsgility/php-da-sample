<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Twitter Twitterwidget library.
  * Requirements: Twitter widget id
  */


class Twitterwidget extends CI_Controller
{
        const TWITTER_WIDGET_ID = '444344809403080704'; // this is widget id for displaying tweets from twitter
        const TWITTER_API_URL = 'https://twitter.com/twitterapi';
        const TWITTER_TWEET_LIMIT = '10';
        
        const TWITTER_WIDGET_ID_HASHTAG_1 = '443652092536684544';
        
        public static $twitter_widget_hashtag = array(
                                                '444344809403080704'=>'#html #css', 
                                                '443652092536684544'=>'#html'
                                                    );
        

        function __construct()
	{
		parent::__construct();
	}
        
        /*
         * get the widget for display
         */
	
        public function get_twitter_widget(){
            // http://platform.twitter.com/widgets.js is added directly in the javascript.
            
            return '<a class="twitter-timeline" href="'.self::TWITTER_API_URL.'" data-widget-id="'.self::TWITTER_WIDGET_ID.'" data-related="twitterapi,twitter">Tweet '.self::$twitter_widget_hashtag[self::TWITTER_WIDGET_ID].'</a>';
        }
}

/* End of file Twitterwidget.php */
/* Location: ./application/libraries/Twitterwidget.php */