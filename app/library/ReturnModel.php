<?php

class ReturnModel
{
	public $user;

	public $auth;

	public $notes;

	public $tags;

	public function setUser($user)
	{
		if($user != null)
		{
			$this->user = new stdClass();

			$this->user->id  			= $user->id;
			$this->user->name  		= $user->name;
			$this->user->username = $user->username;
		}
	}

	public function setAuth($auth)
	{
		if($auth != null)
		{
			$this->auth = new stdClass();

			$this->auth->token = $auth->token;
			$this->auth->url 	 = $auth->url;
		}
	}
}