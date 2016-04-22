<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * This is class News_model
	 *
	 */	 
	class News_model extends CI_Model{
		
		/**
		 * This is method __construct
		 *
		 * @return mixed This is the return value description
		 *
		 */		
		public function __construct(){
			parent::__construct();			
		}
		
		/**
		 * This is method getNews
		 *
		 * @param mixed $count This is a description
		 * @return mixed This is the return value description
		 *
		 */			
		public function getNews($count=0) {
			
			$items = $this->loadRss();

			function rss_sort_asc($a, $b)
			{
				$a_startDate = strtolower($a->pubDate);
				$b_startDate = strtolower($b->pubDate);
				if ($a_startDate == $b_startDate) {
					return 0;
				}
				// Reverse the results for latest news
				return ($a_startDate > $b_startDate) ? -1 : 1;
			}			
			// Sort an array by values using a user-defined {'rss_sort_asc'} comparison function
			usort($items, 'rss_sort_asc');

			$count = ($count == 0) ? count($items) : $count; // find if count is 0 then load all news
			return array_slice($items, 0, $count); // Slice results based on count
		}
		
		/**
		 * This is method getFeed
		 *
		 * @param mixed $title This is a description
		 * @return mixed This is the return value description
		 *
		 */		
		public function getFeed($link){					
			$items = $this->loadRss();
			$feed = '';
			
			//Strip index.php from the link
			$link = parse_url($link);
			$stripLink = str_replace('/index.php', '', $link['path']);
					
			// Loop all feeds
			foreach ($items as $value)
			{
				$l = parse_url($value->link);	

				if ($l['path'] == $stripLink)
				{
					// If find the value then break the loop and return
					$feed = $value;
					break;
				}
			}			
			return  $feed;
		}
		
		/**
		 * This is method loadRss
		 *
		 * @return mixed This is the return value description
		 *
		 * Load Rss xml file and return channel items
		 */
		private function loadRss(){
			//Load the feed using simplexml
			$rss = new SimpleXMLElement(base_url().'news.xml', null, true);

			$num_items = count($rss->channel->item);
			
			//array_slice() returns the sequence of elements from the array array as specified by the offset and length parameters. 
			$items = array_slice($rss->xpath('channel/item'), 0, $num_items);
			return $items;
		}
	}
	
/* End of file news_model.php */
/* Location: ./application/models/news_model.php */