<?php
use EDAM\oAuth\tmhOAuth;

class Auth2Helper
{
	private $oAuth;
	private $tmh;

	private function outputError() {
		echo 'Error: ' . $this->oAuth->response['response'] . PHP_EOL;
		TmhUtilitiesHelper::pr($this->oAuth);
	}

	public function Auth2Helper()
	{
		$this->oAuth = new tmhOAuth(array(
			'consumer_key'    => OAUTH_CONSUMER_KEY,
			'consumer_secret' => OAUTH_CONSUMER_SECRET,
		));

		$tmh = TmhUtilitiesHelper::php_self();
		// reset request?
		if(isset($_REQUEST['wipe']))
		{
			session_destroy();
		}
		// already got some credentials stored?
		elseif(isset($_SESSION['access_token']))
		{
			// We have all the required information to make calls to the Evernote Cloud API 
			// Learn more: dev.evernote.com
			$_SESSION["authKey"] = $_SESSION['access_token']["oauth_token"];

			$user = $userStore->getUser($_SESSION["authKey"]);
    }
    // we're being called back by Twitter
    elseif (isset($_REQUEST['oauth_verifier']))
    {
      $this->oAuth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
      $this->oAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

      $code = $this->oAuth->request('POST', $this->oAuth->url('oauth', ''), array(
        'oauth_verifier' => $_REQUEST['oauth_verifier']
      ));

      if ($code == 200) {
        $_SESSION['access_token'] = $this->oAuth->extract_params($this->oAuth->response['response']);
        unset($_SESSION['oauth']);
        header("Location: {$this->tmh}");
      } else {
        $this->outputError();
      }
    // start the OAuth dance
    } elseif ( isset($_REQUEST['authenticate']) || isset($_REQUEST['authorize']) ) {
      $callback = isset($_REQUEST['oob']) ? 'oob' : $this->tmh;

      $params = array(
        'oauth_callback'     => $callback
      );

      if (isset($_REQUEST['force_write'])) :
        $params['x_auth_access_type'] = 'write';
      elseif (isset($_REQUEST['force_read'])) :
        $params['x_auth_access_type'] = 'read';
      endif;

      $code = $this->oAuth->request('POST', $this->oAuth->url('oauth', ''), $params);

      if ($code == 200) {
        $_SESSION['oauth'] = $this->oAuth->extract_params($this->oAuth->response['response']);

        $authurl = $this->oAuth->url("OAuth.action", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}";
        header("Location: $authurl");
        exit(1);
      } else {
        $this->outputError();
      }
    }
    else
    {

    }
	}

}