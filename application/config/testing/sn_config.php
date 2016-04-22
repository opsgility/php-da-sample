<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Social network configuration for OAuth
// All the social logins are based on the contosoweb@outlook.com account
// all social login email/username is contosoweb@outlook.com password is mentioned in the project document
// staging or testing apps
/*
|--------------------------------------------------------------------------
| Twitter app info 
|--------------------------------------------------------------------------
|
| if don't have twitter app for this web site please create in twitter. 
| API information here
|
|	https://apps.twitter.com/
|
| If this is not set twitter auth won't work
|
*/

$config['twitter_consumer_key']   	= '[Application Settings - API key]';
$config['twitter_consumer_secret']  = '[Application Settings - API secret]';
$config['twitter_access_token']     = '[Your access token - Access token]';   
$config['twitter_access_secret']    = '[Your access token - Access token secret]';  
$config['twitter_oauth_callback']   = 'http://[YOUR WEB SITE NAME].azurewebsites.net/users/callback';

/*
|--------------------------------------------------------------------------
| Facebook app info 
|--------------------------------------------------------------------------
|
| if don't have facebook app for this web site please create in facebook.
| API information here
|
|	https://developers.facebook.com
|
| If this is not set facebook auth won't work
|
*/
$config['facebook_appId']	= '[Facebook - App Id]'; 
$config['facebook_secret']  = '[Facebook - App Secret]';



/*
|--------------------------------------------------------------------------
| Microsoft app info 
|--------------------------------------------------------------------------
|
| if don't have microsoft app for this web site please create in microsoft.
| API information here
|
|	https://account.live.com
|   https://account.live.com/developers/applications/index
|
| If this is not set microsoft auth won't work
|
*/
$config['microsoft_appid']        = '[Microsoft - Client ID]'; 
$config['microsoft_app_secret']   = '[Microsoft - Client secret]';
$config['microsoft_scope']        = 'wl.basic wl.emails'; 
$config['microsoft_redirect_url'] = 'http://[YOUR WEB SITE NAME].azurewebsites.net/users/afterLoginFromMs'; 

/*
|--------------------------------------------------------------------------
| Google app info 
|--------------------------------------------------------------------------
|
| if don't have google app for this web site please create in google.
| API information here
|
|	https://console.developers.google.com/project
|
| If this is not set google auth won't work;you want apply this in library/google/src/config.php oauth2_redirect_uri also
|
*/
$config['google_appid']           = '[Google - Client ID]'; 
$config['google_app_secret']      = '[Google - Client secret]'; 
$config['google_redirect_url']    = 'http://[YOUR WEB SITE NAME].azurewebsites.net/users/googleLogin';
$config['google_aplication_name'] = 'Contoso Digital Agency';


/* End of file sn_config.php */
/* Location: ./application/config/sn_config.php */
