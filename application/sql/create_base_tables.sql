-- phpMyAdmin SQL Dump
-- version 4.1.9
-- http://www.phpmyadmin.net
--
-- Host: ap-cdbr-azure-east-b.cloudapp.net
-- Generation Time: May 13, 2014 at 12:55 PM
-- Server version: 5.5.21-log
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cdb_65a2581577`
--

-- --------------------------------------------------------

--
-- Table structure for table `community_data_cron`
--

CREATE TABLE IF NOT EXISTS `community_data_cron` (
  `id` int(11) NOT NULL,
  `last_processed_id` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='community cron jobs find the last processed record';

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_path` varchar(1000) DEFAULT NULL COMMENT 'Media Path',
  `media_type` varchar(50) DEFAULT NULL COMMENT 'Media Type',
  `media_description` varchar(160) NOT NULL COMMENT 'Media Description',
  `postid` int(11) NOT NULL DEFAULT '0' COMMENT 'Post id',
  `responseid` int(11) NOT NULL DEFAULT '0' COMMENT 'Response id',
  `asset_id` varchar(500) DEFAULT NULL,
  `job_id` varchar(500) NOT NULL DEFAULT '0',
  `job_status` tinyint(4) NOT NULL DEFAULT '0',
  `access_policy_id` varchar(64) DEFAULT NULL COMMENT 'sas access policy id',
  `locator_id` varchar(64) DEFAULT NULL COMMENT 'sas locator id',
  `sas_write_url` varchar(1000) DEFAULT NULL COMMENT 'sas write url',
  `file_name` varchar(100) NOT NULL COMMENT 'file name',
  `media_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time stamp',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Discussion Media' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `content` text NOT NULL,
  `userid` int(11) NOT NULL,
  `havemedia` tinyint(1) NOT NULL,
  `post_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Discussion Posts' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `post_responses`
--

CREATE TABLE IF NOT EXISTS `post_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `postid` int(11) NOT NULL COMMENT 'User id from users table',
  `no_of_post_responses` int(11) NOT NULL DEFAULT '0' COMMENT 'No of responses have each post',
  `updated_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp when this row updated',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='info about, how many responses have each post' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Product Id',
  `name` varchar(250) NOT NULL COMMENT 'Product Name',
  `url` varchar(500) NOT NULL COMMENT 'uri of product name for better SEO url names',
  `created_date` datetime NOT NULL COMMENT 'Product created date and time',
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Product update date and time',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_components`
--

CREATE TABLE IF NOT EXISTS `product_components` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'component Id',
  `component_type` varchar(250) NOT NULL COMMENT 'Component Type',
  `component_details` text NOT NULL COMMENT 'Component Details',
  `product_id` int(11) NOT NULL COMMENT 'component of product ID',
  `created_date` datetime NOT NULL COMMENT 'component created date',
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'component updated date',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_media`
--

CREATE TABLE IF NOT EXISTS `product_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'product media id',
  `image_name` varchar(500) NOT NULL COMMENT 'product image name (We will store media in file db)',
  `video_name` varchar(500) NOT NULL COMMENT 'product video name (We will store media in file db)',
  `product_id` int(11) NOT NULL COMMENT 'product id',
  `product_component_id` int(11) DEFAULT NULL COMMENT 'product component id',
  `created_date` datetime NOT NULL COMMENT 'row created date and time',
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'row updated date and time',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE IF NOT EXISTS `responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `postid` int(11) NOT NULL COMMENT 'post id',
  `userid` int(11) NOT NULL COMMENT 'user id, who gave response to the post',
  `havemedia` tinyint(1) NOT NULL,
  `response_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Discussion Responses' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'User id auto incement and pk',
  `username` varchar(200) NOT NULL COMMENT 'currently email',
  `email` varchar(200) NOT NULL COMMENT ' email address',
  `password` varchar(40) DEFAULT NULL COMMENT 'password in md5 ',
  `social_userid` varchar(32) NOT NULL COMMENT 'social network user id',
  `social_provider` varchar(50) DEFAULT NULL COMMENT 'social identity',
  `social_token` varchar(50) DEFAULT NULL COMMENT 'social identity',
  `tokens` varchar(50) DEFAULT NULL COMMENT 'social identity',
  `name` varchar(100) NOT NULL,
  `avatar` varchar(256) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `twitter_handle` varchar(100) DEFAULT NULL,
  `notif_special` tinyint(1) NOT NULL,
  `notif_product` tinyint(1) NOT NULL,
  `notif_post` tinyint(1) NOT NULL,
  `notif_tweet` tinyint(1) NOT NULL,
  `created_timestamp` datetime NOT NULL,
  `updated_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'auto update on row updation',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Users info' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_posts`
--

CREATE TABLE IF NOT EXISTS `user_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT 'User id from users table',
  `no_of_posts` int(11) NOT NULL COMMENT 'No of posts posted by user',
  `no_of_responses` int(11) NOT NULL DEFAULT '0' COMMENT 'No of responses posted by user',
  `updated_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'timestamp when this row updated',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='user info about no of posts and no of responses' AUTO_INCREMENT=1 ;


--
-- Dumping data for table `community_data_cron`
--

INSERT INTO `community_data_cron` (`id`, `last_processed_id`) VALUES
(1, 0),
(2, 0);  
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
