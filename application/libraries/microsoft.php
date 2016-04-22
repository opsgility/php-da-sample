<?php
if (!function_exists('curl_init')) {
  exit('Microsoft Class Needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  exit('Microsoft Class Needs the JSON PHP extension.');
}

class MocrosoftLiveCnt
{
    private $clientId;
	private $clientSecret;
	private $scope;
	private $redirectUrl;
	
	//construct
    public function __construct($params) {
		$this->clientId 		= (string) $params['client_id'];
		$this->clientSecret  	= (string) $params['client_secret'];
		$this->scope 			= (string) $params['client_scope'];
		$this->redirectUrl  	= (string) $params['redirect_url'];
    }
	
	//return redirect url
	public function GetRedirectUrl()
	{
		return $this->redirectUrl;
	}
	
	//return microsoft login url
	public function GetLoginUrl()
	{
		$redirect_url	= urlencode($this->redirectUrl);
		$scope 			= urlencode($this->scope);
		$clientid 		= urlencode($this->clientId);
		$dialog_url 	= 'https://login.live.com/oauth20_authorize.srf?client_id='.$clientid.'&scope='.$scope.'&response_type=code&redirect_uri='.$redirect_url;
		return $dialog_url;
	}
	
	//get user details
	public function getUser($code)
	{
		$getAccessToken = $this->getAccessToken($code);
	    $url = 'https://apis.live.net/v5.0/me?access_token='.$getAccessToken;
		$result = json_decode($this->HttpPost($url));
		
		if(!empty($result->error))
		{
			return false;
		}else{
			return $result;
		}
	}
	
	//get access token
	public function getAccessToken($code=null)
	{
			$token = $this->getSessionVar('ms_access_token');
			
			if($token && !$code) {
				return $token;
			}else{
				$url = 'https://login.live.com/oauth20_token.srf';
				$fields = array(
					'client_id' => urlencode($this->clientId),
					'client_secret' => urlencode($this->clientSecret),
					'redirect_uri' => urlencode($this->redirectUrl),
					'code' => urlencode($code),
					'grant_type' => urlencode('authorization_code')
					); 
				$result = $this->HttpPost($url,1,$fields);
                
				if(!$result) {
					return false;
				}

                $authCode=json_decode($result);
                
                if (!empty($authCode->access_token)) {
                    return $authCode->access_token;
                }else{
                    return false;
                }
			}
	}
	
	//set access token
	public function setAccessToken($token)
	{
		$this->setSessionVar('ms_access_token', $token);
	}
	
	//distroy all sessions	
	public function distroySession(){
		$this->initiateSession();
		session_destroy();
	}
	
	//httppost
	private function HttpPost($url=null,$post=0,$postargs=array())
	{
        
        $fields_string = '';
		$ch = curl_init();        
		curl_setopt($ch,CURLOPT_URL, $url);
		if($post)
		{           
			foreach($postargs as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			//$url1=rtrim($fields_string, '&');$url=$url.'?'.$url1;
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string); 
		}
        
        curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POST, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
       
       ///////////////////////////////
       /* $ch = curl_init();
        if($post)
		{           
			foreach($postargs as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			$url1=rtrim($fields_string, '&');$url=$url.'?'.$url1;
			//curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string); 
		}
        curl_setopt ($ch, CURLOPT_URL, $url );

        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");

        curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 0);
        //curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt ($ch, CURLOPT_REFERER, "vineeshjosephone.com/index.php/users/afterloginfromms");

        //curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
        //curl_setopt ($ch, CURLOPT_POST, 1);
        $result = curl_exec ($ch); 
        return $result;*/
	}
	
	//set session variables
	private function setSessionVar($key, $value)
	{
		$this->initiateSession();
		$_SESSION[$key] = $value;
	}
	
	//return session variables
	private function getSessionVar($key)
	{
		$this->initiateSession();
		if(isset($_SESSION[$key])){
		return $_SESSION[$key];
		}
	}
	
	//session start
	private function initiateSession()
	{
		if (!session_id()) {
		  session_start();
		}	
	}

}


?>
