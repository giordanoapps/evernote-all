<?php

class HomeController extends BaseController {

	private $evernote;
	private $user;

 /**
	* Index route.
	*/
	public function index()
	{
		if(Session::has('user'))
		{
			$this->user = unserialize(Session::get('user'));
		}
		else
		{
			$this->user = null;
		}

		$evernote = new EvernoteHelper($this->user);
		$return = new ReturnModel();

		$return->setUser($this->user);
		$return->setAuth($evernote->auth);
		
		ConversionHelper::toArray($return);

		return $return;
	}

 /**
	* Notes route.
	*/
	public function notes()
	{
		$evernote = new EvernoteHelper();

		if($evernote->auth->token == null)
		{
			return $this->login($evernote);
		}

		return $evernote;
	}
}