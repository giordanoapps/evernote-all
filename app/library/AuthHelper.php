<?php

class AuthHelper
{

 /**
  * Evernote\Client object for authentication via oAuth2.
	*
	* @var Evernote\Client $client
	*/
	protected $client;

 /**
  * string for return url.
	*
	* @var string $returnUrl
	*/
	public $returnUrl;

 /**
  * stdClass with authentication parameters.
	*
	* @var stdClass $auth
	*/
	public $auth;

 /**
  * Constructor.
	*/
	public function AuthHelper()
	{
		$this->initClient();

		if(Input::has('returnUrl'))
		{
			Session::put('returnUrl',Input::get('returnUrl'));

			$this->returnUrl = Input::get('returnUrl');
		}
		else if(Session::has('returnUrl'))
		{
			$this->returnUrl = Session::get('returnUrl');
		}
		else
		{
			$this->returnUrl = 'http://127.0.0.1/tag4share/evernote-all/public/';
		}
	}

 /**
  * Create a instance of Evernote\Client on $this->client.
	*/
	protected function initClient()
	{
		if($this->client == NULL)
		{
			$this->client = new Evernote\Client
			(
				array
				(
					'consumerKey' 	 => OAUTH_CONSUMER_KEY,
					'consumerSecret' => OAUTH_CONSUMER_SECRET,
					'sandbox' 			 => SANDBOX
				)
			);
		}
	}

 /**
  * Returns temporary credentials.
	*
	* @return stdClass $return
	*/
	protected function getTemporaryCredentials()
	{
		$return = new stdClass();

		try
		{
			$client = &$this->client;

			$requestTokenInfo = $client->getRequestToken($this->returnUrl);

			if($requestTokenInfo)
			{
				Session::put
				(
					'requestToken',
					$requestTokenInfo['oauth_token']
				);

				Session::put
				(
					'requestTokenSecret',
					$requestTokenInfo['oauth_token_secret']
				);

				Session::put
				(
					'loginUrl',
					$client->getAuthorizeUrl($requestTokenInfo['oauth_token'])
				);

				$return->status = true;
			}
		}
		catch(OAuthException $e)
		{
			$return->status 		= false;
			$return->exception  = $e;
		}

		return $return;
	}

 /**
  * Returns token credentials.
	*
	* @return stdClass $return
	*/
	protected function getTokenCredentials()
	{
		$return = new stdClass();

		if(Session::has('accessToken'))
		{
			$return->status = true;
		}

		try
		{
			$client = $this->client;

			$accessTokenInfo = $client->getAccessToken
													(
														Session::get('requestToken'),
														Session::get('requestTokenSecret'),
														Input::get('oauth_verifier')
													);

			if($accessTokenInfo)
			{
				Session::put
				(
					'oauthVerifier',
					Input::get('oauth_verifier')
				);

				Session::put
				(
					'accessToken',
					$accessTokenInfo['oauth_token']
				);
				
				$return->status = true;
			}
			else
			{
				$return->status 	 = false;
				$return->exception = null;
			}
		}
		catch(OAuthException $e)
		{
			$return->status 	 = false;
			$return->exception = $e->getMessage();

			var_dump($return);

			die();
		}
		return $return;
	}

 /**
  * Executes the authentication.
	*
	* @return stdClass $return
	*/
	public function doAuth()
	{
		$result = new stdClass();

		if(Session::has('accessToken'))
		{
			$result->TempToken = Session::get('requestToken');
			$result->Verifier	 = Session::get('oauthVerifier');
			$result->Token		 = Session::get('accessToken');
			$result->Url			 = Session::get('loginUrl');

			$this->auth = $result;

			return;
		}
		
		if(Input::has('oauth_verifier')
		&& !Session::has('accessToken'))
			$this->getTokenCredentials();

		if(!Session::has('requestToken'))
		{
			$status = $this->getTemporaryCredentials();

			$result->TempToken = Session::get('requestToken');
			$result->Verifier	 = null;
			$result->Token		 = null;
			$result->Url			 = Session::get('loginUrl');
		}
		else
		{
			$result->TempToken = Session::get('requestToken');
			$result->Verifier	 = Session::get('oauthVerifier');
			$result->Token		 = Session::get('accessToken');
			$result->Url			 = Session::get('loginUrl');
		}

		$this->auth = $result;
	}
}