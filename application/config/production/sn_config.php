<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Social network configuration for OAuth
// All the social logins are based on the contosoweb@outlook.com account
// all social login email/username is contosoweb@outlook.com password is mentioned in the project document
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

$websiteName = getenv('APPSETTING_WEBSITE_SITE_NAME');

$config['twitter_consumer_key']   	= getenv('APPSETTING_TW_API_KEY');
$config['twitter_consumer_secret']  = getenv('APPSETTING_TW_API_SECRET');
$config['twitter_access_token']     = getenv('APPSETTING_TW_ACCESS_TOKEN');   
$config['twitter_access_secret']    = getenv('APPSETTING_TW_ACCESS_TOKEN_SECRET'); 
$config['twitter_oauth_callback']   = 'http://' . $websiteName . '.azurewebsites.net/users/callback';

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
$config['facebook_appId']	= getenv('APPSETTING_FB_APP_ID');
$config['facebook_secret']  = getenv('APPSETTING_FB_APP_SECRET');



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
$config['microsoft_appid']        = getenv('APPSETTING_MS_CLIENT_ID');
$config['microsoft_app_secret']   = getenv('APPSETTING_MS_CLIENT_SECRET');
$config['microsoft_scope']        = 'wl.basic wl.emails'; 
$config['microsoft_redirect_url'] = 'http://' . $websiteName . '.azurewebsites.net/users/afterLoginFromMs'; 

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
$config['google_appid']           = getenv('APPSETTING_GGL_CLIENT_ID'); 
$config['google_app_secret']      = getenv('APPSETTING_GGL_CLIENT_SECRET');
$config['google_redirect_url']    = 'http://' . $websiteName . '.azurewebsites.net/users/googleLogin';
$config['google_aplication_name'] = 'Contoso Digital Agency';


/* End of file sn_config.php */
/* Location: ./application/config/sn_config.php */
